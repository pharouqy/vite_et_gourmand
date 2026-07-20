<?php

/**
 * Partial header — inclus en tête de chaque vue
 * La variable $titre_page doit être définie avant l'include
 */
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($titre_page ?? 'Vite & Gourmand') ?> — Vite & Gourmand</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;600;700&family=Rubik:wght@400;500&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">

    <!-- Charte graphique -->
    <link href="/assets/css/style.css" rel="stylesheet">
</head>

<body>

    <!-- ── Navigation ─────────────────────────────────────────────────── -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="/">🍽 Vite & Gourmand</a>

            <!-- Burger mobile -->
            <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/menus">Nos menus</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/contact">Contact</a>
                    </li>
                </ul>

                <!-- Zone utilisateur à droite -->
                <div class="d-flex gap-2 align-items-center">
                    <?php if (est_connecte()): ?>
                        <?php $role = role_actuel(); ?>

                        <?php if ($role === ROLE_ADMIN): ?>
                            <a href="/admin/statistiques" class="btn btn-ghost btn-sm">
                                Admin
                            </a>
                        <?php elseif ($role === ROLE_EMPLOYE): ?>
                            <a href="/employe/commandes" class="btn btn-ghost btn-sm">
                                Espace employé
                            </a>
                        <?php else: ?>
                            <a href="/compte/commandes" class="btn btn-ghost btn-sm">
                                Mon compte
                            </a>
                        <?php endif; ?>

                        <a href="/deconnexion" class="btn btn-outline btn-sm">
                            Déconnexion
                        </a>
                    <?php else: ?>
                        <a href="/connexion" class="btn btn-ghost btn-sm">Connexion</a>
                        <a href="/inscription" class="btn btn-primary btn-sm">S'inscrire</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- ── Alertes flash (succès / erreur) ───────────────────────────── -->
    <?php require_once dirname(__DIR__) . '/partials/alertes.php'; ?>

    <!-- ── Contenu principal ─────────────────────────────────────────── -->