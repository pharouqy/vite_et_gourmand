<?php
$titre_page = $menu['titre'];
require dirname(__DIR__) . '/partials/header.php';
?>

<div class="container py-xl">

    <!-- ── Fil d'Ariane ─────────────────────────────────────────────── -->
    <nav aria-label="Fil d'Ariane" class="mb-4">
        <ol class="breadcrumb"
            style="font-size: var(--font-size-sm);">
            <li class="breadcrumb-item">
                <a href="/">Accueil</a>
            </li>
            <li class="breadcrumb-item">
                <a href="/menus">Nos menus</a>
            </li>
            <li class="breadcrumb-item active">
                <?= e($menu['titre']) ?>
            </li>
        </ol>
    </nav>

    <div class="row g-5">

        <!-- ── Colonne principale ─────────────────────────────────────── -->
        <div class="col-lg-8">

            <!-- En-tête du menu -->
            <div class="mb-4">
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <?php if ($menu['regime']): ?>
                        <span class="badge badge-regime">
                            <?= e($menu['regime']) ?>
                        </span>
                    <?php endif; ?>
                    <?php if ($menu['theme']): ?>
                        <span class="badge badge-secondary">
                            <?= e($menu['theme']) ?>
                        </span>
                    <?php endif; ?>
                    <?php if ((int)$menu['quantite_restante'] > 0): ?>
                        <span class="badge badge-accepte">Disponible</span>
                    <?php else: ?>
                        <span class="badge badge-annulee">Complet</span>
                    <?php endif; ?>
                </div>

                <h1 style="color: var(--color-primary);">
                    <?= e($menu['titre']) ?>
                </h1>

                <p class="mt-3"
                   style="font-size: var(--font-size-lg);
                          line-height: 1.7;">
                    <?= e($menu['description'] ?? '') ?>
                </p>
            </div>

            <!-- Liste des plats -->
            <?php if (!empty($menu['plats'])): ?>
                <h2 class="h4 mb-3" style="color: var(--color-primary);">
                    🍽 Composition du menu
                </h2>

                <div class="row g-3 mb-4">
                    <?php foreach ($menu['plats'] as $plat): ?>
                        <div class="col-sm-6">
                            <div class="card h-100">
                                <?php if ($plat['photo']): ?>
                                    <img src="<?= e($plat['photo']) ?>"
                                         alt="<?= e($plat['titre_plat']) ?>"
                                         class="card-img-top"
                                         style="height:160px; object-fit:cover;">
                                <?php else: ?>
                                    <div style="height:100px;
                                                background: linear-gradient(135deg,
                                                    var(--color-primary) 0%,
                                                    var(--color-primary-light) 100%);
                                                display:flex;
                                                align-items:center;
                                                justify-content:center;
                                                font-size:2rem;">
                                        🍴
                                    </div>
                                <?php endif; ?>

                                <div class="card-body">
                                    <h3 class="h6 mb-1">
                                        <?= e($plat['titre_plat']) ?>
                                    </h3>
                                    <?php if ($plat['allergenes']): ?>
                                        <p class="text-muted mb-0"
                                           style="font-size:var(--font-size-xs);">
                                            ⚠️ Allergènes :
                                            <?= e($plat['allergenes']) ?>
                                        </p>
                                    <?php else: ?>
                                        <p class="text-muted mb-0"
                                           style="font-size:var(--font-size-xs);">
                                            ✅ Sans allergènes déclarés
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- ⚠️ Conditions du menu — mis en évidence comme demandé -->
            <div class="alerte alerte-info mb-4">
                <h3 class="h6 mb-2">
                    ⚠️ Conditions importantes à lire avant de commander
                </h3>
                <ul class="mb-0"
                    style="font-size: var(--font-size-sm);">
                    <li>
                        Nombre minimum de personnes :
                        <strong>
                            <?= (int)$menu['nombre_personne_minimum'] ?>
                            personnes
                        </strong>
                    </li>
                    <li>
                        Une remise de <strong>10%</strong> est appliquée
                        pour toute commande dépassant de 5 personnes
                        le minimum requis.
                    </li>
                    <li>
                        Livraison incluse dans Bordeaux.
                        Hors Bordeaux : forfait 5 DA +
                        0,59 DA/km supplémentaire.
                    </li>
                    <?php if ($menu['regime']): ?>
                        <li>
                            Ce menu est certifié
                            <strong><?= e($menu['regime']) ?></strong>.
                        </li>
                    <?php endif; ?>
                </ul>
            </div>

        </div>

        <!-- ── Colonne latérale — récapitulatif et commande ───────────── -->
        <div class="col-lg-4">
            <div class="card"
                 style="position: sticky; top: var(--space-lg);">

                <div class="card-header">
                    <h2 class="h5 mb-0" style="color:#fff;">
                        Récapitulatif
                    </h2>
                </div>

                <div class="card-body">

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Prix / personne</span>
                        <strong class="card-prix">
                            <?= prix_format((float)$menu['prix_par_personne']) ?>
                        </strong>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Minimum</span>
                        <strong>
                            <?= (int)$menu['nombre_personne_minimum'] ?> pers.
                        </strong>
                    </div>

                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Total minimum</span>
                        <strong class="card-prix">
                            <?= prix_format(
                                (float)$menu['prix_par_personne']
                                * (int)$menu['nombre_personne_minimum']
                            ) ?>
                        </strong>
                    </div>

                    <hr>

                    <?php
                    // URL de destination selon authentification
                    $url_commande = '/commande/prestation?menu_id='
                                  . (int)$menu['menu_id'];
                    $url_bouton   = est_connecte()
                        ? $url_commande
                        : '/connexion?redirect=' . urlencode($url_commande);
                    ?>

                    <?php if ((int)$menu['quantite_restante'] > 0): ?>
                        <a href="<?= e($url_bouton) ?>"
                           class="btn btn-primary w-100 mb-2">
                            Commander ce menu
                        </a>

                        <?php if (!est_connecte()): ?>
                            <p class="text-center text-muted"
                               style="font-size: var(--font-size-xs);">
                                Vous serez invité à vous connecter
                                avant de passer commande.
                            </p>
                        <?php endif; ?>

                    <?php else: ?>
                        <button class="btn btn-outline w-100" disabled>
                            Menu complet — indisponible
                        </button>
                    <?php endif; ?>

                    <a href="/menus"
                       class="btn btn-ghost w-100 mt-2">
                        ← Retour aux menus
                    </a>

                </div>
            </div>
        </div>

    </div>
</div>

</main>
<?php require dirname(__DIR__) . '/partials/footer.php'; ?>