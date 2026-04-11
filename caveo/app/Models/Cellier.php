<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cellier extends Model
{
    use HasFactory;

    /**
     * Nom de la table associée au modèle.
     *
     * @var string
     */
    protected $table = 'celliers';

    /**
     * Clé primaire du modèle.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Attributs pouvant être assignés en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nom',
        'id_utilisateur',
        'description',
        'emplacement',
    ];

    public $timestamps = false;

    /**
     * Retourne l'utilisateur propriétaire du cellier.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'id_utilisateur');
    }

    /**
     * Retourne les lignes d'inventaire associées au cellier.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inventaires()
    {
        return $this->hasMany(Inventaire::class, 'id_cellier');
    }
}
