<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>

<div class="container flex-grow-1">
    @yield('content')
</div>

<footer>
    <div >
        allo
    </div>
</footer>

</body>
</html>
