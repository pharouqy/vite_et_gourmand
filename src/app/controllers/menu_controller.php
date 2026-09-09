<?php

declare(strict_types=1);

/**
 * Page liste des menus — charge les filtres pour les selects
 * Les menus sont chargés en AJAX via /api/menus
 */
function menu_liste(): void
{
    $themes  = themes_tous();
    $regimes = regimes_tous();

    render('public/menus-liste', [
        'titre_page' => 'Nos menus',
        'themes'     => $themes,
        'regimes'    => $regimes,
    ]);
}

/**
 * Page détail d'un menu
 */
function menu_detail(): void
{
    $menu_id = (int) get_param('id', 0);

    if (!$menu_id) {
        redirect('/menus');
    }

    $menu = menu_par_id($menu_id);

    if (!$menu) {
        http_response_code(404);
        render('public/404', ['titre_page' => '404']);
        return;
    }

    render('public/menu-detail', [
        'titre_page' => $menu['titre'],
        'menu'       => $menu,
    ]);
}