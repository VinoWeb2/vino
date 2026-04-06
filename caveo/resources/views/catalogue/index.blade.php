@extends('layouts.main')
@section('title', 'caveo')
@section('content')

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