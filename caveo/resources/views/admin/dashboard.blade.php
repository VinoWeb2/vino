{{--
    Bouton CTA permettant à l'administrateur
    de déclencher la mise à jour de l'inventaire SAQ.
--}}

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

<div class="card mt-4">
    <div class="card-body">
        <h5 class="card-title">Mise à jour de l’inventaire SAQ</h5>
        <p class="card-text">
            Cliquez sur le bouton ci-dessous pour lancer l’importation
            et la mise à jour des bouteilles provenant du site de la SAQ.
        </p>

        <form action="{{ route('admin.saq.update') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary">
                Mettre à jour l’inventaire SAQ
            </button>
        </form>
    </div>
</div>