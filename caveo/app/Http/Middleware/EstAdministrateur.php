<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware vérifiant que l'utilisateur authentifié
 * possède le rôle « admin ».
 *
 * Doit être utilisé après le middleware `auth`.
 */
class EstAdministrateur
{
    /**
     * Traite la requête entrante.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): \Symfony\Component\HttpFoundation\Response $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $utilisateur = $request->user();

        if (! $utilisateur || optional($utilisateur->role)->nom !== 'admin') {
            abort(403, 'Accès réservé aux administrateurs.');
        }

        return $next($request);
    }
}
