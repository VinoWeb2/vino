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

        {{-- Actions cellier --}}
        <div class="mt-4 flex flex-wrap gap-y-2">
            <a href="{{ route('celliers.index') }}"
                class="px-4 py-2 bg-[#A83248] text-white rounded text-sm">
                Voir les celliers
            </a>

            <div class="w-full flex items-center gap-4">
                <a href="{{ route('celliers.edit', $cellier) }}"
                    class="text-xs leading-none text-gray-500 hover:text-gray-700">
                    Modifier
                </a>

                <form method="POST"
                    action="{{ route('celliers.destroy', $cellier) }}"
                    class="m-0 p-0 inline-flex">
                    @csrf
                    @method('DELETE')

                    <button type="submit"
                        onclick="return confirm('Supprimer ce cellier ?')"
                        class="text-xs leading-none text-red-500 hover:text-red-700">
                        Supprimer
                    </button>
                </form>
            </div>
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

            {{-- Image --}}
            <div class="w-full sm:w-[90px] flex justify-center">
                <img
                    src="{{ $inventaire->bouteille->image ?? asset('images/bouteille-vide.png') }}"
                    alt="{{ $inventaire->bouteille->nom ?? 'Bouteille' }}"
                    class="h-[130px]">
            </div>

            {{-- Contenu --}}
            <div class="flex-1">
                <div class="flex justify-between items-start gap-3">
                    <h2 class="font-semibold">
                        {{ $inventaire->bouteille->nom ?? 'N/A' }}
                    </h2>

                    @if($inventaire->quantite == 0)
                    <span class="bg-red-100 text-red-600 text-xs px-2 py-1 rounded-full shrink-0">
                        Bue
                    </span>
                    @endif
                </div>

                <p class="text-sm text-gray-600">
                    {{ $inventaire->bouteille->pays ?? '' }}
                    @if(!empty($inventaire->bouteille->pays) && !empty($inventaire->bouteille->format)) | @endif
                    {{ $inventaire->bouteille->format ?? '' }}@if(!empty($inventaire->bouteille->format)) ml @endif
                    @if((!empty($inventaire->bouteille->pays) || !empty($inventaire->bouteille->format)) && !empty($inventaire->bouteille->type)) | @endif
                    {{ $inventaire->bouteille->type ?? '' }}
                </p>

                <p class="mt-2">
                    Quantité :
                    <span class="{{ $inventaire->quantite == 0 ? 'text-red-600 font-bold' : '' }}">
                        {{ $inventaire->quantite }}
                    </span>
                </p>

                @if($inventaire->quantite == 0)
                <p class="text-xs text-red-500 mt-1">
                    Cette bouteille est conservée dans le cellier, mais elle a été bue.
                </p>
                @endif

                {{-- Contrôle quantité + / - --}}
                <form method="POST"
                    action="{{ route('inventaires.update', $inventaire) }}"
                    class="mt-3 flex items-center gap-2">
                    @csrf
                    @method('PUT')

                    <button type="button"
                        onclick="updateQty(this, -1)"
                        class="px-3 py-1 border rounded">
                        −
                    </button>

                    <input type="hidden"
                        name="quantite"
                        value="{{ $inventaire->quantite }}"
                        class="qty-input">

                    <span class="w-6 text-center font-semibold qty-display">
                        {{ $inventaire->quantite }}
                    </span>

                    <button type="button"
                        onclick="updateQty(this, 1)"
                        class="px-3 py-1 border rounded">
                        +
                    </button>

                    <button type="submit"
                        class="ml-2 px-3 py-1 bg-[#A83248] text-white text-sm rounded">
                        OK
                    </button>
                </form>

                <p class="text-xs text-gray-500 mt-1">
                    Mets 0 si la bouteille a été bue.
                </p>

                {{-- Actions bouteille --}}
                <div class="flex gap-3 mt-3 items-center">
                    @if($inventaire->bouteille)
                    <a href="{{ route('bouteilles.show', $inventaire->bouteille->id) }}"
                        class="px-3 py-1 bg-[#A83248] text-white text-sm rounded">
                        Détail
                    </a>
                    @endif

                    <form method="POST"
                        action="{{ route('inventaires.destroy', $inventaire) }}"
                        class="inline-flex">
                        @csrf
                        @method('DELETE')

                        <button type="submit"
                            onclick="return confirm('Supprimer cette bouteille ?')"
                            class="text-xs text-red-500">
                            Supprimer
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <p>Aucune bouteille.</p>
        @endforelse
    </div>

</section>

{{-- Données pour JS --}}
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

        {{-- Header modal --}}
        <div class="p-4 border-b flex justify-between items-center">
            <h2 class="font-semibold text-lg">Ajouter une bouteille</h2>
            <button id="closeModal" class="text-xl">✕</button>
        </div>

        {{-- Recherche --}}
        <div class="p-4 border-b">
            <input id="search"
                type="text"
                placeholder="Rechercher une bouteille..."
                class="w-full border p-2 rounded">
        </div>

        {{-- Résultats --}}
        <div id="results" class="p-4 overflow-y-auto flex-1 space-y-4">
            <p class="text-center text-gray-500">
                Tape au moins 2 caractères pour rechercher
            </p>
        </div>
    </div>
</div>

<script>
    /**
     * Initialise la modale de recherche de bouteilles
     * ainsi que les interactions utilisateur.
     */
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

        /**
         * Ouvre la modale.
         */
        openBtn.onclick = () => {
            overlay.classList.remove('hidden');
            modal.classList.remove('hidden');
            search.focus();
        };

        /**
         * Ferme la modale.
         */
        function close() {
            overlay.classList.add('hidden');
            modal.classList.add('hidden');
        }

        closeBtn.onclick = close;
        overlay.onclick = close;

        /**
         * Ferme la modale avec la touche Échap.
         */
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') close();
        });

        /**
         * Lance la recherche avec debounce.
         */
        search.addEventListener('input', () => {
            clearTimeout(timer);
            timer = setTimeout(() => fetchResults(search.value), 300);
        });

        /**
         * Recherche des bouteilles via AJAX.
         *
         * @param {string} q
         */
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

        /**
         * Affiche les résultats de recherche dans la modale.
         *
         * @param {Array} list
         */
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
                <img src="${b.image || fallbackImage}" alt="${b.nom}" class="h-[100px]">

                <div class="flex-1">
                    <p class="font-semibold">${b.nom}</p>
                    <p class="text-sm text-gray-600">
                        ${b.pays || ''}
                        ${b.format ? '| ' + b.format + ' ml' : ''}
                        ${b.type ? '| ' + b.type : ''}
                    </p>

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

    /**
     * Met à jour la quantité via les boutons + et -.
     *
     * @param {HTMLElement} btn
     * @param {number} delta
     */
    function updateQty(btn, delta) {
        const form = btn.closest('form');
        const input = form.querySelector('.qty-input');
        const display = form.querySelector('.qty-display');

        let value = parseInt(input.value, 10);
        value += delta;

        if (value < 0) value = 0;
        if (value > 999) value = 999;

        input.value = value;
        display.textContent = value;
    }
</script>

@endsection