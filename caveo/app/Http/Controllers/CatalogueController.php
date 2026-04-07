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

        // Valeurs uniques pour les filtres, sans null et triées

        // Type : ordre alphabétique
        $types = Bouteille::whereNotNull('type')->distinct()->orderBy('type')->pluck('type');

        // Pays : ordre alphabétique
        $pays = Bouteille::whereNotNull('pays')->distinct()->orderBy('pays')->pluck('pays');

        // Formats / quantités : tri croissant numérique
        // Utilisation de orderByRaw avec CAST(... AS UNSIGNED) pour que les valeurs stockées en texte (ex : "600", "700", "1000") 
        // soient triées correctement comme des nombres et non comme des chaînes
        $formats = Bouteille::whereNotNull('format')->distinct()->orderByRaw('CAST(format AS UNSIGNED) ASC')->pluck('format');

        // Millésime : ordre croissant numérique
        $millesimes = Bouteille::whereNotNull('millesime')->distinct()->orderBy('millesime')->pluck('millesime');

        return view('catalogue.index', compact('bouteilles', 'types', 'pays', 'formats', 'millesimes'));
    }
}
