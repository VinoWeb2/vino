<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BouteilleController;
use App\Http\Controllers\CatalogueController;
use App\Http\Controllers\CellierController;
use App\Http\Controllers\InventaireController;
use App\Http\Controllers\InventaireSaqController;
use App\Http\Controllers\ListeAchatController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UtilisateurController;
use App\Models\Bouteille;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Ce fichier contient les routes Web de l'application.
| Il permet :
| - d'afficher les pages publiques ;
| - de gérer les pages accessibles aux utilisateurs connectés ;
| - de lancer la mise à jour de l'inventaire SAQ ;
| - de tester manuellement la connexion à l'API SAQ via une route temporaire.
|
*/

/**
   * Routes pour l'inscription.
   */
  Route::get('/inscription', function () {
    return view('auth.inscription');
  })->name('inscription.form');

  Route::post('/inscription', [RegisterController::class, 'store'])
    ->name('inscription.submit');

  /**
   * Routes pour la connexion et la déconnexion.
   */
  Route::get('/', [AuthController::class, 'create'])->name('connexion');
  Route::post('/', [AuthController::class, 'store'])->name('auth.store');
  Route::get('/deconnexion', [AuthController::class, 'destroy'])->name('deconnexion');

/**
 * Routes accessibles uniquement aux utilisateurs connectés.
 */
Route::middleware('auth')->group(function () {

  /**
   * Route de la page d'accueil.
   *
   * @return \Illuminate\View\View
   */
  Route::get('/accueil', function () {
    return view('welcome');
  })->name('accueil');

  /**
   * Route vers le catalogue des bouteilles.
   */
  Route::get('/catalogue', [CatalogueController::class, 'index'])
    ->name('catalogue.index');

  /**
   * Route vers la fiche détail d'une bouteille.
   */
  Route::get('/bouteilles/{bouteille}', [BouteilleController::class, 'show'])
    ->name('bouteilles.show')
    ->missing(function () {
      return redirect()
        ->route('catalogue.index')
        ->with('status', 'Cette bouteille est introuvable.');
    });

  /**
   * Routes de gestion des celliers.
   *
   * Génère automatiquement :
   * - celliers.index
   * - celliers.create
   * - celliers.store
   * - celliers.show
   * - celliers.edit
   * - celliers.update
   * - celliers.destroy
   */
  Route::resource('celliers', CellierController::class);

  /**
   * Ajoute une bouteille dans un cellier.
   */
  Route::post('/celliers/{cellier}/inventaires', [InventaireController::class, 'store'])
    ->name('inventaires.store');

  /**
   * Met à jour une ligne d'inventaire.
   */
  Route::put('/inventaires/{inventaire}', [InventaireController::class, 'update'])
    ->name('inventaires.update');

  /**
   * Met à jour rapidement la quantité d'une bouteille
   * à l'aide des boutons + et -.
   */
  Route::patch('/inventaires/{inventaire}/quantite', [InventaireController::class, 'updateQuantite'])
    ->name('inventaires.updateQuantite');

  /**
   * Supprime une ligne d'inventaire.
   */
  Route::delete('/inventaires/{inventaire}', [InventaireController::class, 'destroy'])
    ->name('inventaires.destroy');

  /**
   * Déclenche la mise à jour de l'inventaire SAQ.
   */
  Route::post('/admin/saq/update', [InventaireSaqController::class, 'mettreAJour'])
    ->name('admin.saq.update');


  /**
   *  Retourne la page de la liste d'achat
   */
  Route::get('/achat', [ListeAchatController::class, 'index'])->name('achat.index');

  Route::get('/achat/creation', [ListeAchatController::class, 'create'])->name('achat.create');
  Route::post('/achat/creation', [ListeAchatController::class, 'store'])->name('achat.store');

  Route::get('/achat/{liste}', [ListeAchatController::class, 'edit'])->name('achat.edit');
  Route::put('/achat/{liste}', [ListeAchatController::class, 'update'])->name('achat.update');

  Route::delete('/achat/{liste}', [ListeAchatController::class, 'destroy'])->name('achat.destroy');

  Route::post('/achat/{liste}/bouteilles', [ListeAchatController::class, 'addBouteille'])->name('achat.bouteilles.add');
  Route::delete('/achat/{liste}/bouteilles/{bouteille}', [ListeAchatController::class, 'removeBouteille'])->name('achat.bouteilles.destroy');

  /**
   * Routes pour le profil et la modification du profil
   */
  Route::get('/profil', [UtilisateurController::class, 'show'])->name('profil.show');
  Route::get('/profil/edit', [UtilisateurController::class, 'edit'])->name('profil.edit');
  Route::post('/profil', [UtilisateurController::class, 'update'])->name('profil.update');

  Route::get('/profil/password', [UtilisateurController::class, 'editPassword'])->name('profil.password.edit');
  Route::post('/profil/password', [UtilisateurController::class, 'updatePassword'])->name('profil.password.update');

  /**
   * Route pour supprimer son compte
   */
  Route::delete('/profil', [UtilisateurController::class, 'destroy'])->name('profil.destroy');
});

