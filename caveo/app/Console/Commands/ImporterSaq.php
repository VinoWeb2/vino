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
    private array $ignoredItems = [];

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

            while (true) {
                $this->info("Import de la page {$page}...");

                $response = $this->envoyerRequeteSaq($page, $pageSize);

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
                    $this->info("Aucun item retourné à la page {$page}. Fin de l'import.");
                    break;
                }

                $totalImporteAvantPage = $totalImporte;

                foreach ($items as $item) {
                    if ($this->traiterItem($item)) {
                        $totalImporte++;
                    } else {
                        $totalIgnores++;
                    }
                }

                $this->info("Page {$page} terminée. Importés : {$totalImporte} | Ignorés : {$totalIgnores}");

                /**
                 * Arrêt intelligent :
                 * si aucune nouvelle bouteille n'a été importée sur la page courante,
                 * on considère que l'import est arrivé à sa fin utile.
                 */
                // if ($totalImporte === $totalImporteAvantPage) {
                //   $this->warn("Aucune nouvelle bouteille ajoutée à la page {$page}. Fin de l'import.");
                //   break;
                // }

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

            $this->info('Import terminé avec succès.');
            $this->info("Nombre total de vins importés : {$totalImporte}");
            $this->info("Nombre total d’items ignorés : {$totalIgnores}");

            file_put_contents(
                storage_path('app/saq_ignored.json'),
                json_encode($this->ignoredItems, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            );

            $this->info('Fichier JSON des items ignorés créé : storage/app/saq_ignored.json');

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

        if (empty($codeSaq) || empty($nom)) {
            $this->ajouterItemIgnore($item, 'code ou nom manquant');
            return false;
        }

        $isWine = $this->estUnVin($attributes);

        if (!$isWine) {
            $this->ajouterItemIgnore($item, 'pas un vin');
            return false;
        }

        $typeContenant = $this->trouverAttribut($attributes, 'type_contenant');

        if ($typeContenant && !$this->estUneVraieBouteille($typeContenant)) {
            $this->ajouterItemIgnore($item, 'pas une bouteille (contenant)');
            return false;
        }

        $cepage = $this->trouverAttribut($attributes, 'cepage');
        $type = $this->trouverAttribut($attributes, 'identite_produit');
        $pays = $this->trouverAttribut($attributes, 'pays_origine');
        $millesime = $this->trouverAttribut($attributes, 'millesime_produit');
        $tauxAlcool = $this->trouverAttribut($attributes, 'pourcentage_alcool_par_volume');
        $format = $this->trouverAttribut($attributes, 'format_contenant_ml');
        $pastilleGout = $this->trouverAttribut($attributes, 'pastille_gout');

        $image = $this->normaliserUrlImage(
            $item['product']['image']['url'] ?? null
        );

        $bouteille = Bouteille::updateOrCreate(
            ['code_saq' => $codeSaq],
            [
                'nom' => $nom,
                'type' => $type,
                'pays' => $pays,
                'cepage' => $cepage,
                'millesime' => is_numeric($millesime) ? (int) $millesime : null,
                'taux_alcool' => $this->nettoyerDecimal($tauxAlcool),
                'prix' => $this->nettoyerDecimal(
                    $item['product']['price_range']['minimum_price']['regular_price']['value'] ?? null
                ),
                'format' => $this->nettoyerEntier($format),
                'image' => $image,
                'pastille_gout' => $pastilleGout,
                'est_saq' => true,
            ]
        );

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

    /**
     * Détermine si un produit est un vin selon ses attributs.
     *
     * @param array $attributes
     * @return bool
     */
    private function estUnVin(array $attributes): bool
    {
        $type = mb_strtolower($this->trouverAttribut($attributes, 'identite_produit') ?? '');
        $cepage = mb_strtolower($this->trouverAttribut($attributes, 'cepage') ?? '');
        $gamme = mb_strtolower($this->trouverAttribut($attributes, 'gamme_marketing') ?? '');
        $designation = mb_strtolower($this->trouverAttribut($attributes, 'designation_reglementee') ?? '');
        $name = mb_strtolower($this->trouverAttribut($attributes, 'sanitized_name') ?? '');

        $keywords = [
            'vin',
            'porto',
            'mousseux',
            'rosé',
            'rouge',
            'blanc',
            'fortifié'
        ];

        $fields = [$type, $designation, $gamme];

        foreach ($keywords as $keyword) {
            foreach ($fields as $field) {
                if (!empty($field) && str_contains($field, $keyword)) {
                    return true;
                }
            }
        }

        if (str_contains($designation, 'vin')) return true;

        if (!empty($cepage)) {
            $grapes = [
                'pinot', 'chardonnay', 'sauvignon', 'cabernet',
                'merlot', 'syrah', 'riesling', 'gamay',
                'malbec', 'nebbiolo'
            ];

            foreach ($grapes as $grape) {
                if (str_contains($cepage, $grape)) {
                    return true;
                }
            }
        }

        if (str_contains($name, 'pinot') || str_contains($name, 'vin')) return true;

        if (str_contains($gamme, 'cellier')) return true;

        return false;
    }

    /**
     * Vérifie si le contenant est une bouteille en verre.
     *
     * @param string|null $typeContenant
     * @return bool
     */
    private function estUneVraieBouteille(?string $typeContenant): bool
    {
        if (!$typeContenant) {
            return false;
        }

        $typeContenant = mb_strtolower(trim($typeContenant));

        return $typeContenant === 'verre';
    }

    /**
     * Ajoute un item ignoré dans le registre interne.
     *
     * @param array $item
     * @param string $raison
     * @return void
     */
    private function ajouterItemIgnore(array $item, string $raison): void
    {
        $this->ignoredItems[] = [
            'raison' => $raison,
            'nom' => $item['productView']['name'] ?? null,
        ];

        $this->line("[IGNORÉ] {$raison} | " . ($item['productView']['name'] ?? 'sans nom'));
    }
}