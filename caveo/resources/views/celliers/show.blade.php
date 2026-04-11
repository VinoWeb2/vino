@extends('layouts.main')

@section('title', $cellier->nom)

@section('fleche')
<a href="{{ route('celliers.index') }}" class="text-white text-2xl leading-none" aria-label="Retour">
    <img src="/images/fleches/gauche-blanc.svg" alt="Flèche de retour" class="w-10 h-10">
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

                {{-- Contrôle quantité avec icônes --}}
                <div class="mt-4 flex items-center justify-between w-full">

                    <!-- Moins -->
                    <form method="POST"
                        action="{{ route('inventaires.updateQuantite', $inventaire) }}"
                        class="w-1/3">
                        @csrf
                        @method('PATCH')

                        <input type="hidden" name="quantite" value="{{ max(0, $inventaire->quantite - 1) }}">

                        <button type="submit"
                            class="w-full flex items-center justify-center py-5 rounded-lg hover:bg-gray-100 active:scale-95 transition"
                            aria-label="Diminuer la quantité">
                            <img src="{{ asset('images/icons/cercle-moins.svg') }}"
                                alt=""
                                aria-hidden="true"
                                class="w-10 h-10">
                        </button>
                    </form>

                    <!-- Quantité -->
                    <div class="w-1/3 text-center">
                        <span class="text-2xl font-semibold">
                            {{ $inventaire->quantite }}
                        </span>
                    </div>

                    <!-- Plus -->
                    <form method="POST"
                        action="{{ route('inventaires.updateQuantite', $inventaire) }}"
                        class="w-1/3">
                        @csrf
                        @method('PATCH')

                        <input type="hidden" name="quantite" value="{{ min(999, $inventaire->quantite + 1) }}">

                        <button type="submit"
                            class="w-full flex items-center justify-center py-5 rounded-lg hover:bg-gray-100 active:scale-95 transition"
                            aria-label="Augmenter la quantité">
                            <img src="{{ asset('images/icons/cercle-plus.svg') }}"
                                alt=""
                                aria-hidden="true"
                                class="w-10 h-10">
                        </button>
                    </form>

                </div>

                <p class="text-xs text-gray-500 mt-1">
                    La quantité peut être à 0 si la bouteille a été bue.
                </p>

                {{-- Actions bouteille --}}
                <div class="mt-4 flex gap-3">
                    <!-- Bouton Détail -->
                    @if($inventaire->bouteille)
                    <a href="{{ route('bouteilles.show', $inventaire->bouteille->id) }}?source=cellier"
                        class="w-1/2 text-center bg-[#A83248] text-white py-2 rounded text-sm font-medium">
                        Détail
                    </a>
                    @endif

                    <!-- Bouton Supprimer -->
                    <form method="POST"
                        action="{{ route('inventaires.destroy', $inventaire) }}"
                        class="w-1/2">
                        @csrf
                        @method('DELETE')

                        <button type="submit"
                            onclick="return confirm('Supprimer cette bouteille ?')"
                            class="w-full text-center border border-red-300 text-red-600 py-2 rounded text-sm font-medium hover:bg-red-50 transition">
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
</script>

@endsection