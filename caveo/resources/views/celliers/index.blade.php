@extends('layouts.main')

@section('title', 'Mes celliers')

@section('content')

<div class="m-4 flex items-start justify-between gap-4">
    <div>
        <h1 class="text-3xl text-[#7A1E2E]" style="font-family: 'Crimson Text', serif;">
            Mes celliers
        </h1>
        <p class="text-sm text-gray-600 mt-1 font-roboto">
            Consultez et gérez vos celliers.
        </p>
    </div>

    <a href="{{ route('celliers.create') }}"
        class="bg-[#A83248] text-white px-4 py-3 rounded font-semibold whitespace-nowrap">
        Nouveau
    </a>
</div>

<div class="m-4">
    <x-alerts />
</div>

@if($celliers->isEmpty())
<div class="m-4 border p-4 rounded bg-white font-roboto">
    <p>Vous n’avez encore aucun cellier.</p>
</div>
@else
@foreach($celliers as $cellier)
<div class="flex gap-6 m-4 mb-6 font-roboto border p-4 rounded bg-white">
    {{-- Image cellier --}}
    <div class="w-[90px] flex justify-center shrink-0">
        <img
            src="{{ asset('images/bouteille-vide.png') }}"
            alt="Illustration du cellier"
            class="w-auto h-[135px]">
    </div>

    {{-- Contenu --}}
    <div class="flex flex-col justify-between flex-1 min-w-0">
        <div>
            <h2 class="font-semibold text-lg break-words">
                {{ $cellier->nom }}
            </h2>

            <div class="flex items-center text-sm text-gray-600 space-x-2 flex-wrap">
                @if(!empty($cellier->emplacement))
                <p>{{ $cellier->emplacement }}</p>
                @endif

                @if(!empty($cellier->emplacement))
                <span>|</span>
                @endif

                <p>{{ $cellier->inventaires_count ?? 0 }} bouteille(s)</p>
            </div>

            @if(!empty($cellier->description))
            <p class="mt-2 font-medium mb-3 text-sm text-gray-700">
                {{ $cellier->description }}
            </p>
            @endif
        </div>

        {{-- Actions --}}
        <div class="mt-4 flex items-center gap-2 w-full">
            <a href="{{ route('celliers.show', $cellier) }}"
                class="flex-1 text-center bg-[#A83248] text-white py-2 rounded text-sm font-medium">
                Voir
            </a>

            <a href="{{ route('celliers.edit', $cellier) }}"
                class="flex-1 text-center border border-gray-300 text-gray-700 py-2 rounded text-sm font-medium hover:bg-gray-50 transition">
                Modifier
            </a>

            <form method="POST"
                action="{{ route('celliers.destroy', $cellier) }}"
                class="flex-1">
                @csrf
                @method('DELETE')

                <button type="submit"
                    onclick="return confirm('Supprimer ce cellier ?')"
                    class="w-full text-center border border-red-300 text-red-600 py-2 rounded text-sm font-medium hover:bg-red-50 transition">
                    Supprimer
                </button>
            </form>
        </div>
    </div>
</div>
</div>
@endforeach
@endif

@endsection