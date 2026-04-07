@extends('layouts.main')
@section('fleche')
    <a href="{{ url()->previous() }}">
        <img src="{{ asset('images/fleches/gauche-blanc.svg') }}" alt="Flèche de retour" class="w-10 h-10">
    </a>
@endsection
@section('title', 'Fiche détaillée')
@section('content')
    <div class="px-3 pt-4 pb-32">
        <h2 class="mb-6 text-center text-xl text-[#7A1E2E]" style="font-family: 'Roboto', sans-serif;">
            Fiche détaillée de la bouteille
        </h2>
        <section class="rounded-xl border border-[#E0E0E0] bg-white px-5 py-6 shadow-md">
            <h3 class="mb-5 text-center text-xl font-medium text-[#1A1A1A]" style="font-family: 'Roboto', sans-serif;">
                {{ $bouteille->nom }}
            </h3>
            <div class="relative mb-5 flex h-56 items-center justify-center">
                @if ($bouteille->image)
                    <img src="{{ $bouteille->image }}" alt="{{ $bouteille->nom }}" class="max-h-full max-w-full object-contain">
                @else
                    <img src="{{ asset('images/bouteilles/bouteille-placeholder.png') }}" alt="Image par défaut"
                        class="max-h-full max-w-full object-contain">
                @endif

                @if ($bouteille->image_pastille)
                    <img src="{{ asset('images/pastilles/' . $bouteille->image_pastille) }}"
                        alt="{{ $bouteille->pastille_gout }}" class="absolute right-2 bottom-2 h-20 w-20 object-contain">

                @endif
            </div>
            <p class="mb-1 text-left text-lg font-medium text-[#1A1A1A]" style="font-family: 'Roboto', sans-serif;">
                {{ $bouteille->type }} |
                {{ $bouteille->format ? $bouteille->format . 'ml' : 'Format non spécifié' }} |
                {{ $bouteille->pays }}
            </p>
            <p class="mb-5 text-left text-xl text-[#1A1A1A]" style="font-family: 'Roboto', sans-serif;">
                @if ($bouteille->prix !== null)
                    {{ number_format($bouteille->prix, 2, ',', ' ') }}$
                @else
                    Prix non spécifié
                @endif
            </p>
            <div class="text-left text-[#1A1A1A]" style="font-family: 'Roboto', sans-serif;">
                <div class="flex gap-2 mb-2 text-lg">
                    <span class="font-medium">Millésime :</span>
                    <span class="flex-1">
                        {{ $bouteille->millesime ?? 'Non spécifié' }}
                    </span>
                </div>

                <div class="flex gap-2 mb-2 text-lg">
                    <span class="font-medium">Taux d'alcool :</span>
                    <span class="flex-1">
                        {{ $bouteille->taux_alcool ? $bouteille->taux_alcool . '%' : "Non spécifié" }}
                    </span>
                </div>

                <div class="flex gap-2 text-lg">
                    <span class="font-medium">Cépage(s) :</span>
                    <span class="flex-1">
                        {{ $bouteille->cepage ?? 'Non spécifié' }}
                    </span>
                </div>
            </div>
        </section>

    </div>
@endsection