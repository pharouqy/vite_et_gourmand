<?php

declare(strict_types=1);

// ── Chargement des variables d'environnement ─────────────────────────
// Le fichier .env est dans src/ (un niveau au-dessus de public/)
$env_path = dirname(__DIR__) . '/.env';

if (is_file($env_path)) {
    $lignes = file($env_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lignes as $ligne) {
        // Ignorer les commentaires et les lignes sans '='
        if (str_starts_with(trim($ligne), '#') || !str_contains($ligne, '=')) {
            continue;
        }
        [$cle, $valeur] = explode('=', $ligne, 2);
        putenv(trim($cle) . '=' . trim($valeur));
    }
}

// ── Chargement de l'autoloader Composer ─────────────────────────────
require_once dirname(__DIR__) . '/vendor/autoload.php';

// ── Chargement des configurations ───────────────────────────────────
require_once dirname(__DIR__) . '/app/config/database.php';
require_once dirname(__DIR__) . '/app/config/mongo.php';
require_once dirname(__DIR__) . '/app/config/mail.php';
require_once dirname(__DIR__) . '/app/config/constants.php';

// ── Chargement du core ───────────────────────────────────────────────
require_once dirname(__DIR__) . '/app/core/helpers.php';
require_once dirname(__DIR__) . '/app/core/request.php';
require_once dirname(__DIR__) . '/app/core/response.php';
require_once dirname(__DIR__) . '/app/core/csrf.php';
require_once dirname(__DIR__) . '/app/core/auth_middleware.php';

// ── Chargement des contrôleurs ───────────────────────────────────────
require_once dirname(__DIR__) . '/app/controllers/home_controller.php';
require_once dirname(__DIR__) . '/app/controllers/auth_controller.php';
require_once dirname(__DIR__) . '/app/controllers/menu_controller.php';
require_once dirname(__DIR__) . '/app/controllers/commande_controller.php';
require_once dirname(__DIR__) . '/app/controllers/compte_controller.php';
require_once dirname(__DIR__) . '/app/controllers/contact_controller.php';
require_once dirname(__DIR__) . '/app/controllers/employe_controller.php';
require_once dirname(__DIR__) . '/app/controllers/admin_controller.php';
require_once dirname(__DIR__) . '/app/controllers/api_controller.php';

// ── Chargement des modèles ───────────────────────────────────────────
require_once dirname(__DIR__) . '/app/models/utilisateur_model.php';

// ── Démarrage sécurisé de la session ────────────────────────────────
require_once dirname(__DIR__) . '/app/core/router.php';

// ── Lancement du routeur ─────────────────────────────────────────────
router_dispatch();
