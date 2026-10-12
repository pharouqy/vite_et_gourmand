<?php
$titre_page = 'Mes commandes';
require dirname(__DIR__) . '/partials/header.php';
?>

<div class="container py-xl">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 style="color:var(--color-primary);">Mes commandes</h1>
        <a href="/compte/profil" class="btn btn-outline btn-sm">
            Mon profil
        </a>
    </div>

    <?php if (empty($commandes)): ?>
        <div class="text-center py-xl">
            <p style="font-size:2.5rem;">🍽</p>
            <p class="text-muted">Vous n'avez pas encore de commande.</p>
            <a href="/menus" class="btn btn-primary mt-3">
                Découvrir nos menus
            </a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead style="background:var(--color-primary);color:#fff;">
                    <tr>
                        <th>N° commande</th>
                        <th>Menu</th>
                        <th>Date prestation</th>
                        <th>Total</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($commandes as $cmd): ?>
                        <?php
                        $total = (float) $cmd['prix_menu']
                            + (float) $cmd['prix_livraison'];
                        $badge = badge_statut($cmd['statut']);
                        ?>
                        <tr>
                            <td>
                                <code style="color:var(--color-primary);
                                             font-size:var(--font-size-sm);">
                                                            <?= e($cmd['numero_commande']) ?>
                                                        </code>
                            </td>
                            <td><?= e($cmd['menu_titre']) ?></td>
                            <td><?= date_fr($cmd['date_prestation']) ?></td>
                            <td><strong><?= prix_format($total) ?></strong></td>
                            <td>
                                <span class="badge <?= $badge['classe'] ?>">
                                    <?= e($cmd['statut']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="/compte/commande?numero=<?= e($cmd['numero_commande']) ?>"
                                    class="btn btn-ghost btn-sm">
                                    Voir →
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</div>

</main>
<?php require dirname(__DIR__) . '/partials/footer.php'; ?>