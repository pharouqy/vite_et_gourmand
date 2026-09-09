<?php

declare(strict_types=1);

/**
 * Récupère les menus avec filtres optionnels.
 * Tous les paramètres sont optionnels — sans filtre, retourne tout.
 */
function menus_liste(array $filtres = []): array
{
    $pdo    = getPDO();
    $where  = ['1=1'];
    $params = [];

    // Filtre prix maximum
    if (!empty($filtres['prix_max']) && is_numeric($filtres['prix_max'])) {
        $where[]  = 'm.prix_par_personne <= :prix_max';
        $params[':prix_max'] = (float) $filtres['prix_max'];
    }

    // Filtre fourchette de prix — prix minimum
    if (!empty($filtres['prix_min']) && is_numeric($filtres['prix_min'])) {
        $where[]  = 'm.prix_par_personne >= :prix_min';
        $params[':prix_min'] = (float) $filtres['prix_min'];
    }

    // Filtre par thème
    if (!empty($filtres['theme_id']) && is_numeric($filtres['theme_id'])) {
        $where[]  = 'm.theme_id = :theme_id';
        $params[':theme_id'] = (int) $filtres['theme_id'];
    }

    // Filtre par régime
    if (!empty($filtres['regime_id']) && is_numeric($filtres['regime_id'])) {
        $where[]  = 'm.regime_id = :regime_id';
        $params[':regime_id'] = (int) $filtres['regime_id'];
    }

    // Filtre par nombre de personnes minimum
    if (!empty($filtres['nb_personnes']) && is_numeric($filtres['nb_personnes'])) {
        $where[]  = 'm.nombre_personne_minimum <= :nb_personnes';
        $params[':nb_personnes'] = (int) $filtres['nb_personnes'];
    }

    $sql = "
        SELECT
            m.menu_id,
            m.titre,
            m.nombre_personne_minimum,
            m.prix_par_personne,
            m.description,
            m.quantite_restante,
            r.libelle AS regime,
            t.libelle AS theme
        FROM menu m
        LEFT JOIN regime r ON r.regime_id = m.regime_id
        LEFT JOIN theme  t ON t.theme_id  = m.theme_id
        WHERE " . implode(' AND ', $where) . "
        ORDER BY m.prix_par_personne ASC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Récupère un menu par son ID avec ses plats et allergènes.
 */
function menu_par_id(int $menu_id): ?array
{
    $pdo  = getPDO();

    // Le menu principal
    $stmt = $pdo->prepare("
        SELECT
            m.*,
            r.libelle AS regime,
            t.libelle AS theme
        FROM menu m
        LEFT JOIN regime r ON r.regime_id = m.regime_id
        LEFT JOIN theme  t ON t.theme_id  = m.theme_id
        WHERE m.menu_id = ?
    ");
    $stmt->execute([$menu_id]);
    $menu = $stmt->fetch();

    if (!$menu) return null;

    // Les plats du menu avec leurs allergènes
    $stmt2 = $pdo->prepare("
        SELECT
            p.plat_id,
            p.titre_plat,
            p.photo,
            GROUP_CONCAT(a.libelle SEPARATOR ', ') AS allergenes
        FROM plat p
        JOIN compose co  ON co.plat_id     = p.plat_id
        LEFT JOIN contient c  ON c.plat_id = p.plat_id
        LEFT JOIN allergene a ON a.allergene_id = c.allergene_id
        WHERE co.menu_id = ?
        GROUP BY p.plat_id
    ");
    $stmt2->execute([$menu_id]);
    $menu['plats'] = $stmt2->fetchAll();

    return $menu;
}

/**
 * Récupère tous les thèmes (pour le filtre select).
 */
function themes_tous(): array
{
    return getPDO()->query('SELECT * FROM theme ORDER BY libelle')->fetchAll();
}

/**
 * Récupère tous les régimes (pour le filtre select).
 */
function regimes_tous(): array
{
    return getPDO()->query('SELECT * FROM regime ORDER BY libelle')->fetchAll();
}