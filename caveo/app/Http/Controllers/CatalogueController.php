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
    public function index()
    {
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

        // TYPES
        $selectedTypes = request('types', []);

        $types = Bouteille::whereNotNull('type')
            ->distinct()
            ->pluck('type')
            ->sortBy(function ($type) use ($selectedTypes) {
                return [
                    !in_array($type, $selectedTypes),
                    $type
                ];
            })
            ->values();

        // PAYS
        $selectedPays = request('pays', []);

        $pays = Bouteille::whereNotNull('pays')
            ->distinct()
            ->pluck('pays')
            ->sortBy(function ($p) use ($selectedPays) {
                return [
                    !in_array($p, $selectedPays),
                    $p
                ];
            })
            ->values();

        // FORMATS
        $selectedFormats = request('formats', []);

        $formats = Bouteille::whereNotNull('format')
            ->distinct()
            ->orderByRaw('CAST(format AS UNSIGNED) ASC')
            ->pluck('format')
            ->sortBy(function ($f) use ($selectedFormats) {
                return [
                    !in_array($f, $selectedFormats),
                    (int) $f
                ];
            })
            ->values();

        // MILLÉSIMES
        $selectedMillesimes = request('millesimes', []);

        $millesimes = Bouteille::whereNotNull('millesime')
            ->distinct()
            ->pluck('millesime')
            ->sortBy(function ($m) use ($selectedMillesimes) {
                return [
                    !in_array($m, $selectedMillesimes),
                    $m
                ];
            })
            ->values();

        return view('catalogue.index', compact(
            'bouteilles',
            'types',
            'pays',
            'formats',
            'millesimes'
        ));
    }
}