<?php

namespace App\Http\Controllers;

use App\Models\Bouteille;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CatalogueController extends Controller
{
     /**
     * Display a listing of the resource.
     */
    public function index(){
        $query = Bouteille::query();

        // Recherche texte
        if ($search = request('recherche')) {
            $query->where('nom', 'like', $search . '%');
        }

        // Filtre par types (OU)
        if ($types = request('types')) {
            $query->whereIn('type', $types);
        }

        // Filtre par pays (OU)
        if ($pays = request('pays')) {
            $query->whereIn('pays', $pays);
        }

        // Filtre par formats (OU)
        if ($formats = request('formats')) {
            $query->whereIn('format', $formats);
        }

        // Filtre par millésimes (OU)
        if ($millesimes = request('millesimes')) {
            $query->whereIn('millesime', $millesimes);
        }

        // Tri
        if ($tri = request('tri_nom')) {
            $query->orderBy('nom', $tri);
        }

        // Pagination
        $bouteilles = $query->paginate(25)->withQueryString();

        // Valeurs uniques pour les filtres, sans null
        $types = Bouteille::whereNotNull('type')->distinct()->pluck('type');
        $pays = Bouteille::whereNotNull('pays')->distinct()->pluck('pays');
        $formats = Bouteille::whereNotNull('format')->distinct()->pluck('format');
        $millesimes = Bouteille::whereNotNull('millesime')->distinct()->pluck('millesime');

        return view('catalogue.index', compact('bouteilles', 'types', 'pays', 'formats', 'millesimes'));
    }
}
