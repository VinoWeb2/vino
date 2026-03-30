<?php

use App\Http\Controllers\Admin\InventaireSaqController;
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
| - d'afficher la page d'accueil ;
| - de lancer la mise à jour de l'inventaire SAQ côté admin ;
| - de tester manuellement la connexion à l'API SAQ via une route temporaire.
|
*/

/**
 * Route permettant à l'administrateur de déclencher
 * la mise à jour de l'inventaire SAQ.
 */
Route::post('/admin/saq/update', [InventaireSaqController::class, 'mettreAJour'])
  ->name('admin.saq.update');

/**
 * Recherche un attribut spécifique dans la liste des attributs
 * retournés par l'API SAQ.
 *
 * @param array $attributes Liste des attributs d'un produit
 * @param string $nomRecherche Nom de l'attribut à rechercher
 * @return string|null Valeur trouvée ou null
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
 * Route de la page d'accueil.
 */
Route::get('/', function () {
  return view('welcome');
});

/**
 * Route de test temporaire pour valider la connexion à l'API SAQ
 * et l'importation d'un petit échantillon de produits.
 *
 * Cette route :
 * - interroge l'API GraphQL de la SAQ ;
 * - récupère un petit nombre d'items ;
 * - extrait certains attributs utiles ;
 * - crée ou met à jour les bouteilles en base de données.
 *
 * Remarque :
 * Cette route est utile en développement, mais elle devrait idéalement
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
    /*
         * Désactive temporairement la vérification SSL en environnement local.
         * À remplacer par une configuration SSL valide en production.
         */
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

    Bouteille::updateOrCreate(
      [
        'code_saq' => $item['productView']['sku'] ?? null,
      ],
      [
        'nom' => $item['productView']['name'] ?? null,
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
