@extends('layouts.main')
@section('title', 'Connexion')
@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 pb-24">
    <div class="max-w-md w-full bg-white border border-[#E0E0E0] rounded-lg shadow-sm p-8">
        <h2 class="text-2xl font-semibold text-[#1A1A1A] text-center">Connexion</h2>
            <form method="POST" class="space-y-5">
                @csrf
                <div class="">
                        <label for="email" class="block mb-1 text-sm font-medium text-[#1A1A1A]">Adresse courriel</label>
                        <input type="email" id="email" name="email" class="w-full border-2 p-2 rounded-lg focus:outline:none focus:ring-2 focus:ring-[#E0E0E0] @error ('email') border-red-600 @enderror" value="{{old('email')}}" placeholder="exemple@courriel.com">
                        @error('email')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                </div>
                <div class="">
                        <label for="mot_de_passe" class="block mb-1 text-sm font-medium text-[#1A1A1A]">Mot de passe</label>
                        <input type="password" id="mot_de_passe" name="mot_de_passe" class="w-full border-2 p-2 rounded-lg focus:outline:none focus:ring-2 focus:ring-[#E0E0E0] @error ('mot_de_passe') border-red-600 @enderror" placeholder="********" required>
                        @error('mot_de_passe')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                </div>
                <div class="flex justify-end my-3">
                    <a href="#" class="text-sm text-gray-500 underline">Mot de passe oublié?</a>
                </div>
                <button type="submit" class="w-full bg-[#7A1E2E] text-white py-2 rounded-lg">Se connecter</button>
            </form>
            <div class="flex items-center my-5">
                <div class="grow border-t border-gray-300"></div>
                <span class="px-3 text-[#666666]">ou</span>
                <div class="grow border-t border-gray-300"></div>
            </div>
            <div class="my-4 text-center text-sm">
                <span class="text-gray-500">Pas encore de compte?</span>
                <a href="{{ route('inscription.form') }}" class="text-[#7A1E2E] underline">Créer un compte</a>
            </div>
    </div>
</div>
@endsection