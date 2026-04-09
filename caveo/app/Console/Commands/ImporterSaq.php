<?php

namespace App\Console\Commands;

use App\Models\Bouteille;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * Commande Artisan permettant d'importer les vins depuis l'API SAQ.
 *
 * Cette commande :
 * - récupère les produits SAQ par pagination ;
 * - filtre pour conserver uniquement les vins ;
 * - nettoie certaines valeurs avant insertion ;
 * - met à jour les bouteilles existantes ou en crée de nouvelles ;
 * - évite les doublons grâce à updateOrCreate().
 *
 * Utilisation :
 * php artisan saq:import
 */
class ImporterSaq extends Command
{
  /**
   * Signature de la commande Artisan.
   *
   * @var string
   */
  protected $signature = 'saq:import';

  /**
   * Description de la commande.
   *
   * @var string
   */
  protected $description = 'Importer toutes les bouteilles de vin depuis la SAQ';

  /**
   * Taille de page utilisée pour les requêtes paginées.
   *
   * @var int
   */
  private const TAILLE_PAGE = 50;

  /**
   * Limite de sécurité pour éviter une boucle infinie
   * si l'API se comporte de façon inattendue.
   *
   * @var int
   */
  private const LIMITE_PAGES_SECURITE = 500;

  /**
   * Point d'entrée principal de la commande.
   *
   * @return int
   */
  public function handle(): int
  {
    try {
      $page = 1;
      $pageSize = self::TAILLE_PAGE;
      $totalImporte = 0;
      $totalIgnores = 0;

      do {
        $this->info("Import de la page {$page}...");

        $response = $this->envoyerRequeteSaq($page, $pageSize);

        if ($response->failed()) {
          $this->error("Erreur API SAQ à la page {$page}.");
          $this->line($response->body());

          return self::FAILURE;
        }

        $data = $response->json();
        $items = $data['data']['productSearch']['items'] ?? [];

        if (empty($items)) {
          $this->info("Aucun item retourné à la page {$page}. Fin de l'import.");
          break;
        }

        foreach ($items as $item) {
          if ($this->traiterItem($item)) {
            $totalImporte++;
          } else {
            $totalIgnores++;
          }
        }

        $this->info("Page {$page} terminée. Importés : {$totalImporte} | Ignorés : {$totalIgnores}");

        if (count($items) < $pageSize) {
          $this->info('Dernière page atteinte.');
          break;
        }

        $page++;

        sleep(1);

        if ($page > self::LIMITE_PAGES_SECURITE) {
          $this->warn('Arrêt de sécurité atteint : limite maximale de pages dépassée.');
          break;
        }
      } while (true);

      $this->info('Import terminé avec succès.');
      $this->info("Nombre total de vins importés : {$totalImporte}");
      $this->info("Nombre total d’items ignorés : {$totalIgnores}");

      return self::SUCCESS;
    } catch (Throwable $e) {
      $this->error('Erreur : ' . $e->getMessage());
      $this->line('Fichier : ' . $e->getFile());
      $this->line('Ligne : ' . $e->getLine());

      return self::FAILURE;
    }
  }

  /**
   * Envoie une requête GraphQL à l'API SAQ pour une page donnée.
   *
   * @param int $page
   * @param int $pageSize
   * @return \Illuminate\Http\Client\Response
   */
  private function envoyerRequeteSaq(int $page, int $pageSize)
  {
    $query = <<<GRAPHQL
        {
          productSearch(phrase: "", page_size: $pageSize, current_page: $page) {
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

    return Http::withOptions([
      'verify' => false,
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
  }

  /**
   * Traite un item retourné par l'API SAQ.
   *
   * @param array $item
   * @return bool
   */
  private function traiterItem(array $item): bool
  {
    $attributes = $item['productView']['attributes'] ?? [];

    $codeSaq = $item['productView']['sku'] ?? null;
    $nom = $item['productView']['name'] ?? null;

    $type = $this->trouverAttribut($attributes, 'identite_produit');
    $pays = $this->trouverAttribut($attributes, 'pays_origine');
    $cepage = $this->trouverAttribut($attributes, 'cepage');
    $millesime = $this->trouverAttribut($attributes, 'millesime_produit');
    $tauxAlcool = $this->trouverAttribut($attributes, 'pourcentage_alcool_par_volume');
    $format = $this->trouverAttribut($attributes, 'format_contenant_ml');
    $pastilleGout = $this->trouverAttribut($attributes, 'pastille_gout');

    $image = $item['product']['image']['url'] ?? null;
    $image = $this->normaliserUrlImage($image);

    if (empty($codeSaq) || empty($nom) || empty($type)) {
      return false;
    }

    if (!$this->estUnVin($type)) {
      return false;
    }

    Bouteille::updateOrCreate(
      [
        'code_saq' => $codeSaq,
      ],
      [
        'nom' => $nom,
        'type' => $type,
        'pays' => $pays,
        'cepage' => $cepage,
        'millesime' => is_numeric($millesime) ? (int) $millesime : null,
        'taux_alcool' => $this->nettoyerDecimal($tauxAlcool),
        'prix' => $this->nettoyerDecimal(
          isset($item['product']['price_range']['minimum_price']['regular_price']['value'])
            ? (string) $item['product']['price_range']['minimum_price']['regular_price']['value']
            : null
        ),
        'format' => $this->nettoyerEntier($format),
        'image' => $image,
        'pastille_gout' => $pastilleGout,
        'est_saq' => true,
      ]
    );

    return true;
  }

  /**
   * Recherche la valeur d'un attribut donné dans la liste des attributs SAQ.
   *
   * @param array $attributes
   * @param string $nomRecherche
   * @return string|null
   */
  private function trouverAttribut(array $attributes, string $nomRecherche): ?string
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
   * Nettoie une valeur décimale.
   *
   * @param string|null $valeur
   * @return float|null
   */
  private function nettoyerDecimal(?string $valeur): ?float
  {
    if ($valeur === null || $valeur === '') {
      return null;
    }

    $valeur = str_replace(['%', ','], ['', '.'], trim($valeur));

    return is_numeric($valeur) ? (float) $valeur : null;
  }

  /**
   * Nettoie une valeur entière.
   *
   * @param string|null $valeur
   * @return int|null
   */
  private function nettoyerEntier(?string $valeur): ?int
  {
    if ($valeur === null || $valeur === '') {
      return null;
    }

    $valeur = preg_replace('/[^0-9]/', '', $valeur);

    return is_numeric($valeur) ? (int) $valeur : null;
  }

  /**
   * Vérifie si le type de produit correspond à un vin.
   *
   * @param string $type
   * @return bool
   */
  private function estUnVin(string $type): bool
  {
    $type = mb_strtolower(trim($type));

    $typesAcceptes = [
      'vin rouge',
      'vin blanc',
      'vin rosé',
      'vin mousseux',
      'vin orange',
      'vin',
      'porto',
      'vin fortifié',
      'champagne',
    ];

    foreach ($typesAcceptes as $typeAccepte) {
      if (str_contains($type, $typeAccepte)) {
        return true;
      }
    }

    return false;
  }

  /**
   * Normalise l'URL d'image retournée par l'API.
   *
   * @param string|null $image
   * @return string|null
   */
  private function normaliserUrlImage(?string $image): ?string
  {
    if ($image === null || $image === '') {
      return null;
    }

    if (str_starts_with($image, '//')) {
      return 'https:' . $image;
    }

    return $image;
  }
}
