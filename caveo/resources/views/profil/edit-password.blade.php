@extends('layouts.main')
@section('title', 'Modification du Mot de passe')
@section('content')
@section('fleche')
<a href="{{ route('profil.edit') }}" class="text-white text-2xl leading-none" aria-label="Retour">
    <img src="/images/fleches/gauche-blanc.svg" alt="Flèche de retour" class="w-10 h-10">
</a>
@endsection

    <div class="h-[calc(100vh-90px)] py-6 px-4 sm:px-6 lg:px-8 pb-24">
        <h1 class="text-3xl text-[#7A1E2E] text-center" style="font-family: 'Crimson Text', serif;">Modifier mot de passe</h1>
        <form method="POST" action="{{ route('profil.password.update') }}">
            @csrf
            <h3 class="text-xl font-medium mt-1">Mot de passe</h3>
            <div class="border rounded-lg shadow p-2 bg-white">
                <div class="p-2 flex flex-col">
                    <div class="mb-2">
                        <label id="ancien_mot_de_passe" for="ancien_mot_de_passe" class="block text-md font-medium text-[#1A1A1A]">
                            Mot de passe actuel</label>
                        <input type="password" name="ancien_mot_de_passe" 
                                placeholder="••••••••••••••••" required
                                class="block w-full border-2 text-[#1A1A1A] rounded-lg p-1 focus:outline-none focus:ring-1 focus:ring-[#A83248] focus:border-[#A83248] @error ('ancien_mot_de_passe') border-red-600 @enderror">
                        @error('ancien_mot_de_passe')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-2">
                        <label id="nouveau_mot_de_passe" for="nouveau_mot_de_passe" class="block text-md font-medium text-[#1A1A1A]">
                            Nouveau mot de passe</label>
                        <input type="password" name="nouveau_mot_de_passe" 
                                placeholder="••••••••••••••••" required
                                class="block w-full border-2 text-[#1A1A1A] rounded-lg p-1 focus:outline-none focus:ring-1 focus:ring-[#A83248] focus:border-[#A83248] @error ('nouveau_mot_de_passe') border-red-600 @enderror">
                        @error('nouveau_mot_de_passe')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-2">
                        <label id="nouveau_mot_de_passe_confirmation" for="nouveau_mot_de_passe_confirmation" class="block text-md font-medium text-[#1A1A1A]">
                            Confirmer mot de passe</label>
                        <input type="password" name="nouveau_mot_de_passe_confirmation" 
                                placeholder="••••••••••••••••" required
                                class="block w-full border-2 text-[#1A1A1A] rounded-lg p-1 focus:outline-none focus:ring-1 focus:ring-[#A83248] focus:border-[#A83248] @error ('nouveau_mot_de_passe_confirmation') border-red-600 @enderror">
                        @error('nouveau_mot_de_passe_confirmation')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                </div>
            </div>
                <div class="flex justify-center mt-8">
                    <button type="submit" 
                            class="w-2/5 max-w-xs p-1 flex items-center justify-center border bg-[#7A1E2E] border-[#7A1E2E]  text-white rounded-md shadow">
                        Sauvegarder
                    </button>
                </div>
        </form>
    </div>

@endsection