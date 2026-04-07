@extends('layouts.main')
@section('title', 'caveo')
@section('content')

<script type="module" src="{{ asset('js/filtre.js') }}"></script>

<div class="m-4">
    <!-- 
        Le formulaire envoie une requête GET vers l'URL actuelle.
        url()->current() permet de rester sur la même page (index du catalogue)
        et d'ajouter simplement le paramètre ?recherche=... dans l'URL.

        Exemple : /catalogue?recherche=vin

        Avantages :
        - Permet de partager l’URL avec la recherche
        - Évite de créer une route spécifique pour la recherche
    -->
    <form method="GET" action="{{ url()->current() }}" class="flex gap-2">
        <input 
            type="text" 
            name="recherche" 
            value="{{ request('recherche') }}" 
            placeholder="Rechercher une bouteille..." 
            class="border rounded px-3 py-2 w-full"
        >

        <button type="submit" class="bg-[#A83248] text-white px-4 py-2 rounded">
            <img src="{{ asset('images/recherche/recherche-blanc.svg') }}" alt="" class="w-10 h-10">
        </button>
    </form>

    <div class="m-4">
        <button id="openFilters" class="bg-[#A83248] text-white px-4 py-3 rounded w-full font-semibold">
            Filtres
        </button>
    </div>

    <!-- overlay -->
    <div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 hidden z-40"></div>

    <!-- Paneaux ouvert au-dessus du contenue  -->
    <div id="filterPanel" class="fixed bottom-0 left-0 right-0 bg-white rounded-t-2xl z-50 max-h-[90vh] flex flex-col translate-y-full transition-transform duration-300">

        <!-- Entête des filtres -->
        <div class="flex justify-between items-center p-4 border-b">
            <h2 class="text-lg font-bold">Filtres</h2>
            <button id="closeFilters" class="text-xl">✕</button>
        </div>

        <!-- Contenu scrollable section des choix de filtres -->
        <div class="overflow-y-auto p-4 flex flex-col gap-6">

            <form method="GET" action="{{ url()->current() }}" class="flex flex-col gap-6">

                <!-- TRI -->
                <div>
                    <label class="font-semibold block mb-2">Trier</label>
                    <div class="flex flex-col gap-2">
                        <label class="flex items-center gap-2">
                            <input type="radio" name="tri_nom" value="" {{ request('tri_nom') == '' ? 'checked' : '' }}>
                            Ne pas trier
                        </label>

                        <label class="flex items-center gap-2">
                            <input type="radio" name="tri_nom" value="asc" {{ request('tri_nom') == 'asc' ? 'checked' : '' }}>
                            A → Z
                        </label>

                        <label class="flex items-center gap-2">
                            <input type="radio" name="tri_nom" value="desc" {{ request('tri_nom') == 'desc' ? 'checked' : '' }}>
                            Z → A
                        </label>
                    </div>
                </div>

                <!-- TYPE -->
                <div>
                    <label class="font-semibold block mb-2">Type</label>
                    <div class="flex flex-col gap-2 max-h-40 overflow-y-auto">
                        @foreach($types as $type)
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="types[]" value="{{ $type }}" {{ in_array($type, request('types', [])) ? 'checked' : '' }}>
                                {{ $type }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- PAYS -->
                <div>
                    <label class="font-semibold block mb-2">Pays</label>
                    <div class="flex flex-col gap-2 max-h-40 overflow-y-auto">
                        @foreach($pays as $p)
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="pays[]" value="{{ $p }}" {{ in_array($p, request('pays', [])) ? 'checked' : '' }}>
                                {{ $p }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- FORMAT -->
                <div>
                    <label class="font-semibold block mb-2">Quantité</label>
                    <div class="flex flex-col gap-2 max-h-40 overflow-y-auto">
                        @foreach($formats as $format)
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="formats[]" value="{{ $format }}" {{ in_array($format, request('formats', [])) ? 'checked' : '' }}>
                                {{ $format }} ml
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- MILLÉSIME -->
                <div>
                    <label class="font-semibold block mb-2">Millésime</label>
                    <div class="flex flex-col gap-2 max-h-40 overflow-y-auto">
                        @foreach($millesimes as $m)
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="millesimes[]" value="{{ $m }}" {{ in_array($m, request('millesimes', [])) ? 'checked' : '' }}>
                                {{ $m }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- ACTIONS -->
                <div class="flex gap-3 pt-4 border-t">
                    <a href="{{ url()->current() }}" class="w-1/2 text-center border py-3 rounded font-medium">
                        Réinitialiser
                    </a>

                    <button type="submit" class="w-1/2 bg-[#A83248] text-white py-3 rounded font-medium">
                        Appliquer
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<div class="m-4">
    <p>Résultats {{ $bouteilles->firstItem() }}-{{ $bouteilles->lastItem() }} sur {{ $bouteilles->total() }}</p>
</div>

@foreach($bouteilles as $bouteille)
    <div class="flex gap-6 m-4 mb-6 font-roboto border p-4 rounded">
        <div class="w-[90px] flex justify-center">
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

            <a href="#" class="px-4 py-2 bg-[#A83248] text-white rounded w-max">
                Détail
            </a>
        </div>
    </div>
@endforeach

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

    <p>Résultats {{ $bouteilles->firstItem() }}-{{ $bouteilles->lastItem() }} sur {{ $bouteilles->total() }}</p>

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

@endsection