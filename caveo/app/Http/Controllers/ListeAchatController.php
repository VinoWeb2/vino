<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ListeAchat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ListeAchatController extends Controller
{
    public function index(){
        return view('liste-achat.index');
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
}
