@extends('layouts.main')
@section('title', 'Modification du profil')
@section('content')
@section('fleche')
<a href="{{ route('profil.show') }}" class="text-white text-2xl leading-none" aria-label="Retour">
    <img src="/images/fleches/gauche-blanc.svg" alt="Flèche de retour" class="w-10 h-10">
</a>
@endsection
    <script type="module" src="{{ asset('js/message-flash-auto.js') }}"></script>

    <div class="h-[calc(100vh-90px)] py-6 px-4 sm:px-6 lg:px-8 pb-24">
        <h1 class="text-3xl text-[#7A1E2E] text-center" style="font-family: 'Crimson Text', serif;">Modifier mon profil</h1>
        @if(session('success'))
        <x-alerts />
        @endif
        <form method="POST" action="{{ route('profil.update') }}">
            @csrf
            <h3 class="text-xl font-medium mt-1">Informations personnelles</h3>
            <div class="border rounded-lg shadow p-2 bg-white">
                <div class="p-2 flex flex-col">
                    <div class="mb-2">
                        <label id="prenom" for="prenom" class="block text-md font-medium text-gray-500">
                            Prénom</label>
                        <input type="text" name="prenom" 
                                value="{{ $user->prenom }}" 
                                class="block w-full border-2 text-[#1A1A1A] rounded-lg p-1 focus:outline-none focus:ring-1 focus:ring-[#A83248] focus:border-[#A83248] @error ('prenom') border-red-600 @enderror">
                        @error('prenom')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-2">
                        <label id="nom" for="nom" class="block text-md font-medium text-gray-500">
                            Nom</label>
                        <input type="text" name="nom" 
                                value="{{ $user->nom }}" 
                                class="block w-full border-2 text-[#1A1A1A] rounded-lg p-1 focus:outline-none focus:ring-1 focus:ring-[#A83248] focus:border-[#A83248] @error ('nom') border-red-600 @enderror">
                        @error('nom')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            <h3 class="text-xl font-medium mt-5">Informations de connexion</h3>
                <div class="border rounded-lg shadow p-2 bg-white">
                    <div class="p-2">
                        <div class="mb-2">
                            <label for="email" class="block text-md font-medium text-gray-500">
                                Adresse courriel</label>
                            <input id="email" type="email" name="email" 
                                    value="{{ $user->email }}" 
                                    class="block w-full border-2 text-[#1A1A1A] rounded-lg p-1 focus:outline-none focus:ring-1 focus:ring-[#A83248] focus:border-[#A83248] @error ('email') border-red-600 @enderror">
                            @error('email')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        </div>
                        <div class="mb-2 ">
                            <label class="block text-md font-medium text-gray-500">Mot de passe</label>
                            <a href="{{ route('profil.password.edit') }}" 
                                class="flex items-center justify-between p-1 border-2 gap-2 text-gray-700 w-full bg-gray-50 rounded-lg">
                                <span>••••••••••••••••</span>
                                <img src="{{ asset('images/icons/crayon.svg') }}" alt="" aria-hidden="true" class="w-6 h-6">
                            </a>
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