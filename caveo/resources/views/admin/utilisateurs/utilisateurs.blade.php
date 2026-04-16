@extends('layouts.main')

@section('title', 'Gestion des utilisateurs - Administration')

@section('content')

<script type='module' src="{{ asset('js/recherche.js') }}"></script>
<script type='module' src="{{ asset('js/renitialiser-bouton.js') }}"></script>

<form method="GET" action="{{ url()->current() }}" id="search-form">
    <div class="m-4">
        <!-- En-tête de la page -->
        <div class="mb-4 text-center">
            <h2 class="text-xl font-bold text-[#7A1E2E] mb-1">
                Gestion des utilisateurs
            </h2>
            <p class="text-[#1A1A1A] text-sm">
                Liste complète des utilisateurs
            </p>
        </div>

        <x-alerts />

        <!-- Filtre par rôle -->
        <div class="flex flex-wrap gap-2 mb-3">
            <label class="cursor-pointer">
                <input type="radio" name="role_id" value="" class="hidden peer"
                    {{ request('role_id') == '' ? 'checked' : '' }}
                    onchange="this.form.submit()">
                <span class="inline-block px-4 py-2 rounded-full text-sm font-medium border
                    peer-checked:bg-[#A83248] peer-checked:text-white peer-checked:border-[#A83248]
                    bg-white text-[#1A1A1A] border-gray-300">
                    Tous
                </span>
            </label>
            @foreach($roles as $role)
            <label class="cursor-pointer">
                <input type="radio" name="role_id" value="{{ $role->id }}" class="hidden peer"
                    {{ request('role_id') == $role->id ? 'checked' : '' }}
                    onchange="this.form.submit()">
                <span class="inline-block px-4 py-2 rounded-full text-sm font-medium border
                    peer-checked:bg-[#A83248] peer-checked:text-white peer-checked:border-[#A83248]
                    bg-white text-[#1A1A1A] border-gray-300">
                    {{ $role->nom }}
                </span>
            </label>
            @endforeach
        </div>

        <!-- Barre de recherche -->
        <div class="flex gap-2 items-stretch">
            <input type="text" name="recherche"
                value="{{ request('recherche') }}"
                placeholder="Rechercher par nom, prénom ou email..."
                class="border rounded px-3 h-12 w-full"
                id="search-input">

            <button type="submit" id="clearBtn" class="bg-[#A83248] text-white px-4 h-12 rounded flex items-center justify-center" title="Réinitialiser la recherche">
                <img src="{{ asset('images/symbole/symbole-x.svg') }}" alt="réinitialiser" class="w-6 h-6">
            </button>
        </div>
        <p class="italic font-bold text-sm md:text-base" style="color: #7A1E2E;">Se soumet automatiquement après 3 secondes</p>
    </div>
</form>

<div class="m-4 pb-24">
    @if($utilisateurs->total() > 0)
    <p class="text-sm text-[#1A1A1A] mb-4">
        Résultats {{ $utilisateurs->firstItem() }}-{{ $utilisateurs->lastItem() }} sur {{ $utilisateurs->total() }}
    </p>
    @endif

    @if($utilisateurs->count() > 0)
        <div class="space-y-3 mb-6">
            @foreach($utilisateurs as $utilisateur)
            <div class="border border-gray-200 rounded-lg p-4 hover:border-[#7A1E2E] transition duration-200">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <p class="text-base font-bold text-[#1A1A1A] mb-2">
                            {{ $utilisateur->prenom }} {{ $utilisateur->nom }}
                        </p>
                        <p class="text-sm text-gray-600 mb-2">
                            {{ $utilisateur->email }}
                        </p>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                            {{ $utilisateur->role->nom === 'admin' ? 'bg-red-100 text-red-800' : 'bg-gray-200 text-gray-500' }}">
                            {{ $utilisateur->role->nom ?? 'Non défini' }}
                        </span>
                    </div>
                    <a href="{{ route('admin.utilisateurs.edit', $utilisateur) }}"
                        class="w-10 h-10 flex items-center justify-center border border-gray-300 rounded hover:bg-gray-100"
                        title="Modifier l'utilisateur"
                        aria-label="Modifier l'utilisateur">
                        <img src="{{ asset('images/icons/crayon.svg') }}" alt="" aria-hidden="true" class="w-6 h-6">
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            <div class="flex justify-center">
                {{ $utilisateurs->links() }}
            </div>
        </div>

    @else
        <!-- Aucun utilisateur trouvé -->
        <div class="py-12 text-center">
            <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <h3 class="text-lg font-bold text-[#7A1E2E] mb-2">
                Aucun utilisateur trouvé
            </h3>
            <p class="text-sm text-[#1A1A1A]">
                @if(request('recherche') || request('role_id'))
                    Aucun utilisateur ne correspond à vos critères de recherche.
                @else
                    Il n'y a actuellement aucun utilisateur dans le système.
                @endif
            </p>
        </div>
    @endif
</div>
@endsection
