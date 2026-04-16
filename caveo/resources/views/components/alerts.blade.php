@if(session('status') || session('success') || session('error'))
<div class="space-y-2 mb-4">

    @if(session('status') || session('success'))
    <div class="message-flash-auto rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-700 shadow-sm">
        {{ session('status') ?? session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="message-flash-auto rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-700 shadow-sm">
        {{ session('error') }}
    </div>
    @endif

</div>
@endif

@if ($errors->any())
<div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-700 shadow-sm">
    <p class="font-medium mb-1">
        Le formulaire contient des erreurs :
    </p>

    <ul class="list-disc list-inside text-sm space-y-1">
        @foreach ($errors->all() as $erreur)
        <li>{{ $erreur }}</li>
        @endforeach
    </ul>
</div>
@endif