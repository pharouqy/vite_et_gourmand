<?php
$titre_page = 'Nos menus';
require dirname(__DIR__) . '/partials/header.php';
?>

<div class="container py-xl">

    <h1 class="mb-2">Nos menus</h1>
    <p class="text-muted mb-4">
        Découvrez notre sélection de menus pour tous vos événements.
    </p>

    <!-- ── Filtres ───────────────────────────────────────────────────── -->
    <div class="filtres-container mb-4">
        <h2 class="h6 mb-3" style="color: var(--color-primary);">
            🔍 Affiner la recherche
        </h2>

        <div class="row g-3" id="filtres-form">

            <!-- Prix maximum -->
            <div class="col-sm-6 col-lg-4">
                <div class="filtre-group">
                    <label for="prix_max">Prix max / personne (DA)</label>
                    <input type="number"
                           id="prix_max"
                           name="prix_max"
                           min="0"
                           step="100"
                           placeholder="Ex : 3000">
                </div>
            </div>

            <!-- Fourchette prix minimum -->
            <div class="col-sm-6 col-lg-4">
                <div class="filtre-group">
                    <label for="prix_min">Prix min / personne (DA)</label>
                    <input type="number"
                           id="prix_min"
                           name="prix_min"
                           min="0"
                           step="100"
                           placeholder="Ex : 1000">
                </div>
            </div>

            <!-- Nombre de personnes -->
            <div class="col-sm-6 col-lg-4">
                <div class="filtre-group">
                    <label for="nb_personnes">Nombre de personnes</label>
                    <input type="number"
                           id="nb_personnes"
                           name="nb_personnes"
                           min="1"
                           placeholder="Ex : 20">
                </div>
            </div>

            <!-- Thème -->
            <div class="col-sm-6 col-lg-4">
                <div class="filtre-group">
                    <label for="theme_id">Thème</label>
                    <select id="theme_id" name="theme_id">
                        <option value="">Tous les thèmes</option>
                        <?php foreach ($themes as $theme): ?>
                            <option value="<?= e((string)$theme['theme_id']) ?>">
                                <?= e($theme['libelle']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Régime -->
            <div class="col-sm-6 col-lg-4">
                <div class="filtre-group">
                    <label for="regime_id">Régime alimentaire</label>
                    <select id="regime_id" name="regime_id">
                        <option value="">Tous les régimes</option>
                        <?php foreach ($regimes as $regime): ?>
                            <option value="<?= e((string)$regime['regime_id']) ?>">
                                <?= e($regime['libelle']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Bouton réinitialiser -->
            <div class="col-sm-6 col-lg-4 d-flex align-items-end">
                <button type="button"
                        id="btn-reset-filtres"
                        class="btn btn-outline w-100">
                    Réinitialiser les filtres
                </button>
            </div>

        </div>
    </div>

    <!-- ── Résultats ──────────────────────────────────────────────────── -->
    <div id="menus-count"
         class="text-muted mb-3"
         style="font-size: var(--font-size-sm);">
        Chargement...
    </div>

    <div id="menus-grille" class="grid-menus">
        <!-- Chargé dynamiquement par filtres-menus.js -->
        <div class="text-center py-xl" style="grid-column: 1/-1;">
            <div class="spinner-border"
                 style="color: var(--color-primary);"
                 role="status">
                <span class="visually-hidden">Chargement...</span>
            </div>
        </div>
    </div>

</div>

</main>
<?php
$scripts = ['/assets/js/filtres-menus.js'];
require dirname(__DIR__) . '/partials/footer.php';
?>