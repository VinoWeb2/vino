@extends('layouts.main')
@section('title', 'caveo')

@section('content')

<script type="module" src="{{ asset('js/filtre.js') }}"></script>

<form method="GET" action="{{ url()->current() }}">
    <div class="m-4">
        <div class="flex gap-2 items-stretch">
            <input type="text" name="recherche"
                value="{{ request('recherche') }}"
                placeholder="Rechercher une bouteille..."
                class="border rounded px-3 h-12 w-full">

            <button type="submit"
                class="bg-[#A83248] text-white px-4 h-12 rounded flex items-center justify-center">
                <img src="{{ asset('images/recherche/recherche-blanc.svg') }}" alt="" class="w-6 h-6">
            </button>
        </div>

        <div class="mt-2">
            <button type="button" id="openFilters"
                class="bg-[#A83248] text-white h-12 rounded w-full font-semibold">
                Filtres
            </button>
        </div>
    </div>

    <!-- overlay -->
    <div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 hidden z-40"></div>

    <!-- Panneau filtres -->
    <div id="filterPanel" class="fixed bottom-0 left-0 right-0 bg-white rounded-t-2xl z-50 max-h-[90vh] flex flex-col translate-y-full transition-transform duration-300">

        <!-- Entête des filtres -->
        <div class="flex justify-between items-center p-4 border-b">
            <h2 class="text-lg font-bold">Filtres</h2>
            <button type="button" id="closeFilters" class="text-xl">✕</button>
        </div>

        <!-- Contenu scrollable -->
        <div class="overflow-y-auto p-4 flex flex-col gap-6">

            <!-- TRI -->
            <div>
                <label class="font-semibold block mb-2 text-sm">Trier</label>
                <div class="flex flex-col gap-2">
                    <label class="flex items-center gap-2 text-sm">
                        <input type="radio" name="tri_nom" value=""
                            {{ request('tri_nom') == '' ? 'checked' : '' }}>
                        Ne pas trier
                    </label>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="radio" name="tri_nom" value="asc"
                            {{ request('tri_nom') == 'asc' ? 'checked' : '' }}>
                        A → Z
                    </label>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="radio" name="tri_nom" value="desc"
                            {{ request('tri_nom') == 'desc' ? 'checked' : '' }}>
                        Z → A
                    </label>
                </div>
            </div>

            <!-- TYPE -->
            <div>
                <label class="font-semibold block mb-2 text-sm">Type</label>
                <div class="flex flex-wrap gap-2">
                    @foreach($types as $type)
                    <label class="flex items-center gap-2 text-sm w-[48%] sm:w-[30%]">
                        <input type="checkbox" name="types[]" value="{{ $type }}"
                            {{ in_array($type, request('types', [])) ? 'checked' : '' }}>
                        {{ $type }}
                    </label>
                    @endforeach
                </div>
            </div>

            <!-- PAYS -->
            <div>
                <label class="font-semibold block mb-2 text-sm">Pays</label>
                <div class="flex flex-wrap gap-2">
                    @foreach($pays as $p)
                    <label class="flex items-center gap-2 text-sm w-[48%] sm:w-[30%]">
                        <input type="checkbox" name="pays[]" value="{{ $p }}"
                            {{ in_array($p, request('pays', [])) ? 'checked' : '' }}>
                        {{ $p }}
                    </label>
                    @endforeach
                </div>
            </div>

            <!-- FORMAT -->
            <div>
                <label class="font-semibold block mb-2 text-sm">Quantité</label>
                <select name="formats" class="w-full border rounded px-3 py-2 text-sm">
                    <option value="">Sélectionner une quantité</option>
                    @foreach($formats as $format)
                    <option value="{{ $format }}" {{ request('formats') == $format ? 'selected' : '' }}>
                        {{ $format }} ml
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- MILLÉSIME -->
            <div>
                <label class="font-semibold block mb-2 text-sm">Millésime</label>
                <select name="millesimes" class="w-full border rounded px-3 py-2 text-sm">
                    <option value="">Sélectionner un millésime</option>
                    @foreach($millesimes as $m)
                    <option value="{{ $m }}" {{ request('millesimes') == $m ? 'selected' : '' }}>
                        {{ $m }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Actions sticky -->
        <div class="sticky bottom-0 bg-white z-50 border-t mb-5">
            <div class="flex flex-row gap-4 pt-4">
                <a href="{{ url()->current() }}?recherche={{ request('recherche') }}"
                    class="w-1/2 text-center border py-3 rounded font-medium text-sm">
                    Réinitialiser
                </a>
                <button type="submit"
                    class="w-1/2 bg-[#A83248] text-white py-3 rounded font-medium text-sm">
                    Appliquer
                </button>
            </div>
        </div>

    </div>
</form>

<div class="m-4">
    @if($bouteilles->total() > 0)
    <p>
        Résultats {{ $bouteilles->firstItem() }}-{{ $bouteilles->lastItem() }} sur {{ $bouteilles->total() }}
    </p>
    @else
    <p></p>
    @endif
</div>

@if($bouteilles->isEmpty())
<div class="mt-[30px] mb-[30px] ml-4 mr-4 p-4 bg-yellow-50 border border-yellow-200 text-yellow-800 rounded text-center">
    Aucune bouteille trouvée
</div>
@else
@foreach($bouteilles as $bouteille)
<div class="flex gap-6 m-4 mb-6 font-roboto border p-4 rounded">
    <div class="w-[90px] flex justify-center items-center">
        <img src="{{ $bouteille->image ?? asset('images/bouteille-vide.png') }}" alt="" class="w-auto h-[135px]">
    </div>

    <div class="flex flex-col justify-between flex-1">
        <div>
            <h2 class="font-semibold text-lg">
                {{ $bouteille->nom }}
            </h2>

            <div class="flex items-center text-sm text-gray-600 space-x-2">
                <p>{{ $bouteille->pays ?? "" }}</p>
                <span>|</span>
                <p>{{ $bouteille->format ?? "" }} ml</p>
                <span>|</span>
                <p>{{ $bouteille->type ?? "" }}</p>
            </div>

            <p class="mt-2 font-medium mb-3">
                {{ $bouteille->prix ?? "Non spécifié" }} $
            </p>
        </div>

        <div class="flex gap-2 flex-wrap">
            <a href="{{ route('bouteilles.show', $bouteille->id) }}?source=catalogue"
                class="px-4 py-2 bg-[#A83248] text-white rounded text-sm font-medium w-max">
                Détail
            </a>

            @if($celliers->isNotEmpty())
            <button type="button"
                class="px-2 py-1 text-sm border border-gray-300 text-gray-600 rounded hover:bg-gray-100 openAddToCellierModal"
                data-bouteille-id="{{ $bouteille->id }}"
                data-bouteille-nom="{{ $bouteille->nom }}">
                Ajouter au cellier
            </button>
            @else
            <a href="{{ route('celliers.create') }}"
                class="px-4 py-2 bg-gray-200 text-gray-500 rounded w-max">
                Créer un cellier
            </a>
            @endif
        </div>
    </div>
</div>
@endforeach
@endif

@if($bouteilles->total() > 0)
<div class="flex justify-between items-center mx-auto my-5 mb-24">
    @if ($bouteilles->onFirstPage())
    <span>
        <img src="{{ asset('images/fleches/gauche-gris.svg') }}" class="w-14" alt="gauche bloqué">
    </span>
    @else
    <a href="{{ $bouteilles->previousPageUrl() }}">
        <img src="{{ asset('images/fleches/gauche-rouge.svg') }}" class="w-14" alt="gauche">
    </a>
    @endif

    <p>
        Résultats {{ $bouteilles->firstItem() }}-{{ $bouteilles->lastItem() }} sur {{ $bouteilles->total() }}
    </p>

    @if ($bouteilles->hasMorePages())
    <a href="{{ $bouteilles->nextPageUrl() }}">
        <img src="{{ asset('images/fleches/droit-rouge.svg') }}" class="w-14" alt="droite">
    </a>
    @else
    <span>
        <img src="{{ asset('images/fleches/droit-gris.svg') }}" class="w-14" alt="droite bloqué">
    </span>
    @endif
</div>
@else
<p></p>
@endif

@if($celliers->isNotEmpty())
{{-- Overlay modale --}}
<div id="cellierOverlay" class="fixed inset-0 bg-black bg-opacity-50 hidden z-40"></div>

{{-- Modale ajout au cellier --}}
<div id="cellierModal" class="fixed inset-0 hidden z-50 flex items-center justify-center px-4">
    <div class="bg-white w-full max-w-md rounded-lg shadow-lg">

        <div class="p-4 border-b flex justify-between items-center">
            <h2 class="font-semibold text-lg">Ajouter au cellier</h2>
            <button type="button" id="closeCellierModal" class="text-xl">✕</button>
        </div>

        <form id="addToCellierForm" method="POST" class="p-4 flex flex-col gap-4">
            @csrf

            <input type="hidden" name="id_bouteille" id="modalBouteilleId">

            <div>
                <p class="text-sm text-gray-600 mb-2">Bouteille sélectionnée :</p>
                <p id="modalBouteilleNom" class="font-medium text-[#1A1A1A]"></p>
            </div>

            <div>
                <label for="modalCellierId" class="block mb-1 text-sm font-medium text-[#1A1A1A]">
                    Choisir un cellier
                </label>
                <select name="id_cellier" id="modalCellierId" class="w-full border rounded px-3 py-2" required>
                    @foreach($celliers as $cellier)
                    <option value="{{ $cellier->id }}">{{ $cellier->nom }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="modalQuantite" class="block mb-1 text-sm font-medium text-[#1A1A1A]">
                    Quantité
                </label>
                <input type="number" name="quantite" id="modalQuantite" value="1" min="1"
                    class="w-full border rounded px-3 py-2" required>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="w-1/2 bg-[#A83248] text-white py-3 rounded font-medium">
                    Ajouter
                </button>

                <button type="button" id="cancelCellierModal"
                    class="w-1/2 text-center border py-3 rounded font-medium">
                    Annuler
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const overlay = document.getElementById('cellierOverlay');
        const modal = document.getElementById('cellierModal');
        const closeBtn = document.getElementById('closeCellierModal');
        const cancelBtn = document.getElementById('cancelCellierModal');
        const form = document.getElementById('addToCellierForm');

        const bouteilleIdInput = document.getElementById('modalBouteilleId');
        const bouteilleNomText = document.getElementById('modalBouteilleNom');
        const cellierSelect = document.getElementById('modalCellierId');

        document.querySelectorAll('.openAddToCellierModal').forEach(button => {
            button.addEventListener('click', () => {
                const bouteilleId = button.dataset.bouteilleId;
                const bouteilleNom = button.dataset.bouteilleNom;

                bouteilleIdInput.value = bouteilleId;
                bouteilleNomText.textContent = bouteilleNom;
                form.action = `/celliers/${cellierSelect.value}/inventaires`;

                overlay.classList.remove('hidden');
                modal.classList.remove('hidden');
            });
        });

        cellierSelect.addEventListener('change', () => {
            form.action = `/celliers/${cellierSelect.value}/inventaires`;
        });

        function closeModal() {
            overlay.classList.add('hidden');
            modal.classList.add('hidden');
        }

        closeBtn.addEventListener('click', closeModal);
        cancelBtn.addEventListener('click', closeModal);
        overlay.addEventListener('click', closeModal);

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeModal();
        });
    });
</script>
@endif

@endsection