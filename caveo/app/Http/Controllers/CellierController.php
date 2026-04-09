<?php

namespace App\Http\Controllers;

use App\Models\Cellier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CellierController extends Controller
{
    /**
     * Affiche la liste des celliers appartenant à l'utilisateur connecté.
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
     * Enregistre un nouveau cellier pour l'utilisateur connecté.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $utilisateur = Auth::user();

        $validated = $request->validate([
            'nom' => 'required|string|max:75|unique:celliers,nom,NULL,id,id_utilisateur,' . $utilisateur->id,
            'description' => 'nullable|string',
            'emplacement' => 'nullable|string|max:55',
        ], [
            'nom.required' => 'Le nom du cellier est obligatoire.',
            'nom.max' => 'Le nom du cellier ne peut pas dépasser 75 caractères.',
            'nom.unique' => 'Vous avez déjà un cellier portant ce nom.',
            'emplacement.max' => 'L’emplacement ne peut pas dépasser 55 caractères.',
        ]);

        Cellier::create([
            'nom' => $validated['nom'],
            'id_utilisateur' => $utilisateur->id,
            'description' => $validated['description'] ?? null,
            'emplacement' => $validated['emplacement'] ?? null,
        ]);

        return redirect()
            ->route('celliers.index')
            ->with('status', 'Le cellier a été créé avec succès.');
    }

    /**
     * Affiche le détail d'un cellier avec son inventaire
     * et la liste des bouteilles disponibles à l'ajout.
     *
     * @param \App\Models\Cellier $cellier
     * @return \Illuminate\View\View
     */
    public function show(Cellier $cellier)
    {
        $this->verifierProprietaire($cellier);

        $cellier->load('inventaires.bouteille');

        $bouteilles = \App\Models\Bouteille::orderBy('nom')->get();

        return view('celliers.show', compact('cellier', 'bouteilles'));
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
     * Met à jour les informations d'un cellier existant.
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
            'description' => 'nullable|string',
            'emplacement' => 'nullable|string|max:55',
        ], [
            'nom.required' => 'Le nom du cellier est obligatoire.',
            'nom.max' => 'Le nom du cellier ne peut pas dépasser 75 caractères.',
            'nom.unique' => 'Vous avez déjà un cellier portant ce nom.',
            'emplacement.max' => 'L’emplacement ne peut pas dépasser 55 caractères.',
        ]);

        $cellier->update([
            'nom' => $validated['nom'],
            'description' => $validated['description'] ?? null,
            'emplacement' => $validated['emplacement'] ?? null,
        ]);

        return redirect()
            ->route('celliers.show', $cellier)
            ->with('status', 'Le cellier a été modifié avec succès.');
    }

    /**
     * Supprime un cellier appartenant à l'utilisateur connecté.
     *
     * @param \App\Models\Cellier $cellier
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Cellier $cellier)
    {
        $this->verifierProprietaire($cellier);

        $cellier->delete();

        return redirect()
            ->route('celliers.index')
            ->with('status', 'Le cellier a été supprimé avec succès.');
    }

    /**
     * Vérifie que le cellier appartient bien à l'utilisateur connecté.
     *
     * @param \App\Models\Cellier $cellier
     * @return void
     */
    private function verifierProprietaire(Cellier $cellier): void
    {
        $utilisateur = Auth::user();

        abort_if(
            $cellier->id_utilisateur !== $utilisateur->id,
            403,
            'Accès non autorisé à ce cellier.'
        );
    }
}
