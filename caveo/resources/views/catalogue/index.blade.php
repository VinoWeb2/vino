@extends('layouts.main')
@section('title', 'caveo')

@section('content')

<script type="module" src="{{ asset('js/filtre.js') }}"></script>
<script type="module" src="{{ asset('js/cellier-overlay.js') }}"></script>
<script type='module' src="{{ asset('js/recherche.js') }}"></script>
<script type='module' src="{{ asset('js/renitialiser-bouton.js') }}"></script>

<form method="GET" action="{{ url()->current() }}" id="search-form">
    <div class="m-4">
        <div class="flex gap-2 items-stretch">
            <input type="text" name="recherche"
                value="{{ request('recherche') }}"
                placeholder="Rechercher une bouteille..."
                class="border rounded px-3 h-12 w-full"
                id="search-input">

            <button type="submit" id="clearBtn" class="bg-[#A83248] text-white px-4 h-12 rounded flex items-center justify-center" title="Réinitialiser la recherche">
                <img src="{{ asset('images/symbole/symbole-x.svg') }}" alt="réinitialiser" class="w-6 h-6">
            </button>
        </div>
        <p class="italic font-bold text-sm md:text-base" style="color: #7A1E2E;">Se soumet automatiquement après 3 secondes</p>

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

            <!-- TRI -->
            <div>
                <label class="font-semibold block mb-2 text-sm">Trier</label>
                <div class="flex flex-col gap-2">
                    <label class="flex items-center gap-2 text-sm">
                        <input type="radio" name="tri_nom" value=""
                            {{ request('tri_nom') == '' ? 'checked' : '' }}>
                        Ne pas trier
                    </label>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="radio" name="tri_nom" value="asc"
                            {{ request('tri_nom') == 'asc' ? 'checked' : '' }}>
                        A → Z
                    </label>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="radio" name="tri_nom" value="desc"
                            {{ request('tri_nom') == 'desc' ? 'checked' : '' }}>
                        Z → A
                    </label>
                </div>
            </div>

            <!-- TYPE -->
            <div>
                <label class="font-semibold block mb-2 text-sm">Type</label>
                <div class="flex flex-wrap gap-2">
                    @foreach($types as $type)
                    <label class="flex items-center gap-2 text-sm w-[48%] sm:w-[30%]">
                        <input type="checkbox" name="types[]" value="{{ $type }}"
                            {{ in_array($type, request('types', [])) ? 'checked' : '' }}>
                        {{ $type }}
                    </label>
                    @endforeach
                </div>
            </div>

            <!-- PAYS -->
            <div>
                <label class="font-semibold block mb-2 text-sm">Pays</label>
                <div class="flex flex-wrap gap-2">
                    @foreach($pays as $p)
                    <label class="flex items-center gap-2 text-sm w-[48%] sm:w-[30%]">
                        <input type="checkbox" name="pays[]" value="{{ $p }}"
                            {{ in_array($p, request('pays', [])) ? 'checked' : '' }}>
                        {{ $p }}
                    </label>
                    @endforeach
                </div>
            </div>

            <!-- FORMAT -->
            <div>
                <label class="font-semibold block mb-2 text-sm">Quantité</label>
                <select name="formats" class="w-full border rounded px-3 py-2 text-sm">
                    <option value="">Sélectionner une quantité</option>
                    @foreach($formats as $format)
                    <option value="{{ $format }}" {{ request('formats') == $format ? 'selected' : '' }}>
                        {{ $format }} ml
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- MILLÉSIME -->
            <div>
                <label class="font-semibold block mb-2 text-sm">Millésime</label>
                <select name="millesimes" class="w-full border rounded px-3 py-2 text-sm">
                    <option value="">Sélectionner un millésime</option>
                    @foreach($millesimes as $m)
                    <option value="{{ $m }}" {{ request('millesimes') == $m ? 'selected' : '' }}>
                        {{ $m }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Actions sticky -->
        <div class="sticky bottom-0 bg-white z-50 border-t mb-5">
            <div class="flex flex-row gap-4 pt-4">
                <a href="{{ url()->current() }}?recherche={{ request('recherche') }}"
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

<div class="m-4">
    @if($bouteilles->total() > 0)
    <p>
        Résultats {{ $bouteilles->firstItem() }}-{{ $bouteilles->lastItem() }} sur {{ $bouteilles->total() }}
    </p>
    @else
    <p></p>
    @endif
</div>

@if($bouteilles->isEmpty())
<div class="mt-[30px] mb-[30px] ml-4 mr-4 p-4 bg-yellow-50 border border-yellow-200 text-yellow-800 rounded text-center">
    Aucune bouteille trouvée
</div>
@else
@foreach($bouteilles as $bouteille)
<div class="flex gap-6 m-4 mb-6 font-roboto border p-4 rounded">
    <div class="w-[90px] flex justify-center items-center">
        <img src="{{ $bouteille->image ?? asset('images/bouteille-vide.png') }}" alt="" class="w-auto h-[135px]">
    </div>

    <div class="flex flex-col justify-between flex-1">
        <div>
            <h2 class="font-semibold text-lg">
                {{ $bouteille->nom }}
            </h2>

            <div class="flex items-center text-sm text-gray-600 space-x-2">
                <p>{{ $bouteille->pays ?? "" }}</p>
                <span>|</span>
                <p>{{ $bouteille->format ?? "" }} ml</p>
                <span>|</span>
                <p>{{ $bouteille->type ?? "" }}</p>
            </div>

            <p class="mt-2 font-medium mb-3">
                {{ $bouteille->prix ?? "Non spécifié" }} $
            </p>
        </div>

        <div class="flex gap-2 flex-wrap">
            <a href="{{ route('bouteilles.show', $bouteille->id) }}?source=catalogue"
                class="px-2 py-2 border border-gray-300 rounded hover:bg-gray-100 flex items-center gap-2 w-max" title="Détail de la bouteille">
                <img src="{{ asset('images/symbole/info.svg') }}" alt="information" class="w-6 h-6">
            </a>

            @if($celliers->isNotEmpty())
            <button type="button"
                class="px-2 py-1 text-sm border border-gray-300 text-gray-600 rounded hover:bg-gray-100 openAddToCellierModal"
                data-bouteille-id="{{ $bouteille->id }}"
                data-bouteille-nom="{{ $bouteille->nom }}">
                Ajouter au cellier
            </button>
            @else
            <a href="{{ route('celliers.create') }}"
                class="px-4 py-2 bg-gray-200 text-gray-500 rounded text-sm font-medium w-max">
                Créer un cellier
            </a>
            @endif
        </div>
    </div>
</div>
@endforeach
@endif

@if($bouteilles->total() > 0)
<div class="flex justify-between items-center mx-auto my-5 mb-24">
    @if ($bouteilles->onFirstPage())
    <span>
        <img src="{{ asset('images/fleches/gauche-gris.svg') }}" class="w-14" alt="gauche bloqué">
    </span>
    @else
    <a href="{{ $bouteilles->previousPageUrl() }}">
        <img src="{{ asset('images/fleches/gauche-rouge.svg') }}" class="w-14" alt="gauche">
    </a>
    @endif

    <p>
        Résultats {{ $bouteilles->firstItem() }}-{{ $bouteilles->lastItem() }} sur {{ $bouteilles->total() }}
    </p>

    @if ($bouteilles->hasMorePages())
    <a href="{{ $bouteilles->nextPageUrl() }}">
        <img src="{{ asset('images/fleches/droit-rouge.svg') }}" class="w-14" alt="droite">
    </a>
    @else
    <span>
        <img src="{{ asset('images/fleches/droit-gris.svg') }}" class="w-14" alt="droite bloqué">
    </span>
    @endif
</div>
@else
<p></p>
@endif

@if($celliers->isNotEmpty())
{{-- Overlay modale --}}
<div id="cellierOverlay" class="fixed inset-0 bg-black bg-opacity-50 hidden z-40"></div>

{{-- Modal ajout au cellier --}}
<div id="addToCellierModal" class="fixed inset-0 hidden z-50 flex items-center justify-center px-4">
    <div class="bg-white w-full max-w-md rounded-lg shadow-lg p-5">

        <h2 class="text-lg font-semibold mb-4 text-center">
            Ajouter au cellier
        </h2>

        <form method="POST" id="addToCellierForm">
            @csrf

            {{-- Bouteille --}}
            <input type="hidden" name="id_bouteille" id="modalBouteilleId">

            <p class="text-center mb-4 font-medium" id="modalBouteilleNom"></p>

            {{-- Choix cellier --}}
            <div class="mb-4">
                <label class="block mb-1 text-sm font-medium">
                    Choisir un cellier
                </label>

                <select id="modalCellierSelect"
                    class="w-full border rounded px-3 py-2">
                    @foreach($celliers as $cellier)
                    <option value="{{ $cellier->id }}">
                        {{ $cellier->nom }}
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- Quantité (VERSION + / -) --}}
            <div class="mb-5">
                <label class="block mb-2 text-sm font-medium">
                    Quantité
                </label>

                <div class="flex items-center justify-between w-full">

                    {{-- Moins --}}
                    <button type="button"
                        class="w-1/3 flex items-center justify-center py-5"
                        aria-label="Diminuer la quantité"
                        title="Diminuer la quantité">
                        <img src="{{ asset('images/icons/cercle-moins.svg') }}"
                            alt="moins"
                            aria-hidden="true"
                            class="w-10 h-10">
                    </button>

                    {{-- Quantité affichée --}}
                    <div class="w-1/3 text-center">
                        <span id="modalQuantiteDisplay" class="text-2xl font-semibold">
                            1
                        </span>
                    </div>

                    {{-- Plus --}}
                    <button type="button"
                        class="w-1/3 flex items-center justify-center py-5"
                        aria-label="Augmenter la quantité"
                        title="Augmenter la quantité">
                        <img src="{{ asset('images/icons/cercle-plus.svg') }}"
                            alt="plus"
                            aria-hidden="true"
                            class="w-10 h-10">
                    </button>

                </div>

                {{-- Valeur envoyée --}}
                <input type="hidden" name="quantite" id="modalQuantite" value="1">
            </div>

            {{-- Boutons --}}
            <div class="flex gap-3">
                <button type="submit"
                    class="w-1/2 bg-[#A83248] text-white py-3 rounded font-medium">
                    Ajouter
                </button>

                <button type="button"
                    id="closeModal"
                    class="w-1/2 border py-3 rounded font-medium">
                    Annuler
                </button>
            </div>

        </form>
    </div>
</div>

@endif

@endsection