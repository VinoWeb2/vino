@extends('layouts.main')

@section('title', 'Modifier une bouteille')

@section('fleche')
<a href="{{ route('celliers.show', $cellier) }}" class="text-white text-2xl leading-none" aria-label="Retour">
    <img src="{{ asset('images/fleches/gauche-blanc.svg') }}" alt="Flèche de retour" class="w-10 h-10">
</a>
@endsection

@section('content')

<script src="{{ asset('js/quantite-form.js') }}"></script>

<div class="m-4">
    <h1 class="text-3xl text-[#7A1E2E]" style="font-family: 'Crimson Text', serif;">
        Modifier une bouteille non listée
    </h1>
    <p class="text-sm text-gray-600 mt-1 font-roboto">
        Modifiez les informations de cette bouteille dans votre cellier <strong>{{ $cellier->nom }}</strong>.
    </p>
</div>

<div class="m-4">
    <x-alerts />
</div>

<div class="m-4 border p-4 mb-24 rounded bg-white font-roboto">
    <form method="POST"
        action="{{ route('celliers.bouteilles.update', [$cellier, $bouteille]) }}"
        enctype="multipart/form-data"
        class="flex flex-col gap-5" novalidate>
        @csrf
        @method('PUT')

        @include('bouteilles._form')

        <div class="flex gap-3 pt-2">
            <button type="submit" class="w-1/2 bg-[#A83248] text-white py-3 rounded font-medium">
                Enregistrer
            </button>

            <a href="{{ route('celliers.show', $cellier) }}"
                class="w-1/2 text-center border py-3 rounded font-medium">
                Annuler
            </a>
        </div>
    </form>
</div>

@endsection