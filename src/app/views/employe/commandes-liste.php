<?php
$titre_page = 'Commandes';
require dirname(__DIR__) . '/partials/header.php';
?>

<div class="container py-xl">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 style="color:var(--color-primary);">Commandes</h1>
        <a href="/employe/menus" class="btn btn-ghost btn-sm">← Menus</a>
    </div>

    <!-- ── Filtres (statut + client) ─────────────────────────────── -->
    <form action="/employe/commandes" method="GET" class="filtres-container">
        <div class="row g-3 align-items-end">

            <div class="col-md-4">
                <div class="filtre-group">
                    <label for="statut">Statut</label>
                    <select id="statut" name="statut">
                        <option value="">Tous les statuts</option>
                        <?php foreach ($statuts as $s): ?>
                            <option value="<?= e($s) ?>" <?= $s === $filtre_statut ? 'selected' : '' ?>>
                                <?= e($s) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="col-md-4">
                <div class="filtre-group">
                    <label for="client">Client (nom, prénom ou email)</label>
                    <input type="text" id="client" name="client" value="<?= e($filtre_client) ?>"
                        placeholder="Ex : Sofia">
                </div>
            </div>

            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Filtrer</button>
                <a href="/employe/commandes" class="btn btn-outline">
                    Réinitialiser
                </a>
            </div>

        </div>
    </form>

    <p class="text-muted mb-3" style="font-size:var(--font-size-sm);">
        <?= count($commandes) ?>
        commande<?= count($commandes) > 1 ? 's' : '' ?> trouvée<?= count($commandes) > 1 ? 's' : '' ?>
    </p>

    <!-- ── Tableau ───────────────────────────────────────────────── -->
    <?php if (empty($commandes)): ?>
        <div class="text-center py-xl">
            <p style="font-size:2.5rem;">📭</p>
            <p class="text-muted">Aucune commande ne correspond à ces critères.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead style="background:var(--color-primary);color:#fff;">
                    <tr>
                        <th>N° commande</th>
                        <th>Client</th>
                        <th>Menu</th>
                        <th>Prestation</th>
                        <th>Total</th>
                        <th>Statut</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($commandes as $c): ?>
                        <?php
                        $total = (float) $c['prix_menu'] + (float) $c['prix_livraison'];
                        $badge = badge_statut($c['statut']);
                        ?>
                        <tr>
                            <td>
                                <code style="color:var(--color-primary);">
                                                            <?= e($c['numero_commande']) ?>
                                                        </code>
                            </td>
                            <td>
                                <strong>
                                    <?= e($c['client_prenom']) ?>
                                    <?= e($c['client_nom']) ?>
                                </strong><br>
                                <small class="text-muted">
                                    <?= e($c['client_email']) ?>
                                </small>
                            </td>
                            <td><?= e($c['menu_titre']) ?></td>
                            <td>
                                <?= date_fr($c['date_prestation']) ?><br>
                                <small class="text-muted">
                                    <?= e(substr($c['heure_livraison'], 0, 5)) ?>
                                </small>
                            </td>
                            <td><strong><?= prix_format($total) ?></strong></td>
                            <td>
                                <span class="badge <?= $badge['classe'] ?>">
                                    <?= e($c['statut']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="/employe/commande?numero=<?= urlencode($c['numero_commande']) ?>"
                                    class="btn btn-outline btn-sm">
                                    Gérer →
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