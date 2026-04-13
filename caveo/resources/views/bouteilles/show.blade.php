@extends('layouts.main')
@section('title', 'Fiche détaillée')
@section('fleche')
    <!-- Flèche de retour qui revient à la page précédente (Cellier ou Catalogue) -->
    <a href="{{ url()->previous() }}">
        <img src="{{ asset('images/fleches/gauche-blanc.svg') }}" alt="Flèche de retour" class="w-10 h-10">
    </a>
@endsection
@section('content')
    <!-- Titre de la page en fonction de l'origine (Cellier ou Catalogue) -->
    <div class="px-3 pt-4 pb-28">
        <h2 class="mb-3 text-center text-xl text-[#7A1E2E]" style="font-family: 'Roboto', sans-serif;">
            <!-- ATTENTION, AJUSTER LA SOURCE CELLIER AVEC LE BON NOM -->
            {{ $source === 'cellier'
        ? "Fiche détaillée de ma bouteille"
        : "Fiche détaillée de la bouteille" }}
        </h2>
        <!-- Section Détails -->
        <section>
            <!-- Nom de la bouteille -->
            <h3 class="mb-3 text-center text-l font-medium text-[#1A1A1A]" style="font-family: 'Roboto', sans-serif;">
                {{ $bouteille->nom }}
            </h3>
            <!-- Section Image -->
            <div class="relative mb-3 flex h-44 items-center justify-center">
                <!-- Afficher l'image ou l'image par défaut si aucune image trouvée -->
                @if ($bouteille->image)
                    <img src="{{ $bouteille->image }}" alt="{{ $bouteille->nom }}" class="max-h-full max-w-full object-contain">
                @else
                    <img src="{{ asset('images/bouteilles/bouteille-placeholder.png') }}" alt="Image par défaut"
                        class="max-h-full max-w-full object-contain">
                @endif
                <!-- Afficher la pastille de goût s'il y en a une -->
                @if ($bouteille->image_pastille)
                    <img src="{{ asset('images/pastilles/' . $bouteille->image_pastille) }}"
                        alt="{{ $bouteille->pastille_gout }}" class="absolute right-8 bottom-4 h-16 w-16 object-contain">
 
                @endif
            </div>
            <!-- Détails principaux-->
            @if ($bouteille->type)
                <p class="mb-1 text-left text-base font-normal text-[#1A1A1A]" style="font-family: 'Roboto', sans-serif;">
                    {{ $bouteille->type }}
                </p>
            @endif
            @if ($bouteille->pays)
                <p class="mb-1 text-left text-base font-normal text-[#1A1A1A]" style="font-family: 'Roboto', sans-serif;">
                    {{ $bouteille->pays }}
                </p>
            @endif
            <!-- Prix -->
            <p class="mb-5 text-left text-base font-medium text-[#1A1A1A]" style="font-family: 'Roboto', sans-serif;">
                @if ($bouteille->prix !== null)
                    {{ number_format($bouteille->prix, 2, ',', ' ') }}$
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
                    <div class="flex justify-between items-start mb-1 text-base">
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
            <!-- ATTENTION, AJOUTER LES BOUTONS ET LA QUANTITÉ -->
        </section>
    </div>
@endsection