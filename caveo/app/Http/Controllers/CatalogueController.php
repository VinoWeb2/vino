<?php

namespace App\Http\Controllers;

use App\Models\Bouteille;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CatalogueController extends Controller
{
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

        // Filtre par formats (CHOIX UNIQUE)
        if ($formats = request('formats')) {
            // Si un format est sélectionné (une seule valeur)
            if (!empty($formats)) {
                $query->where('format', $formats);
            }
        }

        // Filtre par millésimes (CHOIX UNIQUE)
        if ($millesimes = request('millesimes')) {
            // Si un millésime est sélectionné (une seule valeur)
            if (!empty($millesimes)) {
                $query->where('millesime', $millesimes);
            }
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

        // FORMATS (choix unique)
        $selectedFormats = request('formats', null);
        $formats = Bouteille::whereNotNull('format')
            ->distinct()
            ->orderByRaw('CAST(format AS UNSIGNED) ASC')
            ->pluck('format')
            ->sortBy(function ($f) use ($selectedFormats) {
                return [
                    $f != $selectedFormats,
                    (int) $f
                ];
            })
            ->values();

        // MILLÉSIMES (choix unique)
        $selectedMillesimes = request('millesimes', null);
        $millesimes = Bouteille::whereNotNull('millesime')
            ->distinct()
            ->pluck('millesime')
            ->sortBy(function ($m) use ($selectedMillesimes) {
                return [
                    $m != $selectedMillesimes,
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