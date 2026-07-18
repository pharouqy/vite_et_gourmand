<?php
/**
 * Initialisation des stats MongoDB depuis MySQL
 * À exécuter UNE FOIS après le seed SQL
 * Usage : docker exec Vite_et_Gourmand php scripts/sync_stats_mongo.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/config/mongo.php';

$pdo        = getPDO();
$collection = getMongoCollection('stats_menus');

// Supprime les documents existants pour repartir propre
$collection->drop();

// Récupère les stats agrégées depuis MySQL
$sql = "
    SELECT
        m.menu_id,
        m.titre,
        COUNT(DISTINCT c.numero_commande)                          AS total_commandes,
        SUM(CASE WHEN c.statut = 'annulée' THEN 1 ELSE 0 END)    AS commandes_annulees,
        COALESCE(SUM(c.prix_menu), 0)                             AS ca_total,
        COALESCE(AVG(a.note), 0)                                  AS note_moyenne,
        COUNT(DISTINCT a.avis_id)                                 AS total_avis
    FROM menu m
    LEFT JOIN commande c ON c.menu_id       = m.menu_id
    LEFT JOIN avis     a ON a.commande_id   = c.numero_commande
                        AND a.statut        = 'publié'
    GROUP BY m.menu_id, m.titre
";

$menus = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

foreach ($menus as $menu) {
    $totalCommandes = (int) $menu['total_commandes'];
    $tauxConversion = 0;

    // taux_conversion = (commandes non annulées / total) * 100
    if ($totalCommandes > 0) {
        $actives        = $totalCommandes - (int) $menu['commandes_annulees'];
        $tauxConversion = round(($actives / $totalCommandes) * 100, 2);
    }

    $collection->insertOne([
        'menu_id'       => (int)   $menu['menu_id'],
        'titre'         => (string)$menu['titre'],
        'stats'         => [
            'total_vues'          => 0,       // incrémenté en temps réel (US-4.6)
            'total_commandes'     => $totalCommandes,
            'commandes_annulees'  => (int)   $menu['commandes_annulees'],
            'taux_conversion'     => $tauxConversion,
            'ca_total'            => (float) $menu['ca_total'],
            'note_moyenne'        => round((float) $menu['note_moyenne'], 2),
            'total_avis'          => (int)   $menu['total_avis'],
        ],
        'last_updated'  => new MongoDB\BSON\UTCDateTime(),
    ]);

    echo "✅ Synchro menu_id {$menu['menu_id']} : {$menu['titre']}\n";
}

echo "\n✅ Collection stats_menus initialisée avec " . count($menus) . " documents.\n";