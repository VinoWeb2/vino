@extends('layouts.main')

@section('title', 'Créer un cellier')

@section('fleche')
<a href="{{ route('celliers.index') }}" class="text-white text-2xl leading-none" aria-label="Retour à la liste des celliers">
    ←
</a>
@endsection

@section('content')
<section class="px-4 py-6 pb-32 max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow border border-gray-100 p-5">
        <h2 class="text-3xl text-[#7A1E2E] mb-5" style="font-family: 'Crimson Text', serif;">
            Créer un cellier
        </h2>

        @if ($errors->any())
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-700">
            <p class="font-medium mb-1">Le formulaire contient des erreurs.</p>
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $erreur)
                <li>{{ $erreur }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('celliers.store') }}" method="POST" class="space-y-5" novalidate>
            @csrf

            @include('celliers._form')

            <div class="flex flex-wrap gap-3 pt-2" style="font-family: 'Roboto', sans-serif;">
                <button type="submit"
                    class="bg-[#7A1E2E] text-white px-5 py-2 rounded-lg shadow hover:opacity-90 transition">
                    Enregistrer
                </button>

                <a href="{{ route('celliers.index') }}"
                    class="px-5 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</section>
@endsection