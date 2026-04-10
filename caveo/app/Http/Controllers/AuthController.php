<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Bouteille;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Affiche le formulaire de connexion.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.create');
    }

    /**
     * Authentifie l'utilisateur et le redirige vers
     * la page détail d'une bouteille.
     *
     * Si aucune bouteille n'est disponible en base de données,
     * l'utilisateur est redirigé vers le catalogue.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'mot_de_passe' => 'required',
        ]);

        $utilisateur = Utilisateur::where('email', $request->email)->first();

        if (!$utilisateur) {
            return back()->withErrors([
                'email' => 'L’adresse courriel est incorrecte.',
            ])->withInput();
        }

        if (!Hash::check($request->mot_de_passe, $utilisateur->mot_de_passe)) {
            return back()->withErrors([
                'mot_de_passe' => 'Le mot de passe est incorrect.',
            ])->withInput();
        }

        Auth::login($utilisateur);

        $bouteille = Bouteille::first();

        if (!$bouteille) {
            return redirect()
                ->route('catalogue.index')
                ->withSuccess('Connexion réussie!');
        }

        return redirect()
            ->route('bouteilles.show', $bouteille->id)
            ->withSuccess('Connexion réussie!');
    }

    /**
     * Déconnecte l'utilisateur et le redirige
     * vers la page de connexion.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy()
    {
        Auth::logout();

        return redirect(route('connexion'));
    }
}
