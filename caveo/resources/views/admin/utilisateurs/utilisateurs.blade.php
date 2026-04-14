@extends('layouts.main')

@section('title', 'Gestion des utilisateurs - Administration')

@section('content')

<script type="module" src="{{ asset('js/filtre-utilisateurs.js') }}"></script>
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

        <!-- Messages flash -->
        @if(session('success'))
        <div class="border-l-4 border-green-500 bg-green-50 text-green-700 p-3 mb-4 rounded" role="alert">
            <p class="text-sm font-medium">{{ session('success') }}</p>
        </div>
        @endif

        @if(session('error'))
        <div class="border-l-4 border-red-500 bg-red-50 text-red-700 p-3 mb-4 rounded" role="alert">
            <p class="text-sm font-medium">{{ session('error') }}</p>
        </div>
        @endif

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

        <!-- Bouton Filtres -->
        <div class="mt-2">
            <button type="button" id="openFilters"
                class="bg-[#A83248] text-white h-12 rounded w-full font-semibold">
                Filtres
            </button>
        </div>
    </div>

    <!-- overlay -->
    <div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 hidden z-40"></div>

    <!-- Panneau filtres -->
    <div id="filterPanel" class="fixed bottom-0 left-0 right-0 bg-white rounded-t-2xl z-50 max-h-[90vh] flex flex-col translate-y-full transition-transform duration-300">

        <!-- Entête des filtres -->
        <div class="flex justify-between items-center p-4 border-b">
            <h2 class="text-lg font-bold">Filtres</h2>
            <button type="button" id="closeFilters" class="text-xl">✕</button>
        </div>

        <!-- Contenu scrollable -->
        <div class="overflow-y-auto p-4 flex flex-col gap-6">

            <!-- FILTRE PAR RÔLE -->
            <div>
                <label class="font-semibold block mb-2 text-sm">
                    Filtrer par rôle
                </label>
                <div class="flex flex-col gap-2">
                    <label class="flex items-center gap-2 text-sm">
                        <input type="radio" name="role_id" value=""
                            {{ request('role_id') == '' ? 'checked' : '' }}>
                        Tous les rôles
                    </label>
                    @foreach($roles as $role)
                    <label class="flex items-center gap-2 text-sm">
                        <input type="radio" name="role_id" value="{{ $role->id }}"
                            {{ request('role_id') == $role->id ? 'checked' : '' }}>
                        {{ $role->nom }}
                    </label>
                    @endforeach
                </div>
            </div>

        </div>

        <!-- Actions sticky -->
        <div class="sticky bottom-0 bg-white z-50 border-t mb-5">
            <div class="flex flex-row gap-4 pt-4">
                <a href="{{ route('admin.utilisateurs.index') }}?recherche={{ request('recherche') }}"
                    class="w-1/2 text-center border py-3 rounded font-medium text-sm">
                    Réinitialiser
                </a>
                <button type="submit"
                    class="w-1/2 bg-[#A83248] text-white py-3 rounded font-medium text-sm">
                    Appliquer
                </button>
            </div>
        </div>

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
                <div class="flex justify-between items-start mb-2">
                    <div class="flex-1">
                        <p class="text-base font-bold text-[#1A1A1A]">
                            {{ $utilisateur->prenom }} {{ $utilisateur->nom }}
                        </p>
                        <p class="text-sm text-gray-600">
                            {{ $utilisateur->email }}
                        </p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                        {{ $utilisateur->role->nom === 'Administrateur' ? 'bg-red-100 text-red-800' : 'bg-gray-200 text-gray-500' }}">
                        {{ $utilisateur->role->nom ?? 'Non défini' }}
                    </span>
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
