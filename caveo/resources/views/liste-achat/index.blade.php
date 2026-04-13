@extends('layouts.main')
@section('title', 'Liste Achat')

@section('fleche')
    <!-- Flèche de retour qui revient à la page précédente (Cellier ou Catalogue) permet de garder les filtres de catalogue si besoin -->
    <a href="{{ url()->previous() }}">
        <img src="{{ asset('images/fleches/gauche-blanc.svg') }}" alt="Flèche de retour" class="w-10 h-10">
    </a>
@endsection

@section('content')
<script type="module" src="{{ asset('js/liste-dropdown.js') }}"></script>
<script type="module" src="{{ asset('js/message-flash-auto.js') }}"></script>

<div class="m-4 flex items-start justify-between gap-4">
    <div>
        <h1 class="text-3xl text-[#7A1E2E]" style="font-family: 'Crimson Text', serif;">
            Mes listes d'achat
        </h1>
        <p class="text-sm text-gray-600 mt-1 font-roboto">
            Consultez et gérez vos liste d'achat.
        </p>
    </div>

    <a href="{{ route('achat.create') }}"
        class="bg-[#A83248] text-white px-4 py-3 rounded font-semibold whitespace-nowrap">
        Nouvelle liste
    </a>
</div>

<div class="m-4">
    <x-alerts />
</div>

@if($listes->isEmpty())
<div class="mt-[30px] mb-[30px] ml-4 mr-4 p-4 bg-yellow-50 border border-yellow-200 text-yellow-800 rounded text-center">
    Vous n’avez encore aucune liste d'achat
</div>
@else
<section class="mb-24">
    @foreach($listes as $liste)

    <div class="m-4 border rounded bg-white">
        <!-- Entête clickable -->
        <div
            class="w-full p-4 flex justify-between items-center hover:bg-gray-50 cursor-pointer toggle-liste"
            data-target="liste-{{ $liste->id }}">

            <!-- GAUCHE : TITRE -->
            <div class="min-w-0">
                <h2 class="font-semibold text-lg">
                    {{ $liste->nom }}
                </h2>

                @if(!empty($liste->description))
                    <p class="text-sm text-gray-600 truncate">
                        {{ $liste->description }}
                    </p>
                @endif
            </div>

            <!-- DROITE : ACTIONS -->
            <div class="flex items-center gap-3 flex-shrink-0">

                <a href="{{ route('achat.edit', $liste) }}"
                    class="w-10 h-10 flex items-center justify-center border rounded hover:bg-gray-100">
                    <img src="{{ asset('images/icons/crayon.svg') }}" class="w-6 h-6">
                </a>

                <form method="POST" action="{{ route('achat.destroy', $liste) }}">
                    @csrf
                    @method('DELETE')

                    <button type="submit"
                        onclick="return confirm('Supprimer cette liste ?')"
                        class="w-10 h-10 flex items-center justify-center border rounded hover:bg-gray-100">
                        <img src="{{ asset('images/icons/poubelle.svg') }}" class="w-6 h-6">
                    </button>
                </form>

            </div>

        </div>

        <!-- CONTENUE DROPDOWN -->
        <div id="liste-{{ $liste->id }}" class="hidden border-t p-4">
            @if($liste->bouteilles->isEmpty())
                <p class="text-sm text-gray-500">
                    Aucune bouteille dans cette liste
                </p>
            @else
                <div class="space-y-2">
                @foreach($liste->bouteilles as $bouteille)
                    <div class="flex justify-between items-start border rounded p-2 gap-4">
                        <span class="flex-1 break-words">
                            {{ $bouteille->nom }}
                        </span>
                        
                        <div class="flex items-center gap-4 shrink-0">
                            <span class="text-sm text-gray-600 whitespace-nowrap">
                                x{{ $bouteille->pivot->quantite }}
                            </span>
                            <form method="POST" action="{{ route('achat.bouteilles.destroy', [$liste->id, $bouteille->id]) }}">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                    onclick="return confirm('Supprimer cette bouteille ?')"
                                    class="w-5 h-5 flex items-center justify-center rounded hover:bg-gray-100">
                                    <img src="{{ asset('images/symbole/symbole-x-noir.svg') }}" class="w-4 h-4">
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    @endforeach
</section>
@endif

@endsection