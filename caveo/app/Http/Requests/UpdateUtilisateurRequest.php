<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUtilisateurRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à effectuer cette requête.
     *
     * Seuls les administrateurs peuvent modifier un utilisateur.
     */
    public function authorize(): bool
    {
        return optional($this->user()?->role)->nom === 'admin';
    }

    /**
     * Obtient les règles de validation applicables à la requête.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $utilisateur = $this->route('utilisateur');

        return [
            'prenom' => 'required|string|max:35',
            'nom' => 'required|string|max:35',
            'email' => 'required|email|max:255|unique:utilisateurs,email,' . $utilisateur->id,
            'id_role' => 'required|exists:roles,id',
        ];
    }

    /**
     * Obtient les messages personnalisés pour les erreurs de validation.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'prenom.required' => 'Le prénom est obligatoire.',
            'prenom.string' => 'Le prénom doit être une chaîne de caractères.',
            'prenom.max' => 'Le prénom ne peut pas dépasser :max caractères.',

            'nom.required' => 'Le nom est obligatoire.',
            'nom.string' => 'Le nom doit être une chaîne de caractères.',
            'nom.max' => 'Le nom ne peut pas dépasser :max caractères.',

            'email.required' => 'L\'adresse courriel est obligatoire.',
            'email.email' => 'L\'adresse courriel doit être valide.',
            'email.max' => 'L\'adresse courriel ne peut pas dépasser :max caractères.',
            'email.unique' => 'Cette adresse courriel est déjà utilisée.',

            'id_role.required' => 'Le rôle est obligatoire.',
            'id_role.exists' => 'Le rôle sélectionné n\'existe pas.',
        ];
    }

    /**
     * Obtient les attributs personnalisés pour les erreurs de validation.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'prenom' => 'prénom',
            'nom' => 'nom',
            'email' => 'adresse courriel',
            'id_role' => 'rôle',
        ];
    }
}
