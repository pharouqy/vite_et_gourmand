<?php
$titre_page = 'Horaires d\'ouverture';
require dirname(__DIR__) . '/partials/header.php';
?>

<div class="container py-xl">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="d-flex align-items-center gap-3 mb-4">
                <a href="/employe/menus" class="btn btn-ghost btn-sm">← Retour</a>
                <h1 class="h4 mb-0" style="color:var(--color-primary);">
                    Horaires d'ouverture
                </h1>
            </div>

            <div class="card">
                <div class="card-body">

                    <p class="text-muted" style="font-size:var(--font-size-sm);">
                        Ces horaires s'affichent dans le pied de page de tout le site.
                        Cochez « Fermé » pour un jour sans ouverture : les heures
                        saisies sont alors ignorées.
                    </p>

                    <form action="/employe/horaires" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_generer()) ?>">

                        <?php foreach ($horaires as $h): ?>
                            <?php
                            $id = (int) $h['horaire_id'];
                            $ferme = $h['heure_ouverture'] === null;
                            ?>
                            <div class="row g-3 align-items-center mb-3 pb-3"
                                style="border-bottom:1px solid var(--color-border);">

                                <div class="col-sm-3">
                                    <strong><?= e($h['jour']) ?></strong>
                                </div>

                                <div class="col-sm-3">
                                    <label style="display:flex;align-items:center;gap:6px;
                                                  font-weight:400;cursor:pointer;">
                                        <input type="checkbox" name="ferme[<?= $id ?>]" <?= $ferme ? 'checked' : '' ?>
                                            style="accent-color:var(--color-primary);">
                                        Fermé
                                    </label>
                                </div>

                                <div class="col-6 col-sm-3">
                                    <label class="visually-hidden" for="ouv_<?= $id ?>">
                                        Ouverture <?= e($h['jour']) ?>
                                    </label>
                                    <input type="time" id="ouv_<?= $id ?>" name="ouverture[<?= $id ?>]"
                                        value="<?= e($h['heure_ouverture'] ?? '') ?>" style="width:100%;">
                                </div>

                                <div class="col-6 col-sm-3">
                                    <label class="visually-hidden" for="ferm_<?= $id ?>">
                                        Fermeture <?= e($h['jour']) ?>
                                    </label>
                                    <input type="time" id="ferm_<?= $id ?>" name="fermeture[<?= $id ?>]"
                                        value="<?= e($h['heure_fermeture'] ?? '') ?>" style="width:100%;">
                                </div>

                            </div>
                        <?php endforeach; ?>

                        <button type="submit" class="btn btn-primary w-100 mt-2">
                            💾 Enregistrer les horaires
                        </button>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

</main>
<?php require dirname(__DIR__) . '/partials/footer.php'; ?>