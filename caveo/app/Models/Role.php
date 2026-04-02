<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    /**
     * Model `Role` — représente la table `roles`.
     * Champs principaux : `nom`.
     * Fournit la relation inverse vers les utilisateurs associés.
     */

    protected $table = 'roles';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'nom',
    ];

    public function utilisateurs()
    {
        // Renvoie tous les `Utilisateur` ayant `id_role` égal à ce rôle.
        return $this->hasMany(Utilisateur::class, 'id_role');
    }
}
