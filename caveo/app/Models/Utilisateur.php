<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Utilisateur extends Authenticatable
{
    use HasFactory;

     protected $fillable = [
        'prenom',
        'nom',
        'email',
        'mot_de_passe',
    ];

    /**
     * Indique quel champ utiliser comme mot de passe.
     * Par défaut Laravel utilise "password", mais ici on utilise "mot_de_passe".
     */
    public function getAuthPassword()
    {
        return $this->mot_de_passe;
    }
}
