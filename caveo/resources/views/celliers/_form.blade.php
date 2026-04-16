<div class="flex flex-col gap-4 font-roboto">
    <div>
        <label for="nom" class="block mb-2 font-semibold text-[#1A1A1A]">
            Nom du cellier
        </label>
        <input
            type="text"
            id="nom"
            name="nom"
            value="{{ old('nom', $cellier->nom ?? '') }}"
            placeholder="Ex. Cellier principal"
            class="w-full border rounded px-3 py-3 @error('nom') border-red-500 @enderror">

        @error('nom')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="emplacement" class="block mb-2 font-semibold text-[#1A1A1A]">
            Emplacement
        </label>
        <input
            type="text"
            id="emplacement"
            name="emplacement"
            value="{{ old('emplacement', $cellier->emplacement ?? '') }}"
            placeholder="Ex. Sous-sol"
            class="w-full border rounded px-3 py-3 @error('emplacement') border-red-500 @enderror">

        @error('emplacement')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="description" class="block mb-2 font-semibold text-[#1A1A1A]">
            Description
        </label>
        <textarea
            id="description"
            name="description"
            rows="4"
            placeholder="Ajoutez une courte description"
            class="w-full border rounded px-3 py-3 @error('description') border-red-500 @enderror">{{ old('description', $cellier->description ?? '') }}</textarea>

        @error('description')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

</div>