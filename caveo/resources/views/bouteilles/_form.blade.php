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
        maxlength="255"
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
        class="w-full border-2 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#E0E0E0] @error('type') border-red-600 @enderror"
        placeholder="Ex. Vin rouge"
        maxlength="100">
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
        class="w-full border-2 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#E0E0E0] @error('pays') border-red-600 @enderror"
        placeholder="Ex. France"
        maxlength="100">
    @error('pays')
    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label for="format" class="block mb-1 text-sm font-medium text-[#1A1A1A]">
            Format (ml)
        </label>
        <input type="number"
            id="format"
            name="format"
            value="{{ old('format', $bouteille->format ?? '') }}"
            class="w-full border-2 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#E0E0E0] @error('format') border-red-600 @enderror"
            placeholder="Ex. 750"
            min="1"
            max="9999">
        @error('format')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="millesime" class="block mb-1 text-sm font-medium text-[#1A1A1A]">
            Millésime
        </label>
        <input type="number"
            id="millesime"
            name="millesime"
            value="{{ old('millesime', $bouteille->millesime ?? '') }}"
            class="w-full border-2 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#E0E0E0] @error('millesime') border-red-600 @enderror"
            placeholder="Ex. 2021"
            min="1000"
            max="9999">
        @error('millesime')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label for="cepage" class="block mb-1 text-sm font-medium text-[#1A1A1A]">
            Cépage
        </label>
        <input type="text"
            id="cepage"
            name="cepage"
            value="{{ old('cepage', $bouteille->cepage ?? '') }}"
            class="w-full border-2 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#E0E0E0] @error('cepage') border-red-600 @enderror"
            placeholder="Ex. Merlot"
            maxlength="255">
        @error('cepage')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="prix" class="block mb-1 text-sm font-medium text-[#1A1A1A]">
            Prix
        </label>
        <input type="number"
            step="0.01"
            id="prix"
            name="prix"
            value="{{ old('prix', $bouteille->prix ?? '') }}"
            class="w-full border-2 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#E0E0E0] @error('prix') border-red-600 @enderror"
            placeholder="Ex. 24.95"
            min="0"
            max="99999.99">
        @error('prix')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>

{{-- Image --}}
<div>
    <label for="image" class="block mb-1 text-sm font-medium text-[#1A1A1A]">
        Image de la bouteille
    </label>
    <input type="file"
        id="image"
        name="image"
        accept="image/*"
        class="w-full border-2 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#E0E0E0] @error('image') border-red-600 @enderror">
    @error('image')
    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
    @enderror

    @if(!empty($bouteille->image))
    <div class="mt-3">
        <p class="text-xs text-gray-500 mb-2">Image actuelle :</p>
        <img src="{{ asset('storage/' . $bouteille->image) }}"
            alt="{{ $bouteille->nom }}"
            class="h-28 w-auto rounded border">
    </div>
    @endif
</div>

<div>
    <label class="block mb-2 text-sm font-medium text-[#1A1A1A]">
        Quantité <span class="text-red-500">*</span>
    </label>

    <div class="flex items-center justify-between w-full">
        <button type="button"
            id="quantiteMinus"
            class="w-1/3 flex justify-center py-5"
            aria-label="Diminuer la quantité">
            <img src="{{ asset('images/icons/cercle-moins.svg') }}" class="w-10 h-10" alt="">
        </button>

        <div class="w-1/3 text-center">
            <span id="quantiteDisplay" class="text-2xl font-semibold">
                {{ old('quantite', $quantite ?? 1) }}
            </span>
        </div>

        <button type="button"
            id="quantitePlus"
            class="w-1/3 flex justify-center py-5"
            aria-label="Augmenter la quantité">
            <img src="{{ asset('images/icons/cercle-plus.svg') }}" class="w-10 h-10" alt="">
        </button>
    </div>

    <input type="hidden"
        name="quantite"
        id="quantiteInput"
        value="{{ old('quantite', $quantite ?? 1) }}">

    <p class="text-xs text-gray-500 mt-1">
        Vous pouvez mettre 0 si la bouteille est déjà bue mais que vous souhaitez la conserver dans votre cellier.
    </p>

    @error('quantite')
    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<div>
    <label for="description" class="block mb-1 text-sm font-medium text-[#1A1A1A]">
        Description
    </label>
    <textarea
        id="description"
        name="description"
        rows="4"
        class="w-full border-2 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#E0E0E0] @error('description') border-red-600 @enderror"
        placeholder="Ajoutez quelques notes sur la bouteille...">{{ old('description', $bouteille->description ?? '') }}</textarea>
    @error('description')
    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>