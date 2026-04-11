<?php

namespace App\Http\Controllers;

use App\Models\Cellier;
use App\Models\Inventaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Classe InventaireController
 *
 * Gère les opérations liées à l'inventaire d'un cellier :
 * - ajout d'une bouteille
 * - mise à jour de la quantité
 * - mise à jour rapide via boutons + / -
 * - suppression
 */
class InventaireController extends Controller
{
    /**
     * Ajoute une bouteille à un cellier.
     * Si elle existe déjà, on augmente la quantité.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Cellier $cellier
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Cellier $cellier)
    {
        $this->verifierProprietaireCellier($cellier);

        $validated = $request->validate([
            'id_bouteille' => 'required|integer|exists:bouteilles,id',
            'quantite' => 'required|integer|min:1|max:999',
        ], [
            'id_bouteille.required' => 'La bouteille est obligatoire.',
            'id_bouteille.exists' => 'La bouteille sélectionnée est invalide.',
            'quantite.required' => 'La quantité est obligatoire.',
            'quantite.integer' => 'La quantité doit être un nombre entier.',
            'quantite.min' => 'La quantité doit être d’au moins 1.',
            'quantite.max' => 'La quantité ne peut pas dépasser 999.',
        ]);

        $inventaire = Inventaire::where('id_cellier', $cellier->id)
            ->where('id_bouteille', $validated['id_bouteille'])
            ->first();

        if ($inventaire) {
            $inventaire->quantite += $validated['quantite'];
            $inventaire->save();

            return redirect()
                ->route('celliers.show', $cellier)
                ->with('status', 'La quantité de la bouteille a été mise à jour.');
        }

        Inventaire::create([
            'id_cellier' => $cellier->id,
            'id_bouteille' => $validated['id_bouteille'],
            'quantite' => $validated['quantite'],
            'date_ajout' => now(),
        ]);

        return redirect()
            ->route('celliers.show', $cellier)
            ->with('status', 'La bouteille a été ajoutée au cellier.');
    }

    /**
     * Met à jour la quantité d'une bouteille dans le cellier.
     *
     * Une quantité de 0 est autorisée afin d'indiquer
     * qu'il n'y a plus de stock.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Inventaire $inventaire
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Inventaire $inventaire)
    {
        $this->verifierProprietaireInventaire($inventaire);

        $validated = $request->validate([
            'quantite' => 'required|integer|min:0|max:999',
        ], [
            'quantite.required' => 'La quantité est obligatoire.',
            'quantite.integer' => 'La quantité doit être un nombre entier.',
            'quantite.min' => 'La quantité ne peut pas être négative.',
            'quantite.max' => 'La quantité ne peut pas dépasser 999.',
        ]);

        $inventaire->update([
            'quantite' => $validated['quantite'],
        ]);

        return redirect()
            ->route('celliers.show', $inventaire->id_cellier)
            ->with('status', 'La quantité a été mise à jour.');
    }

    /**
     * Met à jour rapidement la quantité d'une bouteille
     * à l'aide des boutons + et -.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Inventaire $inventaire
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateQuantite(Request $request, Inventaire $inventaire)
    {
        $this->verifierProprietaireInventaire($inventaire);

        $validated = $request->validate([
            'quantite' => 'required|integer|min:0|max:999',
        ], [
            'quantite.required' => 'La quantité est obligatoire.',
            'quantite.integer' => 'La quantité doit être un nombre entier.',
            'quantite.min' => 'La quantité ne peut pas être négative.',
            'quantite.max' => 'La quantité ne peut pas dépasser 999.',
        ]);

        $inventaire->update([
            'quantite' => $validated['quantite'],
        ]);

        return redirect()
            ->route('celliers.show', $inventaire->id_cellier)
            ->with('status', 'La quantité a été mise à jour.');
    }

    /**
     * Supprime une bouteille du cellier.
     *
     * @param \App\Models\Inventaire $inventaire
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Inventaire $inventaire)
    {
        $this->verifierProprietaireInventaire($inventaire);

        $cellierId = $inventaire->id_cellier;

        $inventaire->delete();

        return redirect()
            ->route('celliers.show', $cellierId)
            ->with('status', 'La bouteille a été supprimée du cellier.');
    }

    /**
     * Vérifie que le cellier appartient à l'utilisateur connecté.
     *
     * @param \App\Models\Cellier $cellier
     * @return void
     */
    private function verifierProprietaireCellier(Cellier $cellier): void
    {
        $utilisateur = Auth::user();

        abort_if(
            $cellier->id_utilisateur !== $utilisateur->id,
            403,
            'Accès non autorisé à ce cellier.'
        );
    }

    /**
     * Vérifie que l'inventaire appartient à un cellier de l'utilisateur.
     *
     * @param \App\Models\Inventaire $inventaire
     * @return void
     */
    private function verifierProprietaireInventaire(Inventaire $inventaire): void
    {
        $utilisateur = Auth::user();

        $inventaire->load('cellier');

        abort_if(
            !$inventaire->cellier || $inventaire->cellier->id_utilisateur !== $utilisateur->id,
            403,
            'Accès non autorisé à cet inventaire.'
        );
    }
}
