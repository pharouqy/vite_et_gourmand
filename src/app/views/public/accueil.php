<?php
$titre_page = 'Accueil';
require dirname(__DIR__) . '/partials/header.php';
?>

<!-- ── Hero section ─────────────────────────────────────────────────── -->
<section style="background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-light) 100%);
                padding: var(--space-3xl) 0;">
    <div class="container text-center">
        <h1 style="color: var(--color-accent);
                   font-size: clamp(2rem, 5vw, 3.5rem);
                   margin-bottom: var(--space-lg);">
            L'excellence culinaire<br>à votre service
        </h1>
        <p style="color: rgba(255,255,255,0.85);
                  font-size: var(--font-size-lg);
                  max-width: 600px;
                  margin: 0 auto var(--space-xl);">
            Traiteur événementiel haut de gamme pour vos anniversaires,
            mariages et événements d'entreprise à Bordeaux et alentours.
        </p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <a href="/menus" class="btn btn-accent">
                Découvrir nos menus
            </a>
            <a href="/contact" class="btn btn-outline"
                style="border-color:#fff; color:#fff;">
                Nous contacter
            </a>
        </div>
    </div>
</section>

<!-- ── Points forts ──────────────────────────────────────────────────── -->
<section class="py-xl" style="background: var(--color-surface);">
    <div class="container">
        <div class="row text-center g-4">

            <div class="col-md-4">
                <div style="font-size: 2.5rem; margin-bottom: var(--space-sm);">
                    🍽
                </div>
                <h3 class="h5" style="color: var(--color-primary);">
                    Cuisine raffinée
                </h3>
                <p class="text-muted" style="font-size: var(--font-size-sm);">
                    Des menus élaborés par nos chefs pour chaque occasion,
                    avec des produits frais et de saison.
                </p>
            </div>

            <div class="col-md-4">
                <div style="font-size: 2.5rem; margin-bottom: var(--space-sm);">
                    🚚
                </div>
                <h3 class="h5" style="color: var(--color-primary);">
                    Livraison à domicile
                </h3>
                <p class="text-muted" style="font-size: var(--font-size-sm);">
                    Livraison incluse dans Bordeaux,
                    et disponible dans toute la région.
                </p>
            </div>

            <div class="col-md-4">
                <div style="font-size: 2.5rem; margin-bottom: var(--space-sm);">
                    ✅
                </div>
                <h3 class="h5" style="color: var(--color-primary);">
                    Commande en ligne
                </h3>
                <p class="text-muted" style="font-size: var(--font-size-sm);">
                    Réservez votre prestation en quelques clics,
                    24h/24 et 7j/7.
                </p>
            </div>

        </div>
    </div>
</section>

<!-- ── Avis clients ───────────────────────────────────────────────────── -->
<?php if (!empty($avis)): ?>
    <section class="py-xl" style="background: var(--color-bg);">
        <div class="container">

            <h2 class="text-center mb-2" style="color: var(--color-primary);">
                Ce que disent nos clients
            </h2>
            <p class="text-center text-muted mb-4"
                style="font-size: var(--font-size-sm);">
                Avis vérifiés — déposés par nos clients après leur prestation
            </p>

            <!-- Carrousel Bootstrap -->
            <div id="carouselAvis"
                class="carousel slide"
                data-bs-ride="carousel"
                data-bs-interval="5000">

                <div class="carousel-inner">
                    <?php foreach ($avis as $index => $a): ?>
                        <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                            <div class="row justify-content-center">
                                <div class="col-md-7 col-lg-6">
                                    <div class="card text-center p-4">
                                        <div class="card-body">

                                            <!-- Étoiles -->
                                            <div class="etoiles mb-3"
                                                aria-label="Note : <?= (int)$a['note'] ?> sur 5">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <?= $i <= (int)$a['note'] ? '★' : '☆' ?>
                                                <?php endfor; ?>
                                            </div>

                                            <!-- Commentaire -->
                                            <blockquote class="mb-3"
                                                style="font-style: italic;
                                                           color: var(--color-text);
                                                           font-size: var(--font-size-lg);">
                                                "<?= e($a['description'] ?? '') ?>"
                                            </blockquote>

                                            <!-- Auteur -->
                                            <cite style="color: var(--color-primary);
                                                     font-weight: 600;
                                                     font-style: normal;">
                                                — <?= e($a['prenom']) ?>
                                                <?= e($a['initiale_nom']) ?>.
                                            </cite>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Contrôles carrousel -->
                <?php if (count($avis) > 1): ?>
                    <button class="carousel-control-prev"
                        type="button"
                        data-bs-target="#carouselAvis"
                        data-bs-slide="prev"
                        style="filter: invert(1);">
                        <span class="carousel-control-prev-icon"></span>
                        <span class="visually-hidden">Précédent</span>
                    </button>
                    <button class="carousel-control-next"
                        type="button"
                        data-bs-target="#carouselAvis"
                        data-bs-slide="next"
                        style="filter: invert(1);">
                        <span class="carousel-control-next-icon"></span>
                        <span class="visually-hidden">Suivant</span>
                    </button>

                    <!-- Indicateurs (points) -->
                    <div class="carousel-indicators"
                        style="position: static;
                            margin-top: var(--space-md);">
                        <?php foreach ($avis as $index => $a): ?>
                            <button type="button"
                                data-bs-target="#carouselAvis"
                                data-bs-slide-to="<?= $index ?>"
                                <?= $index === 0 ? 'class="active"' : '' ?>
                                style="background-color: var(--color-primary);
                                       width: 10px; height: 10px;
                                       border-radius: 50%; border: none;">
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

            </div>

        </div>
    </section>
<?php endif; ?>

<!-- ── CTA final ─────────────────────────────────────────────────────── -->
<section style="background: var(--color-primary); padding: var(--space-2xl) 0;">
    <div class="container text-center">
        <h2 style="color: var(--color-accent); margin-bottom: var(--space-md);">
            Prêt à organiser votre événement ?
        </h2>
        <p style="color: rgba(255,255,255,0.85);
                  margin-bottom: var(--space-lg);">
            Consultez nos menus et passez commande en ligne en quelques minutes.
        </p>
        <a href="/menus" class="btn btn-accent">
            Voir nos menus →
        </a>
    </div>
</section>

</main>
<?php require dirname(__DIR__) . '/partials/footer.php'; ?>