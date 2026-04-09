<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    <!-- <link rel="stylesheet" href="{{ asset('css/app.css') }}"> -->
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Text&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@500&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="flex flex-col min-h-screen">
    <header class="bg-[#7A1E2E] relative py-4">
        <div class="absolute left-4 top-1/2 -translate-y-1/2">
            @yield('fleche')
        </div>
        <h1 class="text-center text-white text-3xl" style="font-family: 'Crimson Text', serif;">
            Caveo
        </h1>
    </header>

    <main class="grow bg-[#FCF8F7]">
        @yield('content')
    </main>

    @if(Auth::check())
    <footer class="bg-[#FCF8F7] text-black fixed bottom-3 left-3 right-3 rounded-xl shadow-2xl ring-1 ring-gray-300 py-2">
        <div class="flex justify-around text-center">
            <a href="{{ route('accueil') }}" class="flex flex-col items-center gap-1 px-3 py-1">
                <img
                    src="{{ request()->routeIs('accueil') 
                            ? asset('images/icons/home-actif.svg') 
                            : asset('images/icons/home-inactif.svg') }}"
                    alt="Explorer"
                    class="w-6 h-6 object-contain">
                <p class="text-base font-roboto font-medium">Accueil</p>
            </a>

            <a href="{{ route('celliers.index') }}" class="flex flex-col items-center gap-1 px-3 py-1">
                <img
                    src="{{ request()->routeIs('celliers.*') 
                ? asset('images/icons/bouteille-actif.svg') 
                : asset('images/icons/bouteille-inactif.svg') }}"
                    alt="Cellier"
                    class="w-6 h-6">
                <p class="text-base font-roboto font-medium">Cellier</p>
            </a>

            <a href="#" class="flex flex-col items-center gap-1 px-3 py-1">
                <img src="{{ asset('images/icons/ajouter-inactif.svg') }}" alt="Ajouter" class="w-6 h-6">
                <p class="text-base font-roboto font-medium">Ajouter</p>
            </a>

            <a href="{{ route('catalogue.index') }}" class="flex flex-col items-center gap-1 px-3 py-1">
                <img
                    src="{{ request()->routeIs('catalogue.index') 
                            ? asset('images/icons/loop-actif.svg') 
                            : asset('images/icons/loop-inactif.svg') }}"
                    alt="Explorer"
                    class="w-6 h-6 object-contain">
                <p class="text-base font-roboto font-medium">Explorer</p>
            </a>

            <a href="#" class="flex flex-col items-center gap-1 px-3 py-1">
                <img src="{{ asset('images/icons/profil-inactif.svg') }}" alt="Profil" class="w-6 h-6">
                <p class="text-base font-roboto font-medium">Profil</p>
            </a>
        </div>
    </footer>
    @endif
</body>

</html>