@extends('layouts.main')

@section('title', 'Créer un cellier')

@section('fleche')
<a href="{{ route('celliers.index') }}" class="text-white text-2xl leading-none" aria-label="Retour">
    <img src="/images/fleches/gauche-blanc.svg" alt="Flèche de retour" class="w-10 h-10">
</a>
@endsection

@section('content')

<div class="m-4">
    <h1 class="text-3xl text-[#7A1E2E]" style="font-family: 'Crimson Text', serif;">
        Créer un cellier
    </h1>
    <p class="text-sm text-gray-600 mt-1 font-roboto">
        Ajoutez un nouveau cellier à votre collection.
    </p>
</div>

<div class="m-4">
    <x-alerts />
</div>

<div class="m-4 border p-4 rounded bg-white font-roboto">
    <form method="POST" action="{{ route('celliers.store') }}" class="flex flex-col gap-5" novalidate>
        @csrf

        @include('celliers._form')

        <div class="flex gap-3 pt-2">
            <button type="submit" class="w-1/2 bg-[#A83248] text-white py-3 rounded font-medium">
                Enregistrer
            </button>

            <a href="{{ route('celliers.index') }}"
                class="w-1/2 text-center border py-3 rounded font-medium">
                Annuler
            </a>
        </div>
    </form>
</div>

@endsection