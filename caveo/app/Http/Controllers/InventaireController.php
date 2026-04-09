<?php

namespace App\Http\Controllers;

use App\Models\Cellier;
use App\Models\Inventaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventaireController extends Controller
{
    /**
     * Ajoute une bouteille à un cellier.
     * Si la bouteille existe déjà dans ce cellier, sa quantité est augmentée.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Cellier $cellier
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Cellier $cellier)
    {
        $this->verifierProprietaireCellier($cellier);

        $validated = $request->validate([
            'id_bouteille' => 'required|exists:bouteilles,id',
            'quantite' => 'nullable|integer|min:1',
            'description' => 'nullable|string',
        ], [
            'id_bouteille.required' => 'La bouteille est obligatoire.',
            'id_bouteille.exists' => 'La bouteille sélectionnée est invalide.',
            'quantite.integer' => 'La quantité doit être un nombre entier.',
            'quantite.min' => 'La quantité doit être d’au moins 1.',
        ]);

        $quantiteAjout = $validated['quantite'] ?? 1;

        $inventaire = Inventaire::where('id_cellier', $cellier->id)
            ->where('id_bouteille', $validated['id_bouteille'])
            ->first();

        if ($inventaire) {
            $inventaire->quantite += $quantiteAjout;

            if (array_key_exists('description', $validated)) {
                $inventaire->description = $validated['description'];
            }

            $inventaire->save();

            return redirect()
                ->route('celliers.show', $cellier)
                ->with('status', 'La quantité de la bouteille a été mise à jour.');
        }

        Inventaire::create([
            'id_cellier' => $cellier->id,
            'id_bouteille' => $validated['id_bouteille'],
            'quantite' => $quantiteAjout,
            'description' => $validated['description'] ?? null,
            'date_ajout' => now(),
        ]);

        return redirect()
            ->route('celliers.show', $cellier)
            ->with('status', 'La bouteille a été ajoutée au cellier.');
    }

    /**
     * Met à jour une ligne d'inventaire existante.
     * Si la quantité reçue est 0, la bouteille est retirée du cellier.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Inventaire $inventaire
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Inventaire $inventaire)
    {
        $this->verifierProprietaireInventaire($inventaire);

        $validated = $request->validate([
            'quantite' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ], [
            'quantite.required' => 'La quantité est obligatoire.',
            'quantite.integer' => 'La quantité doit être un nombre entier.',
            'quantite.min' => 'La quantité ne peut pas être négative.',
        ]);

        if ((int) $validated['quantite'] === 0) {
            $cellierId = $inventaire->id_cellier;
            $inventaire->delete();

            return redirect()
                ->route('celliers.show', $cellierId)
                ->with('status', 'La bouteille a été retirée du cellier.');
        }

        $inventaire->update([
            'quantite' => $validated['quantite'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()
            ->route('celliers.show', $inventaire->id_cellier)
            ->with('status', 'L’inventaire a été mis à jour avec succès.');
    }

    /**
     * Supprime une ligne d'inventaire.
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
     * Vérifie que la ligne d'inventaire appartient à un cellier de l'utilisateur connecté.
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
