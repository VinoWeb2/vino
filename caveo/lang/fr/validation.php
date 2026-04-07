<?php

return [

    'accepted' => 'Le champ :attribute doit être accepté.',
    'accepted_if' => 'Le champ :attribute doit être accepté lorsque :other est :value.',
    'active_url' => 'Le champ :attribute doit être une URL valide.',
    'after' => 'Le champ :attribute doit être une date après :date.',
    'after_or_equal' => 'Le champ :attribute doit être une date après ou égale à :date.',
    'alpha' => 'Le champ :attribute ne doit contenir que des lettres.',
    'alpha_dash' => 'Le champ :attribute ne doit contenir que des lettres, chiffres, tirets et underscores.',
    'alpha_num' => 'Le champ :attribute ne doit contenir que des lettres et chiffres.',
    'array' => 'Le champ :attribute doit être un tableau.',
    'before' => 'Le champ :attribute doit être une date avant :date.',
    'before_or_equal' => 'Le champ :attribute doit être une date avant ou égale à :date.',

    'between' => [
        'numeric' => 'Le champ :attribute doit être entre :min et :max.',
        'string' => 'Le champ :attribute doit contenir entre :min et :max caractères.',
    ],

    'boolean' => 'Le champ :attribute doit être vrai ou faux.',
    'confirmed' => 'La confirmation du champ :attribute ne correspond pas.',
    'email' => 'Le champ :attribute doit être une adresse courriel valide.',
    'exists' => 'Le :attribute sélectionné est invalide.',

    'max' => [
        'string' => 'Le champ :attribute ne doit pas dépasser :max caractères.',
    ],

    'min' => [
        'string' => 'Le champ :attribute doit contenir au moins :min caractères.',
    ],

    'required' => 'Le champ :attribute est obligatoire.',
    'string' => 'Le champ :attribute doit être une chaîne de caractères.',
    'unique' => 'Le :attribute est déjà utilisé.',

    /*
    |--------------------------------------------------------------------------
    | Attributs personnalisés
    |--------------------------------------------------------------------------
    */

    'attributes' => [
        'email' => 'adresse courriel',
        'mot_de_passe' => 'mot de passe',
        'password' => 'mot de passe',
        'prenom' => 'prénom',
        'nom' => 'nom',
    ],

    'custom' => [
        'email' => [
            'required' => 'L’adresse courriel est requis.',
        ],
    ],
];