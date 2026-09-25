<?php
$titre_page = 'Commander — Étape 3';
$c = $commande; // alias court
require dirname(__DIR__) . '/partials/header.php';
?>

<div class="container py-xl">

    <!-- Indicateur étapes -->
    <div class="d-flex align-items-center justify-content-center gap-3 mb-4">
        <div class="d-flex align-items-center gap-2">
            <span style="width:32px;height:32px;border-radius:50%;
                         background:var(--color-sage);color:#fff;
                         display:flex;align-items:center;
                         justify-content:center;font-weight:700;">✓</span>
            <span class="text-muted">Prestation</span>
        </div>
        <div style="height:2px;width:40px;background:var(--color-sage);"></div>
        <div class="d-flex align-items-center gap-2">
            <span style="width:32px;height:32px;border-radius:50%;
                         background:var(--color-sage);color:#fff;
                         display:flex;align-items:center;
                         justify-content:center;font-weight:700;">✓</span>
            <span class="text-muted">Menu</span>
        </div>
        <div style="height:2px;width:40px;background:var(--color-border);"></div>
        <div class="d-flex align-items-center gap-2">
            <span style="width:32px;height:32px;border-radius:50%;
                         background:var(--color-primary);color:#fff;
                         display:flex;align-items:center;
                         justify-content:center;font-weight:700;">3</span>
            <span style="font-weight:600;color:var(--color-primary);">
                Récapitulatif
            </span>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">
                    <h1 class="h4 mb-0" style="color:#fff;">
                        Récapitulatif de votre commande
                    </h1>
                </div>
                <div class="card-body">

                    <!-- Détails prestation -->
                    <h2 class="h6 mb-3"
                        style="color:var(--color-primary);
                               border-bottom:1px solid var(--color-border);
                               padding-bottom:var(--space-sm);">
                        Prestation
                    </h2>
                    <table class="table table-sm mb-4">
                        <tr>
                            <td class="text-muted">Date</td>
                            <td><strong><?= date_fr($c['date_prestation']) ?></strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Heure</td>
                            <td><strong><?= e($c['heure_livraison']) ?></strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Adresse</td>
                            <td>
                                <strong>
                                    <?= e($c['adresse_livraison']) ?>,
                                    <?= e($c['ville_livraison']) ?>
                                </strong>
                            </td>
                        </tr>
                    </table>

                    <!-- Détails menu -->
                    <h2 class="h6 mb-3"
                        style="color:var(--color-primary);
                               border-bottom:1px solid var(--color-border);
                               padding-bottom:var(--space-sm);">
                        Menu choisi
                    </h2>
                    <table class="table table-sm mb-4">
                        <tr>
                            <td class="text-muted">Menu</td>
                            <td><strong><?= e($c['menu_titre']) ?></strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Personnes</td>
                            <td><strong><?= (int)$c['nombre_personne'] ?></strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Prix / pers.</td>
                            <td><?= prix_format((float)$c['prix_par_personne']) ?></td>
                        </tr>
                        <?php if ($c['remise_appliquee']): ?>
                            <tr>
                                <td class="text-muted">Remise 10%</td>
                                <td class="text-success">
                                    − <?= prix_format((float)$c['montant_remise']) ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </table>

                    <!-- Total -->
                    <h2 class="h6 mb-3"
                        style="color:var(--color-primary);
                               border-bottom:1px solid var(--color-border);
                               padding-bottom:var(--space-sm);">
                        Détail du prix
                    </h2>
                    <table class="table table-sm mb-4">
                        <tr>
                            <td class="text-muted">Sous-total menu</td>
                            <td><?= prix_format((float)$c['prix_menu']) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Frais de livraison</td>
                            <td>
                                <?php if ((float)$c['prix_livraison'] === 0.0): ?>
                                    <span class="text-success">Gratuit</span>
                                <?php else: ?>
                                    <?= prix_format((float)$c['prix_livraison']) ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr style="font-size:var(--font-size-lg);">
                            <td><strong>TOTAL</strong></td>
                            <td>
                                <strong class="card-prix">
                                    <?= prix_format(
                                        (float)$c['prix_menu']
                                        + (float)$c['prix_livraison']
                                    ) ?>
                                </strong>
                            </td>
                        </tr>
                    </table>

                    <!-- Conditions -->
                    <div class="alerte alerte-info mb-4"
                         style="font-size:var(--font-size-sm);">
                        ⚠️ En confirmant, vous acceptez les
                        <a href="/cgv" target="_blank">
                            conditions générales de vente
                        </a>,
                        notamment les règles de retour de matériel
                        (pénalité de 600 DA après 10 jours ouvrés).
                    </div>

                    <!-- Boutons -->
                    <form action="/commande/confirmer" method="POST">
                        <input type="hidden"
                               name="csrf_token"
                               value="<?= e(csrf_generer()) ?>">
                        <button type="submit"
                                class="btn btn-accent w-100 mb-2">
                            ✅ Confirmer ma commande
                        </button>
                    </form>
                    <a href="/commande/menu"
                       class="btn btn-ghost w-100">
                        ← Modifier le menu
                    </a>

                </div>
            </div>
        </div>
    </div>
</div>

</main>
<?php require dirname(__DIR__) . '/partials/footer.php'; ?>