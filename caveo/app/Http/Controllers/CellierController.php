<?php

namespace App\Http\Controllers;

use App\Models\Cellier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Classe CellierController
 *
 * Gère toutes les opérations liées aux celliers :
 * - affichage de la liste
 * - création
 * - modification
 * - suppression
 */
class CellierController extends Controller
{
    /**
     * Affiche la liste des celliers de l'utilisateur connecté.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $utilisateur = Auth::user();

        $celliers = Cellier::withCount('inventaires')
            ->where('id_utilisateur', $utilisateur->id)
            ->orderBy('nom')
            ->get();

        return view('celliers.index', compact('celliers'));
    }

    /**
     * Affiche le formulaire de création d'un cellier.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('celliers.create');
    }

    /**
     * Enregistre un nouveau cellier.
     *
     * Cette méthode :
     * - valide les champs du formulaire ;
     * - enregistre l'image si elle est fournie ;
     * - crée le cellier pour l'utilisateur connecté.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $utilisateur = Auth::user();

        $validated = $request->validate([
            'nom' => 'required|string|max:75|unique:celliers,nom,NULL,id,id_utilisateur,' . $utilisateur->id,
            'description' => 'nullable|string|max:2000',
            'emplacement' => 'nullable|string|max:55',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'nom.required' => 'Le nom du cellier est obligatoire.',
            'nom.max' => 'Le nom du cellier ne peut pas dépasser 75 caractères.',
            'description.max' => 'La description ne peut pas dépasser 2000 caractères.',
            'emplacement.max' => 'L’emplacement ne peut pas dépasser 55 caractères.',
            'image.image' => 'Le fichier doit être une image valide.',
            'image.mimes' => 'L’image doit être au format jpg, jpeg, png ou webp.',
            'image.max' => 'L’image ne peut pas dépasser 2 Mo.',
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('celliers', 'public');
        }

        Cellier::create([
            'nom' => $validated['nom'],
            'id_utilisateur' => $utilisateur->id,
            'description' => $validated['description'] ?? null,
            'emplacement' => $validated['emplacement'] ?? null,
            'image' => $imagePath,
        ]);

        return redirect()
            ->route('celliers.index')
            ->with('status', 'Le cellier a été créé avec succès.');
    }

    /**
     * Affiche un cellier avec son inventaire.
     *
     * @param \App\Models\Cellier $cellier
     * @return \Illuminate\View\View
     */
    public function show(Cellier $cellier)
    {
        $this->verifierProprietaire($cellier);

        $cellier->load('inventaires.bouteille');

        return view('celliers.show', compact('cellier'));
    }

    /**
     * Affiche le formulaire de modification d'un cellier.
     *
     * @param \App\Models\Cellier $cellier
     * @return \Illuminate\View\View
     */
    public function edit(Cellier $cellier)
    {
        $this->verifierProprietaire($cellier);

        return view('celliers.edit', compact('cellier'));
    }

    /**
     * Met à jour un cellier existant.
     *
     * Cette méthode :
     * - valide les champs du formulaire ;
     * - remplace l'image si une nouvelle image est fournie ;
     * - supprime l'ancienne image du storage si nécessaire ;
     * - met à jour les données du cellier.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Cellier $cellier
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Cellier $cellier)
    {
        $this->verifierProprietaire($cellier);

        $utilisateur = Auth::user();

        $validated = $request->validate([
            'nom' => 'required|string|max:75|unique:celliers,nom,' . $cellier->id . ',id,id_utilisateur,' . $utilisateur->id,
            'description' => 'nullable|string|max:2000',
            'emplacement' => 'nullable|string|max:55',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'nom.required' => 'Le nom du cellier est obligatoire.',
            'nom.max' => 'Le nom du cellier ne peut pas dépasser 75 caractères.',
            'description.max' => 'La description ne peut pas dépasser 2000 caractères.',
            'emplacement.max' => 'L’emplacement ne peut pas dépasser 55 caractères.',
            'image.image' => 'Le fichier doit être une image valide.',
            'image.mimes' => 'L’image doit être au format jpg, jpeg, png ou webp.',
            'image.max' => 'L’image ne peut pas dépasser 2 Mo.',
        ]);

        $imagePath = $cellier->image;

        if ($request->hasFile('image')) {
            if ($cellier->image && Storage::disk('public')->exists($cellier->image)) {
                Storage::disk('public')->delete($cellier->image);
            }

            $imagePath = $request->file('image')->store('celliers', 'public');
        }

        $cellier->update([
            'nom' => $validated['nom'],
            'description' => $validated['description'] ?? null,
            'emplacement' => $validated['emplacement'] ?? null,
            'image' => $imagePath,
        ]);

        return redirect()
            ->route('celliers.show', $cellier)
            ->with('status', 'Le cellier a été modifié avec succès.');
    }

    /**
     * Supprime un cellier.
     *
     * Cette méthode :
     * - vérifie que le cellier appartient à l'utilisateur connecté ;
     * - supprime l'image associée si elle existe ;
     * - supprime le cellier.
     *
     * @param \App\Models\Cellier $cellier
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Cellier $cellier)
    {
        $this->verifierProprietaire($cellier);

        if ($cellier->image && Storage::disk('public')->exists($cellier->image)) {
            Storage::disk('public')->delete($cellier->image);
        }

        $cellier->delete();

        return redirect()
            ->route('celliers.index')
            ->with('status', 'Le cellier a été supprimé avec succès.');
    }

    /**
     * Vérifie que le cellier appartient à l'utilisateur connecté.
     *
     * @param \App\Models\Cellier $cellier
     * @return void
     */
    private function verifierProprietaire(Cellier $cellier): void
    {
        abort_if(
            $cellier->id_utilisateur !== Auth::id(),
            403
        );
    }
}
