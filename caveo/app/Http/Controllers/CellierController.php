<?php

namespace App\Http\Controllers;

use App\Models\Bouteille;
use App\Models\Cellier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Classe CellierController
 *
 * Gère toutes les opérations liées aux celliers :
 * - affichage de la liste
 * - création
 * - modification
 * - suppression
 * - recherche AJAX de bouteilles pour l'ajout au cellier
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
     * Supprime un cellier.
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
     * Recherche des bouteilles pour l'ajout au cellier.
     *
     * Cette méthode est utilisée par la modale de recherche.
     * Elle retourne une liste JSON limitée de bouteilles
     * correspondant au texte recherché.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function rechercherBouteilles(Request $request)
    {
        $recherche = trim((string) $request->query('q', ''));

        if ($recherche === '' || mb_strlen($recherche) < 2) {
            return response()->json([]);
        }

        $bouteilles = Bouteille::query()
            ->select('id', 'nom', 'pays', 'format', 'type', 'prix', 'image')
            ->where(function ($query) use ($recherche) {
                $query->where('nom', 'like', '%' . $recherche . '%')
                    ->orWhere('pays', 'like', '%' . $recherche . '%')
                    ->orWhere('type', 'like', '%' . $recherche . '%');
            })
            ->orderBy('nom')
            ->limit(10)
            ->get();

        return response()->json($bouteilles);
    }

    /**
     * Vérifie que le cellier appartient à l'utilisateur connecté.
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
