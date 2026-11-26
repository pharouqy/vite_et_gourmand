<?php
$modification = isset($menu);
$titre_page = $modification ? 'Modifier le menu' : 'Nouveau menu';
$action = $modification ? '/employe/menus/modifier' : '/employe/menus/creer';

// Valeurs affichées : saisie précédente (après une erreur) > menu existant > vide
$val = $saisie ?? ($menu ?? []);

require dirname(__DIR__) . '/partials/header.php';
?>

<div class="container py-xl">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="d-flex align-items-center gap-3 mb-4">
                <a href="/employe/menus" class="btn btn-ghost btn-sm">← Retour</a>
                <h1 class="h4 mb-0" style="color:var(--color-primary);">
                    <?= e($titre_page) ?>
                </h1>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="<?= e($action) ?>" method="POST">

                        <input type="hidden" name="csrf_token" value="<?= e(csrf_generer()) ?>">

                        <?php if ($modification): ?>
                            <input type="hidden" name="menu_id" value="<?= (int) $menu['menu_id'] ?>">
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="titre">Titre du menu *</label>
                            <input type="text" id="titre" name="titre" value="<?= e((string) ($val['titre'] ?? '')) ?>"
                                required maxlength="150">
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" rows="4"
                                style="resize:vertical;"><?= e((string) ($val['description'] ?? '')) ?></textarea>
                        </div>

                        <div class="row g-3">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="prix_par_personne">Prix / pers. (DA) *</label>
                                    <input type="number" id="prix_par_personne" name="prix_par_personne"
                                        value="<?= e((string) ($val['prix_par_personne'] ?? '')) ?>" min="0" step="50"
                                        required>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="nombre_personne_minimum">Min. personnes *</label>
                                    <input type="number" id="nombre_personne_minimum" name="nombre_personne_minimum"
                                        value="<?= e((string) ($val['nombre_personne_minimum'] ?? '')) ?>" min="1"
                                        required>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="quantite_restante">Disponibilité *</label>
                                    <input type="number" id="quantite_restante" name="quantite_restante"
                                        value="<?= e((string) ($val['quantite_restante'] ?? '0')) ?>" min="0" required>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="regime_id">Régime alimentaire</label>
                                    <select id="regime_id" name="regime_id">
                                        <option value="">Aucun</option>
                                        <?php foreach ($regimes as $r): ?>
                                            <option value="<?= (int) $r['regime_id'] ?>" <?= (int) ($val['regime_id'] ?? 0) === (int) $r['regime_id']
                                                  ? 'selected' : '' ?>>
                                                <?= e($r['libelle']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="theme_id">Thème</label>
                                    <select id="theme_id" name="theme_id">
                                        <option value="">Aucun</option>
                                        <?php foreach ($themes as $t): ?>
                                            <option value="<?= (int) $t['theme_id'] ?>" <?= (int) ($val['theme_id'] ?? 0) === (int) $t['theme_id']
                                                  ? 'selected' : '' ?>>
                                                <?= e($t['libelle']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Plats composant ce menu</label>

                            <?php if (empty($plats)): ?>
                                <p class="text-muted mb-0" style="font-size:var(--font-size-sm);">
                                    Aucun plat n'existe encore.
                                    <a href="/employe/plats">Créez d'abord des plats</a>.
                                </p>
                            <?php else: ?>
                                <div class="row g-2 mt-1">
                                    <?php foreach ($plats as $plat): ?>
                                        <div class="col-sm-6 col-lg-4">
                                            <label style="display:flex;align-items:center;gap:8px;
                                                          font-weight:400;cursor:pointer;">
                                                <input type="checkbox" name="plats[]" value="<?= (int) $plat['plat_id'] ?>"
                                                    <?= in_array((int) $plat['plat_id'], $plats_du_menu, true)
                                                        ? 'checked' : '' ?>
                                                    style="accent-color:var(--color-primary);">
                                                <?= e($plat['titre_plat']) ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mt-2">
                            <?= $modification ? '💾 Enregistrer' : '➕ Créer le menu' ?>
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

</main>
<?php require dirname(__DIR__) . '/partials/footer.php'; ?>