@extends('layouts.main')

@section('title', 'Inscription')

@section('content')
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 pb-24">
            <div class="max-w-md w-full bg-white border border-[#E0E0E0] rounded-lg shadow-sm p-8">
            @if(session('status'))
                <div id="flash-alert" role="alert" aria-live="polite" class="mb-4 flex items-start gap-3 bg-green-50 border border-green-200 text-green-800 rounded-md p-3">
                    <svg class="w-5 h-5 flex-shrink-0 text-green-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>

                    <div class="flex-1 text-sm">{{ session('status') }}</div>

                    <button type="button" aria-label="Fermer" class="ms-3 text-green-700 hover:text-green-900" onclick="document.getElementById('flash-alert')?.remove()">✕</button>
                </div>

            @endif

            <h2 class="text-2xl font-semibold text-[#1A1A1A] text-center">Créer un compte</h2>

            <form method="POST" action="{{ route('inscription.submit') }}" class="mt-6 space-y-6">
                @csrf

                <h3 class="text-lg font-semibold text-[#7A1E2E]">Informations personnelles</h3>

                <div class="md:grid md:grid-cols-2 md:gap-4">
                    <div>
                        <label for="prenom" class="block text-sm font-medium text-[#1A1A1A]">Prénom</label>
                        <input id="prenom" name="prenom" type="text" placeholder="Marie"
                            value="{{ old('prenom') }}"
                            class="mt-1 block w-full border-2 text-[#1A1A1A] rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-[#A83248] focus:border-[#A83248] @error ('prenom') border-red-600 @enderror" />
                        @error('prenom')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="mt-4 md:mt-0">
                        <label for="nom" class="block text-sm font-medium text-[#1A1A1A]">Nom</label>
                        <input id="nom" name="nom" type="text" placeholder="Tremblay"
                            value="{{ old('nom') }}"
                            class="mt-1 block w-full border-2 text-[#1A1A1A] rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-[#A83248] focus:border-[#A83248] @error ('nom') border-red-600 @enderror" />
                        @error('nom')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label for="courriel" class="block text-sm font-medium text-[#1A1A1A]">Adresse courriel</label>
                    <input id="courriel" name="courriel" type="text" placeholder="exemple@courriel.com"
                        value="{{ old('courriel') }}"
                        class="mt-1 block w-full border-2 text-[#1A1A1A] rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-[#A83248] focus:border-[#A83248] @error ('courriel') border-red-600 @enderror" />
                    @error('courriel')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <hr class="my-6 border-[#E0E0E0]" />

                <h3 class="text-lg font-semibold text-[#7A1E2E]">Sécurité</h3>

                <div>
                    <label for="mot_de_passe" class="block text-sm font-medium text-[#1A1A1A]">Mot de passe</label>
                    <input id="mot_de_passe" name="mot_de_passe" type="password"
                        class="mt-1 block w-full border-2 text-[#1A1A1A] rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-[#A83248] focus:border-[#A83248] @error ('mot_de_passe') border-red-600 @enderror" />
                    <p class="mt-1 text-sm text-[#666666]">Minimum : 8 caractères</p>
                    @error('mot_de_passe')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="mot_de_passe_confirmation" class="block text-sm font-medium text-[#1A1A1A]">Confirmer le mot de passe</label>
                    <input id="mot_de_passe_confirmation" name="mot_de_passe_confirmation" type="password"
                        class="mt-1 block w-full border-2 text-[#1A1A1A] rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-[#A83248] focus:border-[#A83248] @error ('mot_de_passe_confirmation') border-red-600 @enderror" />
                    @error('mot_de_passe_confirmation')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="mt-2">
                    <button type="submit" class="w-full bg-[#7A1E2E] hover:bg-[#551525] text-white px-4 py-2 rounded-md">Créer mon compte</button>
                </div>

                <div class="flex items-center my-4">
                    <div class="flex-grow border-t border-[#E0E0E0]"></div>
                    <span class="px-3 text-[#666666]">ou</span>
                    <div class="flex-grow border-t border-[#E0E0E0]"></div>
                </div>

                <div class="text-center text-sm text-[#666666]">
                    Déjà un compte ? <a href="{{ route('connexion') }}" class="text-[#7A1E2E] font-medium hover:underline">Se connecter</a>
                </div>
            </form>
        </div>
    </div>
@endsection
