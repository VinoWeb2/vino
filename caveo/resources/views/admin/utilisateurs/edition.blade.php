@extends('layouts.main')

@section('title', 'Modifier un utilisateur')

@section('fleche')

<script src="{{ asset('js/selection-role.js') }}"></script>

<a href="{{ route('admin.utilisateurs.index') }}" class="text-white text-2xl leading-none" aria-label="Retour">
    <img src="/images/fleches/gauche-blanc.svg" alt="Flèche de retour" class="w-10 h-10">
</a>
@endsection

@section('content')

<div class="m-4">
    <h1 class="text-3xl text-[#7A1E2E]" style="font-family: 'Crimson Text', serif;">
        Modifier un utilisateur
    </h1>
    <p class="text-sm text-gray-600 mt-1">
        Mettez à jour les informations de l'utilisateur.
    </p>
</div>

<div class="m-4">
    <x-alerts />
</div>

<div class="m-4 border p-4 rounded bg-white">
    <form method="POST" action="{{ route('admin.utilisateurs.update', $utilisateur) }}" class="flex flex-col gap-5" novalidate>
        @csrf
        @method('PUT')

        <div class="flex flex-col gap-4">

            <!-- Prénom -->
            <div>
                <label for="prenom" class="block mb-2 font-semibold text-[#1A1A1A]">
                    Prénom
                </label>
                <input
                    type="text"
                    id="prenom"
                    name="prenom"
                    value="{{ old('prenom', $utilisateur->prenom) }}"
                    maxlength="35"
                    required
                    placeholder="Ex. Marie"
                    class="w-full border rounded px-3 py-3 @error('prenom') border-red-500 @enderror">
                @error('prenom')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nom -->
            <div>
                <label for="nom" class="block mb-2 font-semibold text-[#1A1A1A]">
                    Nom
                </label>
                <input
                    type="text"
                    id="nom"
                    name="nom"
                    value="{{ old('nom', $utilisateur->nom) }}"
                    maxlength="35"
                    required
                    placeholder="Ex. Dupont"
                    class="w-full border rounded px-3 py-3 @error('nom') border-red-500 @enderror">
                @error('nom')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Courriel -->
            <div>
                <label for="email" class="block mb-2 font-semibold text-[#1A1A1A]">
                    Adresse courriel
                </label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email', $utilisateur->email) }}"
                    maxlength="255"
                    required
                    placeholder="exemple@courriel.com"
                    class="w-full border rounded px-3 py-3 @error('email') border-red-500 @enderror">
                @error('email')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Rôle -->
            <div>
                <label class="block mb-2 font-semibold text-[#1A1A1A]">
                    Rôle
                </label>
                <div class="space-y-2">
                    @foreach($roles as $role)
                    <label class="flex items-center cursor-pointer">
                        <input
                            type="checkbox"
                            name="id_role"
                            value="{{ $role->id }}"
                            class="role-checkbox w-5 h-5 border-gray-300 rounded focus:ring-[#A83248] focus:ring-2"
                            style="accent-color: #A83248;"
                            {{ old('id_role', $utilisateur->id_role) == $role->id ? 'checked' : '' }}>
                        <span class="ml-3 text-gray-700">{{ $role->nom }}</span>
                    </label>
                    @endforeach
                </div>
                @error('id_role')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

        </div>

        <!-- Boutons d'action -->
        <div class="flex gap-3 pt-2 mb-24">
            <button type="submit" class="w-1/2 bg-[#A83248] text-white py-3 rounded font-medium">
                Sauvegarder
            </button>

            <a href="{{ route('admin.utilisateurs.index') }}"
                class="w-1/2 text-center border py-3 rounded font-medium">
                Annuler
            </a>
        </div>
    </form>
</div>
@endsection
