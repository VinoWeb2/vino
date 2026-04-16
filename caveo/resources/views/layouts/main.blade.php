<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    <!-- <link rel="stylesheet" href="{{ asset('css/app.css') }}"> -->
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Text&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap"
        rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script type="module" src="{{ asset('js/confirmer-modal.js') }}"></script>
</head>

<div id="confirmModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-[90%] max-w-sm overflow-hidden">
        <div class="bg-[#A83248] w-full p-4 flex items-center justify-center gap-2">
            <h2 class="text-lg font-semibold text-white">Confirmation</h2>
            <!-- <img src="{{ asset('images/symbole/vin.png') }}" alt="icon" class="w-5 h-5"> -->
        </div>
        <div class="p-5">
            <p id="confirmMessage" class="text-sm text-gray-600">Êtes-vous sûr de vouloir supprimer ?</p>
            <div class="flex justify-end gap-3 mt-5">
                <button id="cancelModal" class="px-4 py-2 rounded border text-gray-600">Annuler</button>
                <button id="confirmModalBtn" class="px-4 py-2 rounded bg-[#A83248] text-white ">Supprimer</button>
            </div>
        </div>
    </div>
</div>

<body class="flex flex-col min-h-screen">
    <header class="bg-[#7A1E2E] relative py-4">
        <div class="absolute right-4 top-1/2 -translate-y-1/2">
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
        @if(optional(Auth::user()->role)->nom === 'admin')
        {{-- Footer administrateur --}}
        <footer
            class="bg-[#FCF8F7] text-black fixed bottom-3 left-3 right-3 rounded-xl shadow-2xl ring-1 ring-gray-300 py-2">
            <div class="flex justify-around text-center">
                <a href="{{ route('accueil') }}" class="flex flex-col items-center gap-1 px-3 py-1">
                    <img src="{{ request()->routeIs('accueil')
                        ? asset('images/icons/home-actif.svg')
                        : asset('images/icons/home-inactif.svg') }}" alt="Accueil" class="w-6 h-6 object-contain">
                    <p class="text-sm font-roboto font-medium">Accueil</p>
                </a>

                <a href="{{ route('admin.utilisateurs.index') }}" class="flex flex-col items-center gap-1 px-3 py-1">
                    <img src="{{ request()->routeIs('admin.utilisateurs.*')
                        ? asset('images/icons/profil-actif.svg')
                        : asset('images/icons/profil-inactif.svg') }}" alt="Utilisateurs" class="w-6 h-6 object-contain">
                    <p class="text-sm font-roboto font-medium">Utilisateurs</p>
                </a>
            </div>
        </footer>
        @else
        {{-- Footer utilisateur --}}
        <footer
            class="bg-[#FCF8F7] text-black fixed bottom-3 left-3 right-3 rounded-xl shadow-2xl ring-1 ring-gray-300 py-2">
            <div class="flex justify-around text-center">
                <a href="{{ route('accueil') }}" class="flex flex-col items-center gap-1 px-3 py-1 invisible">
                    <img src="{{ request()->routeIs('accueil')
                ? asset('images/icons/home-actif.svg')
                : asset('images/icons/home-inactif.svg') }}" alt="Explorer" class="w-6 h-6 object-contain">
                    <p class="text-sm font-roboto font-medium">Accueil</p>
                </a>

                <a href="{{ route('celliers.index') }}" class="flex flex-col items-center gap-1 px-3 py-1">
                    <img src="{{ request()->routeIs('celliers.*')
                ? asset('images/icons/cellier-actif.svg')
                : asset('images/icons/cellier-inactif.svg') }}" alt="Cellier" class="w-6 h-6">
                    <p class="text-sm font-roboto font-medium">Cellier</p>
                </a>


                <a href="{{ route('catalogue.index') }}" class="flex flex-col items-center gap-1 px-3 py-1">
                    <img
                        src="{{ request()->routeIs('catalogue.index')
                                ? asset('images/icons/loop-actif.svg')
                                : asset('images/icons/loop-inactif.svg') }}"
                        alt="Explorer"
                        class="w-6 h-6 object-contain">
                    <p class="text-sm font-roboto font-medium">Catalogue</p>
                </a>

                <a href="{{ route('achat.index') }}" class="flex flex-col items-center gap-1 px-3 py-1">
                    <img src="{{ request()->routeIs('achat.index')
                                ? asset('images/icons/liste-actif.svg')
                                : asset('images/icons/liste.svg') }}" alt="Liste achat" class="w-6 h-6">
                    <p class="text-sm font-roboto font-medium wrap">Liste d'achat</p>
                </a>

                <a href="#" class="flex flex-col items-center gap-1 px-3 py-1 ">
                    <img src="{{ asset('images/icons/profil-inactif.svg') }}" alt="Profil" class="w-6 h-6">
                    <p class="text-sm font-roboto font-medium">Profil</p>
                </a>
            </div>
        </footer>
        @endif
    @endif
</body>

</html>