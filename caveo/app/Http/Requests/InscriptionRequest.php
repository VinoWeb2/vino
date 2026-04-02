<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class InscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Règles de validation appliquées lors de l'inscription.
        return [
            'prenom' => ['required', 'string', 'max:35'],
            'nom' => ['required', 'string', 'max:35'],
            'courriel' => ['required', 'email', 'max:255', 'unique:utilisateurs,email'],
            'mot_de_passe' => ['required', 'confirmed', Rules\Password::defaults()],
        ];
    }

    public function messages(): array
    {
        // Messages personnalisés pour des retours clairs à l'utilisateur.
        return [
            'prenom.required' => 'Le prénom est requis.',
            'prenom.max' => 'Le prénom ne doit pas dépasser :max caractères.',

            'nom.required' => 'Le nom est requis.',
            'nom.max' => 'Le nom ne doit pas dépasser :max caractères.',

            'courriel.required' => 'L\'adresse courriel est requise.',
            'courriel.email' => 'Veuillez fournir une adresse courriel valide.',
            'courriel.max' => 'Le courriel ne doit pas dépasser :max caractères.',
            'courriel.unique' => 'Ce courriel est déjà utilisé.',

            'mot_de_passe.required' => 'Le mot de passe est requis.',
            'mot_de_passe.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'mot_de_passe.min' => 'Le mot de passe doit contenir au moins :min caractères.',
        ];
    }

    public function attributes(): array
    {
        // Noms d'attributs lisibles pour remplacer les clés dans les messages d'erreur.
        return [
            'prenom' => 'prénom',
            'nom' => 'nom',
            'courriel' => 'adresse courriel',
            'mot_de_passe' => 'mot de passe',
            'mot_de_passe_confirmation' => 'confirmation du mot de passe',
        ];
    }
}
