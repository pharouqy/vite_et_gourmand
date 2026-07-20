<?php

declare(strict_types=1);

/**
 * Démarre la session de façon sécurisée.
 * Appelé une seule fois, ici, avant tout dispatch.
 */
function session_start_secure(): void
{
    // Cookies de session inaccessibles en JavaScript (protection XSS)
    ini_set('session.cookie_httponly', '1');

    // En production : cookies uniquement sur HTTPS
    // ini_set('session.cookie_secure', '1');

    // Empêche l'injection d'ID de session via l'URL
    ini_set('session.use_only_cookies', '1');

    session_start();
}

/**
 * Table de routage de l'application.
 * Format : 'METHODE /chemin' => 'nom_de_la_fonction_controleur'
 *
 * Convention : les routes API retournent du JSON,
 * toutes les autres retournent une vue HTML.
 */
function get_routes(): array
{
    return [
        // ── Pages publiques ──────────────────────────────────────────
        'GET /'                     => 'home_index',
        'GET /contact'              => 'contact_index',
        'POST /contact'             => 'contact_envoyer',
        'GET /mentions-legales'     => 'home_mentions_legales',
        'GET /cgv'                  => 'home_cgv',

        // ── Authentification ─────────────────────────────────────────
        'GET /inscription'          => 'auth_inscription_form',
        'POST /inscription'         => 'auth_inscription_traiter',
        'GET /connexion'            => 'auth_connexion_form',
        'POST /connexion'           => 'auth_connexion_traiter',
        'GET /deconnexion'          => 'auth_deconnexion',
        'GET /mot-de-passe-oublie'  => 'auth_oublie_form',
        'POST /mot-de-passe-oublie' => 'auth_oublie_traiter',
        'GET /reinitialisation'     => 'auth_reset_form',
        'POST /reinitialisation'    => 'auth_reset_traiter',

        // ── Catalogue menus (public) ─────────────────────────────────
        'GET /menus'                => 'menu_liste',
        'GET /menus/detail'         => 'menu_detail',

        // ── Tunnel de commande (authentifié) ─────────────────────────
        'GET /commande/prestation'  => 'commande_prestation_form',
        'POST /commande/prestation' => 'commande_prestation_traiter',
        'GET /commande/menu'        => 'commande_menu_form',
        'POST /commande/menu'       => 'commande_menu_traiter',
        'GET /commande/recapitulatif'  => 'commande_recap',
        'POST /commande/confirmer'  => 'commande_confirmer',

        // ── Espace client ────────────────────────────────────────────
        'GET /compte/commandes'     => 'compte_mes_commandes',
        'GET /compte/commande'      => 'compte_commande_detail',
        'POST /compte/annuler'      => 'compte_annuler',
        'GET /compte/modifier'      => 'compte_modifier_form',
        'POST /compte/modifier'     => 'compte_modifier_traiter',
        'GET /compte/profil'        => 'compte_profil',
        'POST /compte/profil'       => 'compte_profil_traiter',
        'GET /compte/avis'          => 'compte_avis_form',
        'POST /compte/avis'         => 'compte_avis_traiter',

        // ── Espace employé ───────────────────────────────────────────
        'GET /employe/menus'            => 'employe_menus_liste',
        'GET /employe/menus/creer'      => 'employe_menu_creer_form',
        'POST /employe/menus/creer'     => 'employe_menu_creer_traiter',
        'GET /employe/menus/modifier'   => 'employe_menu_modifier_form',
        'POST /employe/menus/modifier'  => 'employe_menu_modifier_traiter',
        'POST /employe/menus/supprimer' => 'employe_menu_supprimer',
        'GET /employe/plats'            => 'employe_plats_liste',
        'GET /employe/horaires'         => 'employe_horaires',
        'POST /employe/horaires'        => 'employe_horaires_traiter',
        'GET /employe/commandes'        => 'employe_commandes_liste',
        'GET /employe/commande'         => 'employe_commande_detail',
        'POST /employe/commande/statut' => 'employe_commande_statut',
        'GET /employe/avis'             => 'employe_avis_moderation',
        'POST /employe/avis/valider'    => 'employe_avis_valider',

        // ── Espace admin ─────────────────────────────────────────────
        'GET /admin/employes'           => 'admin_employes_liste',
        'GET /admin/employes/creer'     => 'admin_employe_creer_form',
        'POST /admin/employes/creer'    => 'admin_employe_creer_traiter',
        'POST /admin/employes/desactiver' => 'admin_employe_desactiver',
        'GET /admin/statistiques'       => 'admin_statistiques',
        'GET /admin/chiffre-affaires'   => 'admin_ca',

        // ── API JSON ─────────────────────────────────────────────────
        'GET /api/menus'                => 'api_menus_liste',
        'GET /api/menus/detail'         => 'api_menu_detail',
        'GET /api/admin/stats-menus'    => 'api_admin_stats',
    ];
}

/**
 * Dispatch : lit la méthode HTTP et l'URI,
 * trouve la route correspondante, appelle le contrôleur.
 */
function router_dispatch(): void
{
    session_start_secure();

    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $uri    = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

    // Normalise : retire le slash final sauf pour '/'
    if ($uri !== '/' && str_ends_with($uri, '/')) {
        $uri = rtrim($uri, '/');
    }

    $key    = $method . ' ' . $uri;
    $routes = get_routes();

    if (isset($routes[$key])) {
        $fonction = $routes[$key];

        // Vérifie que la fonction contrôleur existe
        if (!function_exists($fonction)) {
            http_response_code(500);
            die("Erreur : contrôleur '{$fonction}' introuvable.");
        }

        $fonction();
        return;
    }

    // Aucune route trouvée → 404
    http_response_code(404);
    require dirname(__DIR__) . '/views/public/404.php';
}