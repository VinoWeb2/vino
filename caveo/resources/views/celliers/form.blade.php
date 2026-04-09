<div class="space-y-4" style="font-family: 'Roboto', sans-serif;">
    <div>
        <label for="nom" class="block text-sm font-medium text-gray-700 mb-1">
            Nom du cellier
        </label>
        <input
            type="text"
            name="nom"
            id="nom"
            value="{{ old('nom', $cellier->nom ?? '') }}"
            maxlength="75"
            required
            class="w-full rounded-lg border px-4 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-[#7A1E2E] @error('nom') border-red-500 @else border-gray-300 @enderror">
        @error('nom')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="emplacement" class="block text-sm font-medium text-gray-700 mb-1">
            Emplacement
        </label>
        <input
            type="text"
            name="emplacement"
            id="emplacement"
            value="{{ old('emplacement', $cellier->emplacement ?? '') }}"
            maxlength="55"
            class="w-full rounded-lg border px-4 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-[#7A1E2E] @error('emplacement') border-red-500 @else border-gray-300 @enderror">
        @error('emplacement')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
            Description
        </label>
        <textarea
            name="description"
            id="description"
            rows="4"
            class="w-full rounded-lg border px-4 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-[#7A1E2E] @error('description') border-red-500 @else border-gray-300 @enderror">{{ old('description', $cellier->description ?? '') }}</textarea>
        @error('description')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>