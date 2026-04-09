<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Utilisateur extends Authenticatable
{
    use HasFactory;

    /**
     * Nom de la table associée au modèle.
     *
     * @var string
     */
    protected $table = 'utilisateurs';

    /**
     * Clé primaire du modèle.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indique si le modèle utilise les colonnes created_at et updated_at.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Attributs pouvant être assignés en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'prenom',
        'nom',
        'email',
        'mot_de_passe',
        'id_role',
    ];

    /**
     * Attributs à masquer lors de la sérialisation.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'mot_de_passe',
    ];

    /**
     * Retourne le mot de passe utilisé par le système d'authentification Laravel.
     *
     * Cette méthode est nécessaire puisque le champ utilisé dans la table
     * n'est pas "password", mais "mot_de_passe".
     *
     * @return string
     */
    public function getAuthPassword(): string
    {
        return $this->mot_de_passe;
    }

    /**
     * Retourne le rôle associé à l'utilisateur.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'id_role');
    }

    /**
     * Retourne les celliers appartenant à l'utilisateur.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function celliers()
    {
        return $this->hasMany(Cellier::class, 'id_utilisateur');
    }
}
