<?php
$titre_page = 'Commander — Étape 2';
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
        <div style="height:2px;width:40px;background:var(--color-border);"></div>
        <div class="d-flex align-items-center gap-2">
            <span style="width:32px;height:32px;border-radius:50%;
                         background:var(--color-primary);color:#fff;
                         display:flex;align-items:center;
                         justify-content:center;font-weight:700;">2</span>
            <span style="font-weight:600;color:var(--color-primary);">
                Menu
            </span>
        </div>
        <div style="height:2px;width:40px;background:var(--color-border);"></div>
        <div class="d-flex align-items-center gap-2">
            <span style="width:32px;height:32px;border-radius:50%;
                         background:var(--color-border);color:var(--color-text-muted);
                         display:flex;align-items:center;
                         justify-content:center;font-weight:700;">3</span>
            <span class="text-muted">Récapitulatif</span>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h1 class="h4 mb-0" style="color:#fff;">
                        Étape 2 — Choisir votre menu
                    </h1>
                </div>
                <div class="card-body">

                    <form action="/commande/menu"
                          method="POST">
                        <input type="hidden"
                               name="csrf_token"
                               value="<?= e(csrf_generer()) ?>">

                        <!-- Sélection du menu -->
                        <div class="form-group mb-4">
                            <label for="menu_id">Menu *</label>
                            <select id="menu_id"
                                    name="menu_id"
                                    required
                                    onchange="mettreAJourInfoMenu(this)">
                                <option value="">
                                    Sélectionner un menu
                                </option>
                                <?php foreach ($menus as $m): ?>
                                    <option
                                        value="<?= $m['menu_id'] ?>"
                                        data-min="<?= $m['nombre_personne_minimum'] ?>"
                                        data-prix="<?= $m['prix_par_personne'] ?>"
                                        <?= (int)$m['menu_id'] === $menu_id ? 'selected' : '' ?>>
                                        <?= e($m['titre']) ?>
                                        — <?= prix_format((float)$m['prix_par_personne']) ?>/pers.
                                        (min. <?= $m['nombre_personne_minimum'] ?> pers.)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Nombre de personnes -->
                        <div class="form-group mb-3">
                            <label for="nombre_personne">
                                Nombre de personnes *
                            </label>
                            <input type="number"
                                   id="nombre_personne"
                                   name="nombre_personne"
                                   min="1"
                                   value="<?= (int)($_SESSION['commande']['nombre_personne'] ?? 0) ?: '' ?>"
                                   required>
                            <small id="info-minimum" class="text-muted"></small>
                        </div>

                        <!-- Récapitulatif prix temps réel -->
                        <div id="recap-prix"
                             class="alerte alerte-info mb-4"
                             style="display:none;">
                        </div>

                        <button type="submit"
                                class="btn btn-primary w-100">
                            Continuer → Récapitulatif
                        </button>
                        <a href="/commande/prestation"
                           class="btn btn-ghost w-100 mt-2">
                            ← Retour étape 1
                        </a>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

</main>
<?php
$scripts = ['/assets/js/commande.js'];
require dirname(__DIR__) . '/partials/footer.php';
?>