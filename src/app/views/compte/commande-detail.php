<?php
$titre_page = 'Commande ' . $commande['numero_commande'];
require dirname(__DIR__) . '/partials/header.php';
$statut = $commande['statut'];
$peut_modifier = in_array($statut, [STATUT_EN_ATTENTE]);
$peut_suivre = !in_array($statut, [STATUT_EN_ATTENTE, STATUT_ANNULEE]);
?>

<div class="container py-xl">

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="/compte/commandes" class="btn btn-ghost btn-sm">
            ← Mes commandes
        </a>
        <h1 class="h4 mb-0" style="color:var(--color-primary);">
            Commande
            <code><?= e($commande['numero_commande']) ?></code>
        </h1>
        <?php $badge = badge_statut($statut); ?>
        <span class="badge <?= $badge['classe'] ?>">
            <?= e($statut) ?>
        </span>
    </div>

    <div class="row g-4">

        <!-- ── Détails commande ───────────────────────────────────── -->
        <div class="col-lg-7">
            <div class="card mb-4">
                <div class="card-header">
                    <h2 class="h5 mb-0" style="color:#fff;">
                        Détails de la prestation
                    </h2>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td class="text-muted">Menu</td>
                            <td><strong>
                                    <?= e($commande['menu_titre']) ?>
                                </strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Personnes</td>
                            <td><?= (int) $commande['nombre_personne'] ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Date</td>
                            <td><?= date_fr($commande['date_prestation']) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Heure</td>
                            <td><?= e($commande['heure_livraison']) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Prix menu</td>
                            <td><?= prix_format((float) $commande['prix_menu']) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Livraison</td>
                            <td>
                                <?php if ((float) $commande['prix_livraison'] === 0.0): ?>
                                    <span class="text-success">Gratuite</span>
                                <?php else: ?>
                                    <?= prix_format((float) $commande['prix_livraison']) ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr style="font-size:var(--font-size-lg);">
                            <td><strong>Total</strong></td>
                            <td><strong class="card-prix">
                                    <?= prix_format(
                                        (float) $commande['prix_menu']
                                        + (float) $commande['prix_livraison']
                                    ) ?>
                                </strong></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- ── Actions (annuler / modifier) ──────────────────── -->
            <?php if ($peut_modifier): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h2 class="h5 mb-0" style="color:#fff;">
                            Modifier / Annuler
                        </h2>
                    </div>
                    <div class="card-body">
                        <p class="text-muted" style="font-size:var(--font-size-sm);">
                            Modification possible tant que la commande
                            n'est pas "acceptée" par notre équipe.
                        </p>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="/compte/modifier?numero=<?= e($commande['numero_commande']) ?>"
                                class="btn btn-outline btn-sm">
                                ✏️ Modifier la date / heure
                            </a>
                            <form action="/compte/annuler" method="POST"
                                onsubmit="return confirm('Confirmer l\'annulation ?')">
                                <input type="hidden" name="csrf_token" value="<?= e(csrf_generer()) ?>">
                                <input type="hidden" name="numero" value="<?= e($commande['numero_commande']) ?>">
                                <button type="submit" class="btn btn-sm" style="background:var(--color-danger);
                                               color:#fff;">
                                    ❌ Annuler la commande
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- ── Avis (commande terminée) ───────────────────────── -->
            <?php if ($statut === STATUT_TERMINEE && empty($commande['avis_id'])): ?>
                <div class="card" style="border:2px solid var(--color-accent);">
                    <div class="card-body text-center">
                        <p class="mb-2">⭐ Votre prestation est terminée !</p>
                        <p class="text-muted" style="font-size:var(--font-size-sm);">
                            Partagez votre expérience.
                        </p>
                        <a href="/compte/avis?numero=<?= e($commande['numero_commande']) ?>" class="btn btn-accent">
                            Laisser un avis
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- ── Frise de suivi ─────────────────────────────────────── -->
        <?php if ($peut_suivre && !empty($commande['historique'])): ?>
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header">
                        <h2 class="h5 mb-0" style="color:#fff;">
                            Suivi de commande
                        </h2>
                    </div>
                    <div class="card-body">
                        <div style="position:relative;
                                    padding-left:var(--space-xl);">

                            <!-- Ligne verticale -->
                            <div style="position:absolute;left:12px;top:0;
                                        bottom:0;width:2px;
                                        background:var(--color-border);"></div>

                            <?php foreach ($commande['historique'] as $i => $h): ?>
                                <?php
                                $est_dernier = $i === count($commande['historique']) - 1;
                                $couleur = $est_dernier
                                    ? 'var(--color-primary)'
                                    : 'var(--color-sage)';
                                ?>
                                <div style="position:relative;
                                            margin-bottom:var(--space-lg);">

                                    <!-- Point sur la frise -->
                                    <div style="position:absolute;
                                                left:-20px;top:4px;
                                                width:12px;height:12px;
                                                border-radius:50%;
                                                background:<?= $couleur ?>;
                                                border:2px solid #fff;
                                                box-shadow:0 0 0 2px <?= $couleur ?>;">
                                    </div>

                                    <p style="font-weight:600;
                                              color:<?= $couleur ?>;
                                              margin-bottom:2px;
                                              font-size:var(--font-size-sm);">
                                        <?= e($h['statut']) ?>
                                    </p>
                                    <p class="text-muted mb-0" style="font-size:var(--font-size-xs);">
                                        <?= date(
                                            'd/m/Y à H:i',
                                            strtotime($h['created_at'])
                                        ) ?>
                                    </p>
                                    <?php if ($h['commentaire']): ?>
                                        <p style="font-size:var(--font-size-xs);
                                                  color:var(--color-text-muted);
                                                  margin-top:2px;
                                                  font-style:italic;">
                                            <?= e($h['commentaire']) ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

</main>
<?php require dirname(__DIR__) . '/partials/footer.php'; ?>