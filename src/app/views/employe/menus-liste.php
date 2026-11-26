<?php
$titre_page = 'Gestion des menus';
require dirname(__DIR__) . '/partials/header.php';
?>

<div class="container py-xl">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h1 style="color:var(--color-primary);">Gestion des menus</h1>
        <a href="/employe/menus/creer" class="btn btn-primary">
            + Nouveau menu
        </a>
    </div>

    <?php if (empty($menus)): ?>

        <div class="text-center py-xl">
            <p style="font-size:2.5rem;">🍽</p>
            <p class="text-muted">Aucun menu enregistré pour le moment.</p>
            <a href="/employe/menus/creer" class="btn btn-primary mt-2">
                Créer le premier menu
            </a>
        </div>

    <?php else: ?>

        <p class="text-muted mb-3" style="font-size:var(--font-size-sm);">
            <?= count($menus) ?>
            menu<?= count($menus) > 1 ? 's' : '' ?>
        </p>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead style="background:var(--color-primary);color:#fff;">
                    <tr>
                        <th>Titre</th>
                        <th>Prix / pers.</th>
                        <th>Min. pers.</th>
                        <th>Disponibilité</th>
                        <th>Régime</th>
                        <th>Thème</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($menus as $m): ?>
                        <tr>
                            <td><strong><?= e($m['titre']) ?></strong></td>
                            <td><?= prix_format((float) $m['prix_par_personne']) ?></td>
                            <td><?= (int) $m['nombre_personne_minimum'] ?></td>
                            <td>
                                <?php if ((int) $m['quantite_restante'] > 0): ?>
                                    <?= (int) $m['quantite_restante'] ?>
                                <?php else: ?>
                                    <span class="badge badge-annulee">Complet</span>
                                <?php endif; ?>
                            </td>
                            <td><?= e($m['regime'] ?? '—') ?></td>
                            <td><?= e($m['theme'] ?? '—') ?></td>
                            <td>
                                <div class="d-flex gap-2 flex-wrap">
                                    <a href="/menus/detail?id=<?= (int) $m['menu_id'] ?>" target="_blank" rel="noopener"
                                        class="btn btn-ghost btn-sm">
                                        👁 Voir
                                    </a>
                                    <a href="/employe/menus/modifier?id=<?= (int) $m['menu_id'] ?>"
                                        class="btn btn-outline btn-sm">
                                        ✏️ Modifier
                                    </a>
                                    <form action="/employe/menus/supprimer" method="POST"
                                        onsubmit="return confirm('Supprimer ce menu ? Cette action est définitive.')">
                                        <input type="hidden" name="csrf_token" value="<?= e(csrf_generer()) ?>">
                                        <input type="hidden" name="menu_id" value="<?= (int) $m['menu_id'] ?>">
                                        <button type="submit" class="btn btn-sm"
                                            style="background:var(--color-danger);color:#fff;">
                                            🗑 Supprimer
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php endif; ?>

    <!-- ── Autres pages de gestion ───────────────────────────────── -->
    <div class="d-flex gap-3 mt-4 flex-wrap">
        <a href="/employe/plats" class="btn btn-outline">Gérer les plats</a>
        <a href="/employe/horaires" class="btn btn-outline">Gérer les horaires</a>
        <a href="/employe/commandes" class="btn btn-outline">Voir les commandes</a>
    </div>

</div>

</main>
<?php require dirname(__DIR__) . '/partials/footer.php'; ?>