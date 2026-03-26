<?php

use App\Models\Bouteille;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BouteilleController;

function trouverAttribut(array $attributes, string $nomRecherche): ?string
{
    foreach ($attributes as $attribute) {
        if (($attribute['name'] ?? '') === $nomRecherche) {
          $value = $attribute['value'] ?? null;

          if (is_array($value)){
            return implode(', ', $value);
          }

            return $value;
        }
    }

    return null;
}

Route::get('/', function () {
    return view('welcome');
});

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
