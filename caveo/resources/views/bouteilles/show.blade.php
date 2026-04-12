@extends('layouts.main')
@section('title', 'Fiche détaillée')

@section('fleche')
<!-- Flèche de retour qui revient vers le cellier ou le catalogue selon la source -->
<a href="{{ $source === 'cellier' ? route('celliers.index') : route('catalogue.index') }}">
    <img src="{{ asset('images/fleches/gauche-blanc.svg') }}" alt="Flèche de retour" class="w-10 h-10">
</a>
@endsection

@section('content')
<!-- Titre de la page en fonction de l'origine (Cellier ou Catalogue) -->
<div class="px-3 pt-4 pb-32">
    <h2 class="mb-6 text-center text-xl text-[#7A1E2E]" style="font-family: 'Roboto', sans-serif;">
        {{ $source === 'cellier'
                ? 'Fiche détaillée de ma bouteille'
                : 'Fiche détaillée de la bouteille' }}
    </h2>

    <!-- Section Détails -->
    <section class="rounded-xl border border-[#E0E0E0] bg-white px-5 py-6 shadow-md">
        <!-- Nom de la bouteille -->
        <h3 class="mb-5 text-center text-xl font-medium text-[#1A1A1A]" style="font-family: 'Roboto', sans-serif;">
            {{ $bouteille->nom }}
        </h3>

        <!-- Section Image -->
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

        <!-- Détails principaux -->
        <p class="mb-1 text-left text-lg font-medium text-[#1A1A1A]" style="font-family: 'Roboto', sans-serif;">
            {{ $bouteille->type }} |
            {{ $bouteille->format ? $bouteille->format . 'ml' : 'Format non spécifié' }} |
            {{ $bouteille->pays }}
        </p>

        <!-- Prix -->
        <p class="mb-5 text-left text-xl text-[#1A1A1A]" style="font-family: 'Roboto', sans-serif;">
            @if ($bouteille->prix !== null)
            {{ number_format($bouteille->prix, 2, ',', ' ') }}$
            @else
            Prix non spécifié
            @endif
        </p>
        <!-- Détails secondaires -->
        <div class="mt-3 pt-3 border-t border-[#E0E0E0] text-left text-[#1A1A1A]"
            style="font-family: 'Roboto', sans-serif;">
            <!-- Millésime -->
            @if ($bouteille->millesime)
            <div class="flex gap-2 mb-1 text-base">
                <span class="font-medium">Millésime :</span>
                <span class="flex-1 font-normal">
                    {{ $bouteille->millesime}}
                </span>
            </div>
            @endif

            <!-- Taux d'alcool -->
            @if ($bouteille->taux_alcool)
            <div class="flex gap-2 mb-1 text-base">
                <span class="font-medium">Taux d'alcool :</span>
                <span class="flex-1 font-normal">
                    {{ $bouteille->taux_alcool ? $bouteille->taux_alcool . '%' : 'Non spécifié' }}
                </span>
            </div>
            @endif

            @if ($bouteille->cepage || $bouteille->format)
            <div class="flex justify-between item-start mb-1 text-base">

                <!-- Cépage(s) à gauche -->
                @if ($bouteille->cepage)
                <div class="flex gap-2 text-base">
                    <span class="font-medium">Cépage(s) :</span>
                    <span class="flex-1 font-normal">
                        {{ $bouteille->cepage }}
                    </span>
                </div>
                @endif

                <!-- Format à droite -->
                @if ($bouteille->format)
                <span class="text-sm font-normal">
                    {{ $bouteille->format }} ml
                </span>
                @endif
            </div>
            @endif


            <!-- Description à afficher seulement s'il y en a une -->
            @if (!empty($bouteille->description))
            <div class="flex gap-2 mb-1 text-base">
                <span class="font-medium">Description :</span>
                <span class="flex-1 font-normal wrap-break-word">{{ $bouteille->description }}</span>
            </div>
            @endif
        </div>

        @if (!empty($bouteille->description))
        <div class="flex gap-2 mb-2 text-lg">
            <span class="font-medium">Description :</span>
            <span class="flex-1 wrap-break-word">{{ $bouteille->description }}</span>
        </div>
        @endif
</div>

<!-- ATTENTION, AJOUTER LES BOUTONS ET LA QUANTITÉ -->
</section>
</div>
@endsection