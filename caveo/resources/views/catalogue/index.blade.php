@extends('layouts.main')
@section('title', 'caveo')
@section('content')

<div>
    <p>Résultats {{ $bouteilles->firstItem() }}-{{ $bouteilles->lastItem() }} sur {{ $bouteilles->total() }}</p>
</div>

@foreach($bouteilles as $bouteille)
    <div>
        {{ $bouteille->nom }}
        <img src="{{ $bouteille->image }}" alt="">
        
    </div>
@endforeach

<div class="flex justify-between items-center mx-auto my-5">
    @if ($bouteilles->onFirstPage())
        <span>
            <img src="{{ asset('images/fleches/gauche-gris.svg') }}" class="w-10" alt="">
        </span>
    @else
        <a href="{{ $bouteilles->previousPageUrl() }}">
            <img src="{{ asset('images/fleches/gauche-rouge.svg') }}" class="w-10 hover:scale-110 transition" alt="">
        </a>
    @endif

    @if ($bouteilles->hasMorePages())
        <a href="{{ $bouteilles->nextPageUrl() }}">
            <img src="{{ asset('images/fleches/droit-rouge.svg') }}" class="w-10 hover:scale-110 transition" alt="">
        </a>
    @else
        <span>
            <img src="{{ asset('images/fleches/droit-gris.svg') }}" class="w-10" alt="">
        </span>
    @endif
</div>

@endsection