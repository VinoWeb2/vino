<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : url('/');
    }

    /**
     * Traite la requête après authentification.
     *
     * Redirige l'administrateur vers la gestion des utilisateurs
     * s'il tente d'accéder à une route non-admin.
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $response = parent::handle($request, $next, ...$guards);

        $utilisateur = $request->user();

        if ($utilisateur
            && optional($utilisateur->role)->nom === 'admin'
            && ! $request->routeIs('admin.*')
            && ! $request->routeIs('accueil')
            && ! $request->routeIs('deconnexion')
        ) {
            return redirect()->route('admin.utilisateurs.index');
        }

        return $response;
    }
}
