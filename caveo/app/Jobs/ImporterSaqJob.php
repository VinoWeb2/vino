<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Classe ImporterSaqJob
 *
 * Job Laravel responsable de lancer la commande
 * d'importation des bouteilles SAQ en arrière-plan.
 *
 * Ce job permet d'éviter de bloquer l'interface
 * d'administration lors de la mise à jour de l'inventaire.
 */
class ImporterSaqJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Nombre maximal de tentatives du job.
     *
     * @var int
     */
    public $tries = 1;

    /**
     * Crée une nouvelle instance du job.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Exécute le job.
     *
     * Cette méthode lance la commande Artisan
     * responsable d'importer les bouteilles depuis la SAQ.
     *
     * @return void
     */
    public function handle(): void
    {
        Artisan::call('saq:import');
    }
}