/**
 * Recherche un attribut spécifique dans la liste des attributs SAQ.
 *
 * @param array $attributes
 * @param string $nomRecherche
 * @return string|null
 */
function trouverAttribut(array $attributes, string $nomRecherche): ?string
{
  foreach ($attributes as $attribute) {
    if (($attribute['name'] ?? '') === $nomRecherche) {
      $valeur = $attribute['value'] ?? null;

      if (is_array($valeur)) {
        return implode(', ', array_map('strval', $valeur));
      }

      return $valeur !== null ? (string) $valeur : null;
    }
  }

  return null;
}

/**
 * Route de test temporaire pour valider la connexion à l'API SAQ
 * et l'importation d'un petit échantillon de produits.
 *
 * Remarque :
 * Cette route est utile en développement, mais devrait idéalement
 * être retirée ou protégée dans la version finale.
 */
Route::get('/test-saq', function () {
  $query = <<<'GRAPHQL'
    {
      productSearch(phrase: "", page_size: 5, current_page: 1) {
        items {
          productView {
            name
            sku
            attributes {
              label
              name
              value
            }
          }
          product {
            sku
            image {
              url
            }
            price_range {
              minimum_price {
                regular_price {
                  value
                }
              }
            }
          }
        }
      }
    }
    GRAPHQL;

  $response = Http::withOptions([
    'verify' => false,
    'curl' => [
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_SSL_VERIFYHOST => 0,
    ],
  ])->withHeaders([
    'x-api-key' => env('SAQ_API_KEY'),
    'magento-customer-group' => env('SAQ_CUSTOMER_GROUP'),
    'magento-environment-id' => env('SAQ_ENVIRONMENT_ID'),
    'magento-store-code' => env('SAQ_STORE_CODE'),
    'magento-store-view-code' => env('SAQ_STORE_VIEW_CODE'),
    'magento-website-code' => env('SAQ_WEBSITE_CODE'),
    'Content-Type' => 'application/json',
  ])->post(env('SAQ_GRAPHQL_URL'), [
    'query' => $query,
  ]);

  if ($response->failed()) {
    return response()->json([
      'message' => 'Erreur lors de la requête à la SAQ.',
      'details' => $response->body(),
    ], $response->status());
  }

  $data = $response->json();
  $items = $data['data']['productSearch']['items'] ?? [];

  foreach ($items as $item) {
    $attributes = $item['productView']['attributes'] ?? [];

    $pays = trouverAttribut($attributes, 'pays_origine');
    $cepage = trouverAttribut($attributes, 'cepage');
    $millesime = trouverAttribut($attributes, 'millesime_produit');
    $alcool = trouverAttribut($attributes, 'pourcentage_alcool_par_volume');
    $type = trouverAttribut($attributes, 'identite_produit');

    Bouteille::updateOrCreate(
      [
        'code_saq' => $item['product']['sku'],
      ],
      [
        'nom' => $item['productView']['name'],
        'type' => $type,
        'pays' => $pays,
        'cepage' => $cepage,
        'millesime' => is_numeric($millesime) ? (int) $millesime : null,
        'taux_alcool' => is_numeric($alcool) ? (float) $alcool : null,
        'prix' => $item['product']['price_range']['minimum_price']['regular_price']['value'] ?? null,
        'est_saq' => true,
      ]
    );
  }

  return response()->json([
    'message' => 'Import terminé avec succès.',
    'nombre_bouteilles' => count($items),
  ]);
});