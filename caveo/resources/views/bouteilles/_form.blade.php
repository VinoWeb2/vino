<div>
    <label for="nom" class="block mb-1 text-sm font-medium text-[#1A1A1A]">
        Nom de la bouteille <span class="text-red-500">*</span>
    </label>
    <input type="text"
        id="nom"
        name="nom"
        value="{{ old('nom', $bouteille->nom ?? '') }}"
        class="w-full border-2 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#E0E0E0] @error('nom') border-red-600 @enderror"
        placeholder="Ex. Château Exemple"
        required>
    @error('nom')
    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<div>
    <label for="type" class="block mb-1 text-sm font-medium text-[#1A1A1A]">
        Type
    </label>
    <input type="text"
        id="type"
        name="type"
        value="{{ old('type', $bouteille->type ?? '') }}"
        class="w-full border-2 p-2 rounded-lg @error('type') border-red-600 @enderror"
        placeholder="Ex. Vin rouge">
    @error('type')
    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<div>
    <label for="pays" class="block mb-1 text-sm font-medium text-[#1A1A1A]">
        Pays
    </label>
    <input type="text"
        id="pays"
        name="pays"
        value="{{ old('pays', $bouteille->pays ?? '') }}"
        class="w-full border-2 p-2 rounded-lg @error('pays') border-red-600 @enderror">
    @error('pays')
    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="grid grid-cols-2 gap-4">
    <div>
        <label for="format" class="block mb-1 text-sm font-medium">Format (ml)</label>
        <input type="number"
            id="format"
            name="format"
            value="{{ old('format', $bouteille->format ?? '') }}"
            class="w-full border-2 p-2 rounded-lg @error('format') border-red-600 @enderror">
        @error('format')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="millesime" class="block mb-1 text-sm font-medium">Millésime</label>
        <input type="number"
            id="millesime"
            name="millesime"
            value="{{ old('millesime', $bouteille->millesime ?? '') }}"
            class="w-full border-2 p-2 rounded-lg @error('millesime') border-red-600 @enderror">
        @error('millesime')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="grid grid-cols-2 gap-4">
    <div>
        <label for="cepage" class="block mb-1 text-sm font-medium">Cépage</label>
        <input type="text"
            id="cepage"
            name="cepage"
            value="{{ old('cepage', $bouteille->cepage ?? '') }}"
            class="w-full border-2 p-2 rounded-lg @error('cepage') border-red-600 @enderror">
        @error('cepage')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="prix" class="block mb-1 text-sm font-medium">Prix</label>
        <input type="number"
            step="0.01"
            id="prix"
            name="prix"
            value="{{ old('prix', $bouteille->prix ?? '') }}"
            class="w-full border-2 p-2 rounded-lg @error('prix') border-red-600 @enderror">
        @error('prix')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>

<div>
    <label for="image" class="block mb-1 text-sm font-medium">
        Image
    </label>
    <input type="file"
        id="image"
        name="image"
        accept="image/*"
        class="w-full border-2 p-2 rounded-lg @error('image') border-red-600 @enderror">

    @error('image')
    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<div>
    <label class="block mb-2 text-sm font-medium">
        Quantité <span class="text-red-500">*</span>
    </label>

    <div class="flex items-center justify-between">
        <button type="button" id="quantiteMinus">
            <img src="{{ asset('images/icons/cercle-moins.svg') }}" class="w-10 h-10">
        </button>

        <span id="quantiteDisplay" class="text-xl font-semibold">
            {{ old('quantite', $quantite ?? 1) }}
        </span>

        <button type="button" id="quantitePlus">
            <img src="{{ asset('images/icons/cercle-plus.svg') }}" class="w-10 h-10">
        </button>
    </div>

    <input type="hidden"
        name="quantite"
        id="quantiteInput"
        value="{{ old('quantite', $quantite ?? 1) }}">

    @error('quantite')
    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<div>
    <label for="description" class="block mb-1 text-sm font-medium">
        Description
    </label>
    <textarea
        id="description"
        name="description"
        rows="3"
        class="w-full border-2 p-2 rounded-lg @error('description') border-red-600 @enderror">{{ old('description', $bouteille->description ?? '') }}</textarea>

    @error('description')
    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>