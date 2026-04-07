<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bouteille extends Model
{
    use HasFactory;

    protected $table = 'bouteilles';

    protected $fillable = [
        'code_saq',
        'nom',
        'type',
        'pays',
        'cepage',
        'millesime',
        'taux_alcool',
        'prix',
        'format',
        'image',
        'pastille_gout',
        'est_saq',
        'description',
    ];

    public function getImagePastilleAttribute()
    {
        $mapping = [
            'Aromatique et charnu' => 'aromatique-charnu.png',
            'Aromatique et rond' => 'aromatique-rond.png',
            'Aromatique et souple' => 'aromatique-souple.png',
            'Délicat et léger' => 'delicat-leger.png',
            'Fruité et doux' => 'fruite-doux.png',
            'Fruité et extra-doux' => 'fruite-extra-doux.png',
            'Fruité et généreux' => 'fruite-genereux.png',
            'Fruité et léger' => 'fruite-leger.png',
            'Fruité et vif' => 'fruite-vif.png',
        ];

        return $mapping[$this->pastille_gout] ?? null;
    }
}
