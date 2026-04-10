@extends('layouts.main')

@section('title', $cellier->nom)

@section('fleche')
<a href="{{ route('celliers.index') }}" class="text-white text-2xl leading-none" aria-label="Retour">
    ←
</a>
@endsection

@section('content')
<section class="px-4 py-5 pb-48 max-w-5xl mx-auto font-roboto">

    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-3xl text-[#7A1E2E]" style="font-family: 'Crimson Text', serif;">
            {{ $cellier->nom }}
        </h1>

        <div class="mt-3 text-sm text-gray-700 space-y-1">
            @if($cellier->emplacement)
            <p><strong>Emplacement :</strong> {{ $cellier->emplacement }}</p>
            @endif

            @if($cellier->description)
            <p><strong>Description :</strong> {{ $cellier->description }}</p>
            @endif
        </div>

        <div class="mt-4 flex items-center gap-3">
            <a href="{{ route('celliers.index') }}"
                class="px-4 py-2 bg-[#A83248] text-white rounded text-sm">
                Voir les celliers
            </a>

            <a href="{{ route('celliers.edit', $cellier) }}"
                class="text-xs text-gray-500 hover:text-gray-700">
                Modifier
            </a>

            <form method="POST" action="{{ route('celliers.destroy', $cellier) }}">
                @csrf
                @method('DELETE')
                <button class="text-xs text-red-500 hover:text-red-700"
                    onclick="return confirm('Supprimer ce cellier ?')">
                    Supprimer
                </button>
            </form>
        </div>
    </div>

    <x-alerts />

    {{-- Ajouter bouteille --}}
    <div class="mb-6 border p-4 rounded bg-white">
        <h2 class="font-semibold text-lg mb-4">Ajouter une bouteille</h2>

        <button id="openBottleModal"
            class="bg-[#A83248] text-white px-4 py-3 rounded w-full">
            Rechercher une bouteille
        </button>
    </div>

    {{-- Inventaire --}}
    <div class="space-y-4 pb-20">
        @forelse($cellier->inventaires as $inventaire)
        <div class="flex flex-col sm:flex-row gap-4 border p-4 rounded bg-white">

            <div class="w-full sm:w-[90px] flex justify-center">
                <img
                    src="{{ $inventaire->bouteille->image ?? asset('images/bouteille-vide.png') }}"
                    class="h-[130px]">
            </div>

            <div class="flex-1">
                <h2 class="font-semibold">
                    {{ $inventaire->bouteille->nom ?? 'N/A' }}
                </h2>

                <p class="text-sm text-gray-600">
                    {{ $inventaire->bouteille->pays ?? '' }}
                    {{ $inventaire->bouteille->format ?? '' }}
                    {{ $inventaire->bouteille->type ?? '' }}
                </p>

                <p class="mt-2">
                    Quantité :
                    <span class="{{ $inventaire->quantite == 0 ? 'text-red-600 font-bold' : '' }}">
                        {{ $inventaire->quantite }}
                    </span>
                </p>

                <div class="flex gap-3 mt-3">
                    <a href="{{ route('bouteilles.show', $inventaire->bouteille->id) }}"
                        class="px-3 py-1 bg-[#A83248] text-white text-sm rounded">
                        Détail
                    </a>

                    <form method="POST" action="{{ route('inventaires.destroy', $inventaire) }}">
                        @csrf
                        @method('DELETE')
                        <button class="text-xs text-red-500">Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <p>Aucune bouteille.</p>
        @endforelse
    </div>

</section>

{{-- DATA --}}
<div id="bottleModalData"
    data-fallback-image="{{ asset('images/bouteille-vide.png') }}"
    data-csrf-token="{{ csrf_token() }}"
    data-store-url="{{ route('inventaires.store', $cellier) }}">
</div>

{{-- Overlay --}}
<div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 hidden z-40"></div>

{{-- Modal centrée --}}
<div id="modal" class="fixed inset-0 hidden z-50 flex items-center justify-center px-4">

    <div class="bg-white w-full max-w-lg rounded-lg shadow-lg flex flex-col max-h-[90vh]">

        {{-- Header --}}
        <div class="p-4 border-b flex justify-between items-center">
            <h2 class="font-semibold text-lg">Ajouter une bouteille</h2>
            <button id="closeModal" class="text-xl">✕</button>
        </div>

        {{-- Search --}}
        <div class="p-4 border-b">
            <input id="search"
                type="text"
                placeholder="Rechercher une bouteille..."
                class="w-full border p-2 rounded">
        </div>

        {{-- Results --}}
        <div id="results" class="p-4 overflow-y-auto flex-1 space-y-4">
            <p class="text-center text-gray-500">Tape au moins 2 caractères pour rechercher</p>
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {

        const modal = document.getElementById('modal');
        const overlay = document.getElementById('overlay');
        const openBtn = document.getElementById('openBottleModal');
        const closeBtn = document.getElementById('closeModal');
        const search = document.getElementById('search');
        const results = document.getElementById('results');

        const data = document.getElementById('bottleModalData');

        const fallbackImage = data.dataset.fallbackImage;
        const csrf = data.dataset.csrfToken;
        const storeUrl = data.dataset.storeUrl;

        let timer;

        openBtn.onclick = () => {
            overlay.classList.remove('hidden');
            modal.classList.remove('hidden');
            search.focus();
        };

        closeBtn.onclick = close;
        overlay.onclick = close;

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') close();
        });

        function close() {
            overlay.classList.add('hidden');
            modal.classList.add('hidden');
        }

        search.addEventListener('input', () => {
            clearTimeout(timer);
            timer = setTimeout(() => fetchResults(search.value), 300);
        });

        async function fetchResults(q) {

            if (q.length < 2) {
                results.innerHTML = '<p class="text-center text-gray-500">Minimum 2 caractères</p>';
                return;
            }

            results.innerHTML = '<p class="text-center text-gray-500">Chargement...</p>';

            try {
                const res = await fetch(`{{ route('celliers.bouteilles.recherche') }}?q=${encodeURIComponent(q)}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                });

                const data = await res.json();
                render(data);

            } catch {
                results.innerHTML = '<p class="text-center text-red-500">Erreur lors de la recherche</p>';
            }
        }

        function render(list) {

            if (!list.length) {
                results.innerHTML = '<p class="text-center text-gray-500">Aucun résultat</p>';
                return;
            }

            results.innerHTML = '';

            list.forEach(b => {

                const div = document.createElement('div');
                div.className = 'flex gap-3 border p-3 rounded';

                div.innerHTML = `
                <img src="${b.image || fallbackImage}" class="h-[100px]">

                <div class="flex-1">
                    <p class="font-semibold">${b.nom}</p>
                    <p class="text-sm text-gray-600">${b.pays || ''} ${b.format ? '| ' + b.format + ' ml' : ''} ${b.type ? '| ' + b.type : ''}</p>

                    <form method="POST" action="${storeUrl}" class="mt-2 flex gap-2">
                        <input type="hidden" name="_token" value="${csrf}">
                        <input type="hidden" name="id_bouteille" value="${b.id}">
                        <input type="number" name="quantite" value="1" min="1" class="border px-2 w-16 rounded">
                        <button class="bg-[#A83248] text-white px-3 rounded text-sm">
                            Ajouter
                        </button>
                    </form>
                </div>
            `;

                results.appendChild(div);
            });
        }
    });
</script>

@endsection