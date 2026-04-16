<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUtilisateurRequest;
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
 * - le tri alphabétique par défaut ;
 * - la modification des informations utilisateur.
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

    /**
     * Affiche le formulaire de modification d'un utilisateur.
     *
     * @param \App\Models\Utilisateur $utilisateur
     * @return \Illuminate\View\View
     */
    public function edit(Utilisateur $utilisateur)
    {
        /**
         * Liste des rôles disponibles pour le formulaire.
         */
        $roles = Role::orderBy('nom')->get();

        return view('admin.utilisateurs.edition', compact(
            'utilisateur',
            'roles'
        ));
    }

    /**
     * Met à jour les informations d'un utilisateur.
     *
     * @param \App\Http\Requests\UpdateUtilisateurRequest $request
     * @param \App\Models\Utilisateur $utilisateur
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateUtilisateurRequest $request, Utilisateur $utilisateur)
    {
        /**
         * La validation est gérée automatiquement par UpdateUtilisateurRequest.
         * Récupération des données validées.
         */
        $utilisateur->update($request->validated());

        return redirect()
            ->route('admin.utilisateurs.index')
            ->with('success', 'L\'utilisateur a été modifié avec succès.');
    }
}
