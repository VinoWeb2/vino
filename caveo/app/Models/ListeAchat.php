<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListeAchat extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'id_utilisateur',
        'description',
    ];
}
