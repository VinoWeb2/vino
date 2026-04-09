@extends('layouts.main')

@section('title', $cellier->nom)

@section('fleche')
<a href="{{ route('celliers.index') }}" class="text-white text-2xl leading-none" aria-label="Retour aux celliers">
    ←
</a>
@endsection

@section('content')
<section class="px-4 py-6 pb-32 max-w-6xl mx-auto" style="font-family: 'Roboto', sans-serif;">
    <div class="bg-white rounded-2xl shadow border border-gray-100 p-5 mb-5">
        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
            <div>
                <h2 class="text-3xl text-[#7A1E2E]" style="font-family: 'Crimson Text', serif;">
                    {{ $cellier->nom }}
                </h2>

                <p class="mt-3 text-sm text-gray-700">
                    <span class="font-medium">Emplacement :</span>
                    {{ $cellier->emplacement ?? 'Non précisé' }}
                </p>

                <p class="mt-1 text-sm text-gray-700">
                    <span class="font-medium">Description :</span>
                    {{ $cellier->description ?? 'Aucune description' }}
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('celliers.edit', $cellier) }}"
                    class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition text-sm">
                    Modifier
                </a>

                <a href="{{ route('celliers.index') }}"
                    class="px-4 py-2 rounded-lg border border-[#7A1E2E] text-[#7A1E2E] hover:bg-[#7A1E2E] hover:text-white transition text-sm">
                    Retour
                </a>
            </div>
        </div>
    </div>

    @if(session('status'))
    <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-700">
        {{ session('status') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-700">
        <p class="font-medium mb-1">Certaines informations sont invalides.</p>
        <ul class="list-disc list-inside text-sm">
            @foreach ($errors->all() as $erreur)
            <li>{{ $erreur }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="grid gap-5 lg:grid-cols-3">
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow border border-gray-100 p-5">
                <h3 class="text-2xl text-[#7A1E2E] mb-4" style="font-family: 'Crimson Text', serif;">
                    Ajouter une bouteille
                </h3>

                <form action="{{ route('inventaires.store', $cellier) }}" method="POST" class="space-y-4" novalidate>
                    @csrf

                    <div>
                        <label for="id_bouteille" class="block text-sm font-medium text-gray-700 mb-1">
                            Bouteille
                        </label>
                        <select
                            name="id_bouteille"
                            id="id_bouteille"
                            required
                            class="w-full rounded-lg border px-4 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-[#7A1E2E] @error('id_bouteille') border-red-500 @else border-gray-300 @enderror">
                            <option value="">Choisir une bouteille</option>
                            @foreach($bouteilles as $bouteille)
                            <option value="{{ $bouteille->id }}" {{ old('id_bouteille') == $bouteille->id ? 'selected' : '' }}>
                                {{ $bouteille->nom }}
                                @if(!empty($bouteille->millesime))
                                ({{ $bouteille->millesime }})
                                @endif
                            </option>
                            @endforeach
                        </select>
                        @error('id_bouteille')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="quantite" class="block text-sm font-medium text-gray-700 mb-1">
                            Quantité
                        </label>
                        <input
                            type="number"
                            name="quantite"
                            id="quantite"
                            value="{{ old('quantite', 1) }}"
                            min="1"
                            class="w-full rounded-lg border px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[#7A1E2E] @error('quantite') border-red-500 @else border-gray-300 @enderror">
                        @error('quantite')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                            Note
                        </label>
                        <textarea
                            name="description"
                            id="description"
                            rows="3"
                            class="w-full rounded-lg border px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[#7A1E2E] @error('description') border-red-500 @else border-gray-300 @enderror">{{ old('description') }}</textarea>
                        @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                        class="w-full bg-[#7A1E2E] text-white px-4 py-2 rounded-lg shadow hover:opacity-90 transition">
                        Ajouter au cellier
                    </button>
                </form>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="text-2xl text-[#7A1E2E]" style="font-family: 'Crimson Text', serif;">
                        Inventaire du cellier
                    </h3>
                </div>

                @if($cellier->inventaires->isEmpty())
                <div class="p-5">
                    <p class="text-gray-700">Aucune bouteille dans ce cellier pour le moment.</p>
                </div>
                @else
                <div class="divide-y divide-gray-100">
                    @foreach($cellier->inventaires as $inventaire)
                    <article class="p-5">
                        <div class="flex flex-col gap-4">
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900">
                                    {{ $inventaire->bouteille->nom ?? 'Bouteille introuvable' }}
                                </h4>

                                <div class="mt-1 text-sm text-gray-600 space-y-1">
                                    <p><span class="font-medium">Type :</span> {{ $inventaire->bouteille->type ?? '-' }}</p>
                                    <p><span class="font-medium">Pays :</span> {{ $inventaire->bouteille->pays ?? '-' }}</p>
                                    <p><span class="font-medium">Millésime :</span> {{ $inventaire->bouteille->millesime ?? '-' }}</p>
                                </div>
                            </div>

                            <div class="grid gap-4 md:grid-cols-2">
                                <form action="{{ route('inventaires.update', $inventaire) }}" method="POST" class="space-y-2">
                                    @csrf
                                    @method('PUT')

                                    <label class="block text-sm font-medium text-gray-700">
                                        Quantité
                                    </label>

                                    <div class="flex gap-2">
                                        <input
                                            type="number"
                                            name="quantite"
                                            value="{{ $inventaire->quantite }}"
                                            min="0"
                                            class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[#7A1E2E]">

                                        <input type="hidden" name="description" value="{{ $inventaire->description }}">

                                        <button type="submit"
                                            class="px-4 py-2 rounded-lg border border-[#7A1E2E] text-[#7A1E2E] hover:bg-[#7A1E2E] hover:text-white transition">
                                            OK
                                        </button>
                                    </div>

                                    <p class="text-xs text-gray-500">
                                        Mets 0 pour retirer la bouteille du cellier.
                                    </p>
                                </form>

                                <form action="{{ route('inventaires.update', $inventaire) }}" method="POST" class="space-y-2">
                                    @csrf
                                    @method('PUT')

                                    <label class="block text-sm font-medium text-gray-700">
                                        Note
                                    </label>

                                    <div class="flex gap-2">
                                        <input type="hidden" name="quantite" value="{{ $inventaire->quantite }}">

                                        <input
                                            type="text"
                                            name="description"
                                            value="{{ $inventaire->description }}"
                                            placeholder="Ajouter une note"
                                            class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[#7A1E2E]">

                                        <button type="submit"
                                            class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition">
                                            OK
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <div>
                                <form action="{{ route('inventaires.destroy', $inventaire) }}" method="POST">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                        onclick="return confirm('Voulez-vous vraiment supprimer cette bouteille du cellier ?')"
                                        class="px-4 py-2 rounded-lg border border-red-300 text-red-600 hover:bg-red-50 transition text-sm">
                                        Supprimer la bouteille
                                    </button>
                                </form>
                            </div>
                        </div>
                    </article>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection