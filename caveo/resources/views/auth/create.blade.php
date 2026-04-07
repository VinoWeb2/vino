@extends('layouts.main')
@section('title', 'Connexion')
@section('content')
<div class="flex items-center justify-center min-h-[75vh]">
    <div class="w-full max-w-md p-6 bg-[#FFFFFF] border rounded-2xl shadow">
        <h3 class="font-[Crimson_text] text-xl text-center mb-6">Connexion</h3>
            <form method="POST" class="space-y-5">
                @csrf
                <div class="">
                        <label for="email" class="block mb-1 text-sm">Adresse courriel</label>
                        <input type="email" id="email" name="email" class="w-full border-2 p-2 rounded-lg bg-[#fafafa] focus:outline:none focus:ring-2 focus:ring-[#E0E0E0] @error ('email') border-red-600 @enderror" value="{{old('email')}}" placeholder="exemple@courriel.com">
                        @error('email')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                </div>
                <div class="">
                        <label for="mot_de_passe" class="block mb-1 text-sm">Mot de passe</label>
                        <input type="password" id="mot_de_passe" name="mot_de_passe" class="w-full border-2 p-2 rounded-lg bg-[#fafafa] focus:outline:none focus:ring-2 focus:ring-[#E0E0E0] @error ('mot_de_passe') border-red-600 @enderror" placeholder="********" required>
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
                <span class="mx-3 text-gray-400 text-sm">ou</span>
                <div class="grow border-t border-gray-300"></div>
            </div>
            <div class="my-4 text-center text-sm">
                <span class="text-gray-500">Pas encore de compte?</span>
                <a href="{{ route('inscription.form') }}" class="text-[#7A1E2E] underline">Créer un compte</a>
            </div>
    </div>
</div>
@endsection