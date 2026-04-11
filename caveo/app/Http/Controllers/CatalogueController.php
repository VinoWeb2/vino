<?php

namespace App\Http\Controllers;

use App\Models\Bouteille;
use App\Models\Cellier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Contrôleur du catalogue.
 *
 * Gère :
 * - l'affichage paginé des bouteilles ;
 * - la recherche textuelle ;
 * - les filtres (type, pays, format, millésime) ;
 * - le tri par nom ;
 * - la récupération des celliers de l'utilisateur connecté
 *   pour l'ajout au cellier depuis le catalogue.
 */
class CatalogueController extends Controller
{
    /**
     * Affiche le catalogue des bouteilles.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Bouteille::query();

        /**
         * Recherche textuelle.
         */
        if ($request->filled('recherche')) {
            $recherche = trim($request->recherche);

            $query->where(function ($q) use ($recherche) {
                $q->where('nom', 'like', $recherche . '%');
            });
        }

        /**
         * Filtres.
         */
        if ($request->filled('types')) {
            $query->whereIn('type', $request->types);
        }

        if ($request->filled('pays')) {
            $query->whereIn('pays', $request->pays);
        }

        if ($request->filled('formats')) {
            $query->where('format', $request->formats);
        }

        if ($request->filled('millesimes')) {
            $query->where('millesime', $request->millesimes);
        }

        /**
         * Tri par nom.
         */
        if ($request->tri_nom === 'asc') {
            $query->orderBy('nom', 'asc');
        } elseif ($request->tri_nom === 'desc') {
            $query->orderBy('nom', 'desc');
        }

        /**
         * Pagination.
         */
        /** @var \Illuminate\Pagination\LengthAwarePaginator $bouteilles */
        $bouteilles = $query->paginate(25)->withQueryString();

        /**
         * Valeurs distinctes pour les filtres.
         */
        $types = Bouteille::whereNotNull('type')
            ->where('type', '!=', '')
            ->distinct()
            ->orderBy('type')
            ->pluck('type');

        $pays = Bouteille::whereNotNull('pays')
            ->where('pays', '!=', '')
            ->distinct()
            ->orderBy('pays')
            ->pluck('pays');

        $formats = Bouteille::whereNotNull('format')
            ->distinct()
            ->orderBy('format')
            ->pluck('format');

        $millesimes = Bouteille::whereNotNull('millesime')
            ->distinct()
            ->orderBy('millesime', 'desc')
            ->pluck('millesime');

        /**
         * Celliers de l'utilisateur connecté.
         */
        $celliers = collect();

        if (Auth::check()) {
            $celliers = Cellier::where('id_utilisateur', Auth::id())
                ->orderBy('nom')
                ->get();
        }

        return view('catalogue.index', compact(
            'bouteilles',
            'types',
            'pays',
            'formats',
            'millesimes',
            'celliers'
        ));
    }
}
