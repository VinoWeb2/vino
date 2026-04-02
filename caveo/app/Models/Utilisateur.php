<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Utilisateur extends Authenticatable
{
    use HasFactory;

    /**
     * Model `Utilisateur` — représente la table `utilisateurs`.
     * Étend `Authenticatable` pour l'usage avec le système d'authentification Laravel.
     */

    protected $table = 'utilisateurs';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'prenom',
        'nom',
        'email',
        'mot_de_passe',
        'id_role',
    ];

    protected $hidden = [
        'mot_de_passe',
    ];

    /**
     * Retourne le mot de passe utilisé par le système d'authentification.
     */
    public function getAuthPassword()
    {
        return $this->mot_de_passe;
    }

    /**
     * Relation vers le rôle de l'utilisateur.
     * Utilise la clé `id_role` dans la table `utilisateurs`.
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'id_role');
    }
}
