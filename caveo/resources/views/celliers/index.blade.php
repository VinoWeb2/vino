@extends('layouts.main')

@section('title', 'Mes celliers')

@section('content')
<section class="px-4 py-6 pb-32 max-w-5xl mx-auto" style="font-family: 'Roboto', sans-serif;">
    <div class="flex items-start justify-between gap-4 mb-6">
        <div>
            <h2 class="text-3xl text-[#7A1E2E]" style="font-family: 'Crimson Text', serif;">
                Mes celliers
            </h2>
            <p class="text-sm text-gray-600 mt-1">
                Consultez, modifiez et gérez vos celliers personnels.
            </p>
        </div>

        <a href="{{ route('celliers.create') }}"
            class="shrink-0 bg-[#7A1E2E] text-white px-4 py-2 rounded-lg shadow hover:opacity-90 transition">
            Nouveau cellier
        </a>
    </div>

    @if(session('status'))
    <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-700">
        {{ session('status') }}
    </div>
    @endif

    @if(session('success'))
    <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-700">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-700">
        {{ session('error') }}
    </div>
    @endif

    @if($celliers->isEmpty())
    <div class="bg-white rounded-2xl shadow border border-gray-100 p-5">
        <p class="text-gray-700">Vous n’avez encore aucun cellier.</p>
    </div>
    @else
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($celliers as $cellier)
        <article class="bg-white rounded-2xl shadow border border-gray-100 p-5">
            <h3 class="text-2xl text-[#7A1E2E] mb-3 break-words" style="font-family: 'Crimson Text', serif;">
                {{ $cellier->nom }}
            </h3>

            <div class="space-y-2 text-sm text-gray-700">
                <p>
                    <span class="font-medium">Emplacement :</span>
                    {{ $cellier->emplacement ?? 'Non précisé' }}
                </p>

                <p>
                    <span class="font-medium">Description :</span>
                    {{ $cellier->description ?? 'Aucune description' }}
                </p>

                <p>
                    <span class="font-medium">Entrées d’inventaire :</span>
                    {{ $cellier->inventaires_count ?? 0 }}
                </p>
            </div>

            <div class="mt-5 flex flex-wrap gap-2">
                <a href="{{ route('celliers.show', $cellier) }}"
                    class="px-3 py-2 rounded-lg border border-[#7A1E2E] text-[#7A1E2E] hover:bg-[#7A1E2E] hover:text-white transition text-sm">
                    Voir
                </a>

                <a href="{{ route('celliers.edit', $cellier) }}"
                    class="px-3 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition text-sm">
                    Modifier
                </a>

                <form action="{{ route('celliers.destroy', $cellier) }}" method="POST">
                    @csrf
                    @method('DELETE')

                    <button type="submit"
                        onclick="return confirm('Voulez-vous vraiment supprimer ce cellier ? Cette action est irréversible.')"
                        class="px-3 py-2 rounded-lg border border-red-300 text-red-600 hover:bg-red-50 transition text-sm">
                        Supprimer
                    </button>
                </form>
            </div>
        </article>
        @endforeach
    </div>
    @endif
</section>
@endsection