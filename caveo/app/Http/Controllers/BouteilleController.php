<?php

namespace App\Http\Controllers;

use App\Models\Bouteille;
use App\Models\Inventaire;
use App\Models\Cellier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Contrôleur des bouteilles.
 *
 * Gère :
 * - l'affichage des bouteilles
 * - l'ajout manuel de bouteilles non listées
 * - l'intégration avec les celliers
 */
class BouteilleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Bouteille $bouteille, Request $request)
    {
        $source = $request->query('source', 'catalogue');
        $inventaire = null;

        if ($source === 'cellier' && $request->filled('inventaire')) {
            $inventaire = Inventaire::with('cellier')
                ->where('id', $request->query('inventaire'))
                ->where('id_bouteille', $bouteille->id)
                ->first();

            if (!$inventaire || !$inventaire->cellier || $inventaire->cellier->id_utilisateur !== auth()->id()) {
                abort(403, 'Accès non autorisé.');
            }
        }

        return view('bouteilles.show', [
            'bouteille' => $bouteille,
            'source' => $source,
            'inventaire' => $inventaire,
        ]);
    }

    /**
     * Affiche le formulaire permettant d'ajouter une bouteille non listée
     * dans un cellier spécifique.
     *
     * Cette méthode est utilisée lorsque l'utilisateur souhaite ajouter
     * manuellement une bouteille qui n'existe pas dans le catalogue.
     *
     * @param \App\Models\Cellier $cellier
     * @return \Illuminate\View\View
     */
    public function createFromCellier(Cellier $cellier)
    {
        return view('bouteilles.create-from-cellier', compact('cellier'));
    }

    /**
     * Enregistre une nouvelle bouteille non listée et l'ajoute
     * automatiquement à l'inventaire du cellier sélectionné.
     *
     * Étapes :
     * - Vérifie que le cellier appartient à l'utilisateur connecté
     * - Valide les données du formulaire
     * - Enregistre l'image si elle est fournie
     * - Crée une nouvelle bouteille (non SAQ)
     * - Ajoute la bouteille dans l'inventaire du cellier
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Cellier $cellier
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeFromCellier(Request $request, Cellier $cellier)
    {
        $utilisateur = auth()->user();

        abort_if(
            $cellier->id_utilisateur !== $utilisateur->id,
            403,
            'Accès non autorisé à ce cellier.'
        );

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'type' => 'nullable|string|max:100',
            'pays' => 'nullable|string|max:100',
            'format' => 'nullable|integer|min:1|max:9999',
            'millesime' => 'nullable|integer|min:1000|max:9999',
            'cepage' => 'nullable|string|max:255',
            'prix' => 'nullable|numeric|min:0|max:99999.99',
            'quantite' => 'required|integer|min:0|max:999',
            'description' => 'nullable|string|max:2000',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'nom.required' => 'Le nom de la bouteille est obligatoire.',
            'quantite.required' => 'La quantité est obligatoire.',
            'quantite.min' => 'La quantité ne peut pas être négative.',
            'image.image' => 'Le fichier doit être une image valide.',
            'image.mimes' => 'L’image doit être au format jpg, jpeg, png ou webp.',
            'image.max' => 'L’image ne peut pas dépasser 2 Mo.',
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('bouteilles', 'public');
        }

        $bouteille = Bouteille::create([
            'nom' => $validated['nom'],
            'type' => $validated['type'] ?? null,
            'pays' => $validated['pays'] ?? null,
            'format' => $validated['format'] ?? null,
            'millesime' => $validated['millesime'] ?? null,
            'cepage' => $validated['cepage'] ?? null,
            'prix' => $validated['prix'] ?? null,
            'description' => $validated['description'] ?? null,
            'image' => $imagePath,
            'est_saq' => false,
        ]);

        Inventaire::create([
            'id_cellier' => $cellier->id,
            'id_bouteille' => $bouteille->id,
            'quantite' => $validated['quantite'],
            'date_ajout' => now(),
        ]);

        return redirect()
            ->route('celliers.show', $cellier)
            ->with('status', 'La bouteille a été ajoutée au cellier avec succès.');
    }

    /**
     * Affiche le formulaire de modification d'une bouteille non listée
     * associée à un cellier spécifique.
     *
     * Cette méthode vérifie :
     * - que le cellier appartient à l'utilisateur connecté ;
     * - que la bouteille est bien présente dans ce cellier ;
     * - que la bouteille n'est pas une bouteille SAQ importée.
     *
     * @param \App\Models\Cellier $cellier
     * @param \App\Models\Bouteille $bouteille
     * @return \Illuminate\View\View
     */
    public function editFromCellier(Cellier $cellier, Bouteille $bouteille)
    {
        $utilisateur = Auth::user();

        abort_if(
            $cellier->id_utilisateur !== $utilisateur->id,
            403,
            'Accès non autorisé à ce cellier.'
        );

        $inventaire = Inventaire::where('id_cellier', $cellier->id)
            ->where('id_bouteille', $bouteille->id)
            ->first();

        abort_if(
            !$inventaire,
            404,
            'Cette bouteille n’est pas associée à ce cellier.'
        );

        abort_if(
            $bouteille->est_saq,
            403,
            'Les bouteilles importées ne peuvent pas être modifiées manuellement.'
        );

        $quantite = $inventaire->quantite;

        return view('bouteilles.edit-from-cellier', compact('cellier', 'bouteille', 'quantite'));
    }

    /**
     * Met à jour une bouteille non listée associée à un cellier spécifique.
     *
     * Cette méthode :
     * - vérifie que le cellier appartient à l'utilisateur connecté ;
     * - vérifie que la bouteille est bien dans ce cellier ;
     * - empêche la modification d'une bouteille SAQ importée ;
     * - remplace l'image si une nouvelle image est fournie ;
     * - met à jour les informations de la bouteille ;
     * - met à jour la quantité dans l'inventaire du cellier.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Cellier $cellier
     * @param \App\Models\Bouteille $bouteille
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateFromCellier(Request $request, Cellier $cellier, Bouteille $bouteille)
    {
        $utilisateur = Auth::user();

        abort_if(
            $cellier->id_utilisateur !== $utilisateur->id,
            403,
            'Accès non autorisé à ce cellier.'
        );

        $inventaire = Inventaire::where('id_cellier', $cellier->id)
            ->where('id_bouteille', $bouteille->id)
            ->first();

        abort_if(
            !$inventaire,
            404,
            'Cette bouteille n’est pas associée à ce cellier.'
        );

        abort_if(
            $bouteille->est_saq,
            403,
            'Les bouteilles importées ne peuvent pas être modifiées manuellement.'
        );

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'type' => 'nullable|string|max:100',
            'pays' => 'nullable|string|max:100',
            'format' => 'nullable|integer|min:1|max:9999',
            'millesime' => 'nullable|integer|min:1000|max:9999',
            'cepage' => 'nullable|string|max:255',
            'prix' => 'nullable|numeric|min:0|max:99999.99',
            'quantite' => 'required|integer|min:0|max:999',
            'description' => 'nullable|string|max:2000',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'nom.required' => 'Le nom de la bouteille est obligatoire.',
            'nom.max' => 'Le nom ne peut pas dépasser 255 caractères.',
            'type.max' => 'Le type ne peut pas dépasser 100 caractères.',
            'pays.max' => 'Le pays ne peut pas dépasser 100 caractères.',
            'format.integer' => 'Le format doit être un nombre entier.',
            'format.min' => 'Le format doit être supérieur à 0.',
            'format.max' => 'Le format ne peut pas dépasser 9999 ml.',
            'millesime.integer' => 'Le millésime doit être un nombre entier.',
            'millesime.min' => 'Le millésime doit contenir 4 chiffres valides.',
            'millesime.max' => 'Le millésime doit contenir 4 chiffres valides.',
            'cepage.max' => 'Le cépage ne peut pas dépasser 255 caractères.',
            'prix.numeric' => 'Le prix doit être un nombre valide.',
            'prix.min' => 'Le prix ne peut pas être négatif.',
            'prix.max' => 'Le prix est trop élevé.',
            'quantite.required' => 'La quantité est obligatoire.',
            'quantite.integer' => 'La quantité doit être un nombre entier.',
            'quantite.min' => 'La quantité ne peut pas être négative.',
            'quantite.max' => 'La quantité ne peut pas dépasser 999.',
            'description.max' => 'La description ne peut pas dépasser 2000 caractères.',
            'image.image' => 'Le fichier doit être une image valide.',
            'image.mimes' => 'L’image doit être au format jpg, jpeg, png ou webp.',
            'image.max' => 'L’image ne peut pas dépasser 2 Mo.',
        ]);

        $imagePath = $bouteille->image;

        if ($request->hasFile('image')) {
            if ($bouteille->image && Storage::disk('public')->exists($bouteille->image)) {
                Storage::disk('public')->delete($bouteille->image);
            }

            $imagePath = $request->file('image')->store('bouteilles', 'public');
        }

        $bouteille->update([
            'nom' => $validated['nom'],
            'type' => $validated['type'] ?? null,
            'pays' => $validated['pays'] ?? null,
            'format' => $validated['format'] ?? null,
            'millesime' => $validated['millesime'] ?? null,
            'cepage' => $validated['cepage'] ?? null,
            'prix' => $validated['prix'] ?? null,
            'description' => $validated['description'] ?? null,
            'image' => $imagePath,
        ]);

        $inventaire->update([
            'quantite' => $validated['quantite'],
        ]);

        return redirect()
            ->route('celliers.show', $cellier)
            ->with('status', 'La bouteille a été modifiée avec succès.');
    }

    /**
     * Supprime une bouteille non listée associée à un cellier spécifique.
     *
     * Cette méthode :
     * - vérifie que le cellier appartient à l'utilisateur connecté ;
     * - vérifie que la bouteille est bien liée à ce cellier ;
     * - empêche la suppression d'une bouteille SAQ importée ;
     * - supprime d'abord l'entrée d'inventaire ;
     * - supprime ensuite la bouteille si elle n'est plus utilisée
     *   dans aucun autre cellier.
     *
     * @param \App\Models\Cellier $cellier
     * @param \App\Models\Bouteille $bouteille
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyFromCellier(Cellier $cellier, Bouteille $bouteille)
    {
        $utilisateur = Auth::user();

        abort_if(
            $cellier->id_utilisateur !== $utilisateur->id,
            403,
            'Accès non autorisé à ce cellier.'
        );

        $inventaire = Inventaire::where('id_cellier', $cellier->id)
            ->where('id_bouteille', $bouteille->id)
            ->first();

        abort_if(
            !$inventaire,
            404,
            'Cette bouteille n’est pas associée à ce cellier.'
        );

        abort_if(
            $bouteille->est_saq,
            403,
            'Les bouteilles importées ne peuvent pas être supprimées manuellement.'
        );

        $inventaire->delete();

        $encoreUtilisee = Inventaire::where('id_bouteille', $bouteille->id)->exists();

        if (!$encoreUtilisee) {
            $bouteille->delete();
        }

        return redirect()
            ->route('celliers.show', $cellier)
            ->with('status', 'La bouteille a été supprimée avec succès.');
    }
}
