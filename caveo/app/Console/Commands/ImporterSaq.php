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
 * - filtre pour conserver les vins ;
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
  protected $description = 'Importer les bouteilles de vin depuis la SAQ';

  /**
   * Taille de page utilisée pour les requêtes paginées.
   *
   * @var int
   */
  private const TAILLE_PAGE = 100;

  /**
   * Limite de sécurité pour éviter une boucle infinie
   * si l'API se comporte de façon inattendue.
   *
   * @var int
   */
  private const LIMITE_PAGES_SECURITE = 500;

  /**
   * pagination de l'API SAQ : chaque appel retourne un sous-ensemble
   * des vins disponibles. Le updateOrCreate() sur code_saq déduplique
   * automatiquement entre les passes.
   *
   * @var array
   */
  private const TRIS = [
    ['attribute' => 'name', 'direction' => 'ASC'],
    ['attribute' => 'name', 'direction' => 'DESC'],
    ['attribute' => 'price', 'direction' => 'ASC'],
    ['attribute' => 'price', 'direction' => 'DESC'],
  ];

  /**
   * Point d'entrée principal de la commande.
   *
   * @return int
   */
  public function handle(): int
  {
    try {
      $pageSize = self::TAILLE_PAGE;
      $totalImporte = 0;
      $totalIgnores = 0;

      foreach (self::TRIS as $tri) {
        $triLabel = "{$tri['attribute']} {$tri['direction']}";
        $this->info("=== Passe de tri : {$triLabel} ===");
        $page = 1;

      while (true) {
        $this->info("Import de la page {$page}...");

        $response = $this->envoyerRequeteSaq($page, $pageSize, $tri);

        if ($response->failed()) {
          $this->error("Erreur API SAQ à la page {$page}.");
          $this->line($response->body());

          return self::FAILURE;
        }

        $data = $response->json();
        $items = $data['data']['productSearch']['items'] ?? [];

        /**
         * Arrêt normal si aucune donnée n'est retournée.
         */
        if (empty($items)) {
          $this->info("Aucun item retourné à la page {$page}. Fin de la passe.");
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

        $page++;

        /**
         * Arrêt de sécurité pour éviter une boucle infinie.
         */
        if ($page > self::LIMITE_PAGES_SECURITE) {
          $this->warn('Arrêt de sécurité atteint : limite maximale de pages dépassée.');
          break;
        }

        sleep(1);
      }
      }

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
   * @param array{attribute: string, direction: string} $tri
   * @return \Illuminate\Http\Client\Response
   */
  private function envoyerRequeteSaq(int $page, int $pageSize, array $tri)
  {
    $attr = $tri['attribute'];
    $dir = $tri['direction'];
    $query = <<<GRAPHQL
        {
          productSearch(phrase: "", filter: [{attribute: "categoryPath", eq: "produits/vin"}, {attribute: "availability_front", in: ["En ligne", "En succursale", "Disponible bientôt", "En loterie"]}], sort: [{attribute: "$attr", direction: $dir}], page_size: $pageSize, current_page: $page) {
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
   * Retourne true uniquement lorsqu'une nouvelle bouteille
   * est réellement créée dans la base de données.
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

    if (empty($codeSaq) || empty($nom)) {
      $this->warn('Produit ignoré : code SAQ ou nom manquant.');
      return false;
    }

    if (empty($type)) {
      $this->warn("Produit ignoré : type manquant pour {$nom} ({$codeSaq}).");
      return false;
    }

    if (!$this->estUnVin($type)) {
      $this->line("Produit ignoré (type non retenu) : {$nom} | type = {$type}");
      return false;
    }

    if ($this->trouverAttribut($attributes, 'type_contenant') !== 'Verre') {
      $this->line("Produit ignoré (contenant non retenu) : {$nom}");
      return false;
    }

    $bouteille = Bouteille::updateOrCreate(
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

    /**
     * On considère comme "importé" uniquement un nouvel enregistrement.
     * Une bouteille déjà existante mise à jour ne compte pas
     * comme une nouvelle importation.
     */
    return $bouteille->wasRecentlyCreated;
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
   * Cette version est plus permissive afin d'éviter
   * d'exclure des vins valides à cause de libellés différents.
   *
   * @param string $type
   * @return bool
   */
  private function estUnVin(string $type): bool
  {
    $type = mb_strtolower(trim($type));

    return str_contains($type, 'vin')
      || str_contains($type, 'porto')
      || str_contains($type, 'champagne');
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
