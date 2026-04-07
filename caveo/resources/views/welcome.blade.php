@extends('layouts.main')
@section('title', 'allo')
@section('content')
<p>allo</p>
    @if(Auth::check())
        Bonjour {{ Auth::user()->prenom ?? Auth::user()->email }} |
        <a href="{{ route('deconnexion') }}">Déconnexion</a>
    @else
        <a href="{{ route('connexion') }}">Connexion</a>
    @endif
@endsection