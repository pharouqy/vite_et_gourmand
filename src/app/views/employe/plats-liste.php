<?php
$titre_page = 'Gestion des plats';
$edition = $plat_edition !== null;
require dirname(__DIR__) . '/partials/header.php';
?>

<div class="container py-xl">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h1 style="color:var(--color-primary);">Gestion des plats</h1>
        <a href="/employe/menus" class="btn btn-ghost btn-sm">← Retour aux menus</a>
    </div>

    <!-- ══ Formulaire : création ou modification ═════════════════════ -->
    <div class="card mb-4">
        <div class="card-header">
            <h2 class="h5 mb-0" style="color:#fff;">
                <?= $edition ? 'Modifier le plat' : 'Nouveau plat' ?>
            </h2>
        </div>
        <div class="card-body">

            <form action="/employe/plats/enregistrer" method="POST" enctype="multipart/form-data">

                <input type="hidden" name="csrf_token" value="<?= e(csrf_generer()) ?>">
                <input type="hidden" name="plat_id" value="<?= $edition ? (int) $plat_edition['plat_id'] : 0 ?>">

                <div class="form-group">
                    <label for="titre_plat">Nom du plat *</label>
                    <input type="text" id="titre_plat" name="titre_plat"
                        value="<?= e($edition ? $plat_edition['titre_plat'] : '') ?>" required maxlength="150">
                </div>

                <div class="form-group">
                    <label>Allergènes présents dans ce plat</label>
                    <div class="row g-2 mt-1">
                        <?php foreach ($allergenes as $a): ?>
                            <div class="col-sm-6 col-lg-4">
                                <label style="display:flex;align-items:center;gap:8px;
                                              font-weight:400;cursor:pointer;">
                                    <input type="checkbox" name="allergenes[]" value="<?= (int) $a['allergene_id'] ?>"
                                        <?= in_array((int) $a['allergene_id'], $allergenes_plat, true)
                                            ? 'checked' : '' ?>
                                        style="accent-color:var(--color-primary);">
                                    <?= e($a['libelle']) ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="photo">Photo (JPEG, PNG ou WebP, 2 Mo maximum)</label>

                    <?php if ($edition && !empty($plat_edition['photo'])): ?>
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <img src="<?= e($plat_edition['photo']) ?>" alt="Photo de <?= e($plat_edition['titre_plat']) ?>"
                                style="height:80px;border-radius:8px;">
                            <label style="display:flex;align-items:center;gap:8px;
                                          font-weight:400;cursor:pointer;">
                                <input type="checkbox" name="supprimer_photo" value="1"
                                    style="accent-color:var(--color-primary);">
                                Supprimer la photo actuelle
                            </label>
                        </div>
                    <?php endif; ?>

                    <input type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/webp">
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <?= $edition ? '💾 Enregistrer' : '➕ Créer le plat' ?>
                    </button>
                    <?php if ($edition): ?>
                        <a href="/employe/plats" class="btn btn-outline">Annuler</a>
                    <?php endif; ?>
                </div>

            </form>
        </div>
    </div>

    <!-- ══ Liste des plats ═══════════════════════════════════════════ -->
    <?php if (empty($plats)): ?>

        <p class="text-muted text-center py-xl">Aucun plat enregistré pour le moment.</p>

    <?php else: ?>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead style="background:var(--color-primary);color:#fff;">
                    <tr>
                        <th>Photo</th>
                        <th>Plat</th>
                        <th>Allergènes</th>
                        <th>Menus</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($plats as $p): ?>
                        <tr>
                            <td>
                                <?php if (!empty($p['photo'])): ?>
                                    <img src="<?= e($p['photo']) ?>" alt="Photo de <?= e($p['titre_plat']) ?>" style="height:48px;width:64px;object-fit:cover;
                                                border-radius:6px;">
                                <?php else: ?>
                                    <span class="text-muted" style="font-size:var(--font-size-xs);">—</span>
                                <?php endif; ?>
                            </td>
                            <td><strong><?= e($p['titre_plat']) ?></strong></td>
                            <td style="font-size:var(--font-size-sm);">
                                <?= e($p['allergenes'] ?? 'Aucun') ?>
                            </td>
                            <td>
                                <?= (int) $p['nb_menus'] ?>
                                menu<?= (int) $p['nb_menus'] > 1 ? 's' : '' ?>
                            </td>
                            <td>
                                <div class="d-flex gap-2 flex-wrap">
                                    <a href="/employe/plats?id=<?= (int) $p['plat_id'] ?>" class="btn btn-outline btn-sm">✏️
                                        Modifier</a>

                                    <form action="/employe/plats/supprimer" method="POST"
                                        onsubmit="return confirm('Supprimer ce plat ? Cette action est définitive.')">
                                        <input type="hidden" name="csrf_token" value="<?= e(csrf_generer()) ?>">
                                        <input type="hidden" name="plat_id" value="<?= (int) $p['plat_id'] ?>">
                                        <button type="submit" class="btn btn-sm"
                                            style="background:var(--color-danger);color:#fff;" <?= (int) $p['nb_menus'] > 0
                                                ? 'disabled title="Ce plat est utilisé dans un menu"' : '' ?>>
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

</div>

</main>
<?php require dirname(__DIR__) . '/partials/footer.php'; ?>