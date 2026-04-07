<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('auth.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'mot_de_passe' => 'required'
        ]);

        $utilisateur = Utilisateur::where('email', $request->email)->first();

        if (!$utilisateur) {
            return back()->withErrors([
                'email' => 'L’adresse courriel est incorrect'
            ])->withInput();
        }

        if (!Hash::check($request->mot_de_passe, $utilisateur->mot_de_passe)){
            return back()->withErrors([
                'mot_de_passe' => 'Le mot de passe est incorrect'
            ])->withInput();
        }

        Auth::login($utilisateur);

        return redirect()->intended('/')->withSuccess('Connexion réussie!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy()
    {
        Auth::logout();
        return redirect(route('connexion'));
    }
}
