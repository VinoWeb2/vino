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
    <header>
        <h1 class="text-center bg-[#7A1E2E] text-white text-3xl py-4" style="font-family: 'Crimson Text', serif;">
            Caveo
        </h1>
    </header>

    <main class="grow bg-[#FCF8F7]">
        @yield('content')
    </main>

    @if(Auth::check())
    <footer class="bg-[#FCF8F7] text-black fixed bottom-3 left-3 right-3 rounded-xl shadow-2xl ring-1 ring-gray-300 py-2">
        <div class="flex justify-around text-center">
            <a href="#" class="flex flex-col items-center gap-1 px-3 py-1">
                <img src="{{ asset('images/icons/home-dark.svg') }}" alt="Accueil" class="w-6 h-6">
                <p class="text-base font-roboto font-medium">Accueil</p>
            </a>

            <a href="#" class="flex flex-col items-center gap-1 px-3 py-1">
                <img src="{{ asset('images/icons/bouteille-dark.svg') }}" alt="Cellier" class="w-6 h-6">
                <p class="text-base font-roboto font-medium">Cellier</p>
            </a>

            <a href="#" class="flex flex-col items-center gap-1 px-3 py-1">
                <img src="{{ asset('images/icons/add-dark.svg') }}" alt="Ajouter" class="w-6 h-6">
                <p class="text-base font-roboto font-medium">Ajouter</p>
            </a>

            <a href="#" class="flex flex-col items-center gap-1 px-3 py-1">
                <img src="{{ asset('images/icons/loop-dark.svg') }}" alt="Explorer" class="w-6 h-6">
                <p class="text-base font-roboto font-medium">Explorer</p>
            </a>

            <a href="#" class="flex flex-col items-center gap-1 px-3 py-1">
                <img src="{{ asset('images/icons/profil-dark.svg') }}" alt="Profil" class="w-6 h-6">
                <p class="text-base font-roboto font-medium">Profil</p>
            </a>
        </div>
    </footer>
    @endif
</body>

</html>