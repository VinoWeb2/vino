<?php

namespace App\Http\Controllers;

use App\Models\Bouteille;
use App\Models\Inventaire;
use Illuminate\Http\Request;

class BouteilleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Bouteille $bouteille, Request $request)
    {
        $source = $request->query('source', 'catalogue');
        $inventaire = null;

        if ($source === 'cellier' && $request->filled('inventaire')) {
            $inventaire = Inventaire::with('cellier')
                ->where('id', $request->query('inventaire'))
                ->where('id_bouteille', $bouteille->id)
                ->first();

            if (!$inventaire || !$inventaire->cellier || $inventaire->cellier->id_utilisateur !== auth()->id()) {
                abort(403, 'Accès non autorisé.');
            }
        }

        return view('bouteilles.show', [
            'bouteille' => $bouteille,
            'source' => $source,
            'inventaire' => $inventaire,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bouteille $bouteille)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bouteille $bouteille)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bouteille $bouteille)
    {
        //
    }
}
