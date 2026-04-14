<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Utilisateur;
use Illuminate\Http\Request;

/**
 * Contrôleur de gestion des utilisateurs (administration).
 *
 * Gère :
 * - l'affichage paginé des utilisateurs ;
 * - la recherche textuelle par nom, prénom ou email ;
 * - le filtre par rôle ;
 * - le tri alphabétique par défaut.
 */
class AdminUtilisateurController extends Controller
{
    /**
     * Affiche la liste des utilisateurs du système.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Utilisateur::with('role');

        /**
         * Recherche textuelle.
         */
        if ($request->filled('recherche')) {
            $recherche = trim($request->recherche);

            $query->where(function ($q) use ($recherche) {
                $q->where('nom', 'like', '%' . $recherche . '%')
                  ->orWhere('prenom', 'like', '%' . $recherche . '%')
                  ->orWhere('email', 'like', '%' . $recherche . '%');
            });
        }

        /**
         * Filtre par rôle.
         */
        if ($request->filled('role_id')) {
            $query->where('id_role', $request->role_id);
        }

        /**
         * Tri par défaut : ordre alphabétique.
         */
        $query->orderBy('nom', 'asc')->orderBy('prenom', 'asc');

        /**
         * Pagination.
         */
        /** @var \Illuminate\Pagination\LengthAwarePaginator $utilisateurs */
        $utilisateurs = $query->paginate(15)->withQueryString();

        /**
         * Liste des rôles disponibles pour le filtre.
         */
        $roles = Role::orderBy('nom')->get();

        return view('admin.utilisateurs.utilisateurs', compact(
            'utilisateurs',
            'roles'
        ));
    }
}
