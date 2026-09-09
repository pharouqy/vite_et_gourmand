<?php

declare(strict_types=1);

/**
 * GET /api/menus
 * Retourne la liste des menus filtrés en JSON.
 * Utilisé par filtres-menus.js (AJAX sans rechargement)
 */
function api_menus_liste(): void
{
    $filtres = [
        'prix_max'     => get_param('prix_max'),
        'prix_min'     => get_param('prix_min'),
        'theme_id'     => get_param('theme_id'),
        'regime_id'    => get_param('regime_id'),
        'nb_personnes' => get_param('nb_personnes'),
    ];

    $menus = menus_liste($filtres);

    json_response([
        'success' => true,
        'count'   => count($menus),
        'menus'   => $menus,
    ]);
}

/**
 * GET /api/menus/detail
 * Retourne le détail d'un menu en JSON.
 */
function api_menu_detail(): void
{
    $menu_id = (int) get_param('id', 0);

    if (!$menu_id) {
        json_response(['success' => false, 'message' => 'ID manquant.'], 400);
    }

    $menu = menu_par_id($menu_id);

    if (!$menu) {
        json_response(['success' => false, 'message' => 'Menu introuvable.'], 404);
    }

    json_response(['success' => true, 'menu' => $menu]);
}

// Squelette Sprint 4
function api_admin_stats(): void
{
    require_role(ROLE_ADMIN);
    json_response(['success' => true, 'stats' => []]);
}