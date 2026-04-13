<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ListeAchat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ListeAchatController extends Controller
{
    public function index(){
        /**
         * Liste d'achat de l'utilisateur connecté avec ses bouteilles.
         */
        $listes = collect();

        if (Auth::check()) {
            $listes = ListeAchat::with('bouteilles') 
                ->where('id_utilisateur', Auth::id())
                ->orderBy('nom')
                ->get();
        }

        return view('liste-achat.index', ['listes' => $listes]);
    }

    public function create(){
        return view('liste-achat.create');
    }

    public function store(Request $request)
    {
        $utilisateur = Auth::user();

        $validated = $request->validate([
            'nom' => 'required|string|max:75|',
            'description' => 'nullable|string|max:2000',
        ]);

        ListeAchat::create([
            'nom' => $validated['nom'],
            'id_utilisateur' => $utilisateur->id,
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()
            ->route('achat.index');
    }

    public function edit(ListeAchat $liste)
    {
        $this->verifierProprietaire($liste);

        return view('liste-achat.edit', compact('liste'));
    }

    public function update(Request $request, ListeAchat $liste)
    {
        $this->verifierProprietaire($liste);

        $validated = $request->validate([
            'nom' => 'required|string|max:75',
            'description' => 'nullable|string|max:2000',
        ]);

        $liste->update([
            'nom' => $validated['nom'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()
            ->route('achat.index')
            ->with('status', 'La liste d\'achat a été modifié avec succès.');
    }

    public function destroy(ListeAchat $liste)
    {
        $this->verifierProprietaire($liste);

        $liste->delete();

        return redirect()
            ->route('achat.index')
            ->with('status', 'La liste d\'achat a été supprimé avec succès.');
    }

    private function verifierProprietaire(ListeAchat $liste): void
    {
        $utilisateur = Auth::user();

        abort_if(
            $liste->id_utilisateur !== $utilisateur->id,
            403,
            'Accès non autorisé à cette liste d\'achat.'
        );
    }

    public function addBouteille(Request $request, ListeAchat $liste)
    {
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

        $bouteilleExistante = $liste->bouteilles()
            ->where('id_bouteille', $validated['id_bouteille'])
            ->first();

        if ($bouteilleExistante) {
            $nouvelleQuantite = $bouteilleExistante->pivot->quantite + $validated['quantite'];

            $liste->bouteilles()->updateExistingPivot(
                $validated['id_bouteille'],
                ['quantite' => $nouvelleQuantite]
            );

            return back()->with('status', 'La quantité a été mise à jour.');
        }

        $liste->bouteilles()->attach(
            $validated['id_bouteille'],
            ['quantite' => $validated['quantite']]
        );

        return back();
    }
}
