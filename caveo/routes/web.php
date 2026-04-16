<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BouteilleController;
use App\Http\Controllers\CatalogueController;
use App\Http\Controllers\CellierController;
use App\Http\Controllers\InventaireController;
use App\Http\Controllers\ListeAchatController;
use App\Http\Controllers\RegisterController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Routes publiques / authentification
|--------------------------------------------------------------------------
*/

Route::get('/', [AuthController::class, 'create'])->name('connexion');
Route::post('/', [AuthController::class, 'store'])->name('auth.store');

Route::get('/inscription', function () {
  return view('auth.inscription');
})->name('inscription.form');

Route::post('/inscription', [RegisterController::class, 'store'])
  ->name('inscription.submit');

/*
|--------------------------------------------------------------------------
| Routes protégées
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

  /*
    |--------------------------------------------------------------------------
    | Navigation générale
    |--------------------------------------------------------------------------
    */

  Route::get('/accueil', function () {
    return view('welcome');
  })->name('accueil');

  Route::get('/deconnexion', [AuthController::class, 'destroy'])->name('deconnexion');

  /*
    |--------------------------------------------------------------------------
    | Catalogue / bouteilles
    |--------------------------------------------------------------------------
    */

  Route::get('/catalogue', [CatalogueController::class, 'index'])
    ->name('catalogue.index');

  Route::get('/bouteilles/{bouteille}', [BouteilleController::class, 'show'])
    ->name('bouteilles.show')
    ->missing(function () {
      return redirect()
        ->route('catalogue.index')
        ->with('status', 'Cette bouteille est introuvable.');
    });

  /*
    |--------------------------------------------------------------------------
    | Celliers
    |--------------------------------------------------------------------------
    */

  Route::resource('celliers', CellierController::class);

  /*
    |--------------------------------------------------------------------------
    | Inventaires
    |--------------------------------------------------------------------------
    */

  Route::post('/celliers/{cellier}/inventaires', [InventaireController::class, 'store'])
    ->name('inventaires.store');

  Route::put('/inventaires/{inventaire}', [InventaireController::class, 'update'])
    ->name('inventaires.update');

  Route::patch('/inventaires/{inventaire}/quantite', [InventaireController::class, 'updateQuantite'])
    ->name('inventaires.updateQuantite');

  Route::delete('/inventaires/{inventaire}', [InventaireController::class, 'destroy'])
    ->name('inventaires.destroy');

  /*
    |--------------------------------------------------------------------------
    | Bouteilles non listées liées à un cellier
    |--------------------------------------------------------------------------
    */

  Route::get('/celliers/{cellier}/bouteilles/create', [BouteilleController::class, 'createFromCellier'])
    ->name('celliers.bouteilles.create');

  Route::post('/celliers/{cellier}/bouteilles', [BouteilleController::class, 'storeFromCellier'])
    ->name('celliers.bouteilles.store');

  Route::get('/celliers/{cellier}/bouteilles/{bouteille}/edit', [BouteilleController::class, 'editFromCellier'])
    ->name('celliers.bouteilles.edit');

  Route::put('/celliers/{cellier}/bouteilles/{bouteille}', [BouteilleController::class, 'updateFromCellier'])
    ->name('celliers.bouteilles.update');

  Route::delete('/celliers/{cellier}/bouteilles/{bouteille}', [BouteilleController::class, 'destroyFromCellier'])
    ->name('celliers.bouteilles.destroy');

  /*
    |--------------------------------------------------------------------------
    | Listes d’achat
    |--------------------------------------------------------------------------
    */

  Route::get('/achat', [ListeAchatController::class, 'index'])->name('achat.index');
  Route::get('/achat/creation', [ListeAchatController::class, 'create'])->name('achat.create');
  Route::post('/achat/creation', [ListeAchatController::class, 'store'])->name('achat.store');

  Route::get('/achat/{liste}', [ListeAchatController::class, 'edit'])->name('achat.edit');
  Route::put('/achat/{liste}', [ListeAchatController::class, 'update'])->name('achat.update');
  Route::delete('/achat/{liste}', [ListeAchatController::class, 'destroy'])->name('achat.destroy');

  Route::post('/achat/{liste}/bouteilles', [ListeAchatController::class, 'addBouteille'])
    ->name('achat.bouteilles.add');

  Route::delete('/achat/{liste}/bouteilles/{bouteille}', [ListeAchatController::class, 'removeBouteille'])
    ->name('achat.bouteilles.destroy');
});
