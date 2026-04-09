<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Jobs\ImporterSaqJob;
use Illuminate\Http\RedirectResponse;

/**
 * Classe InventaireSaqController
 *
 * Contrôleur permettant à l'administrateur
 * de lancer la mise à jour de l'inventaire SAQ.
 */
class InventaireSaqController extends Controller
{
    /**
     * Déclenche la mise à jour de l'inventaire SAQ.
     *
     * Cette méthode envoie un job dans la file d'attente
     * afin d'exécuter l'importation en arrière-plan.
     *
     * @return RedirectResponse
     */
    public function mettreAJour(): RedirectResponse
    {
        ImporterSaqJob::dispatch();

        return back()->with('success', 'La mise à jour de l’inventaire SAQ a été lancée.');
    }
}
