<?php
$titre_page = 'Modifier la commande';
require dirname(__DIR__) . '/partials/header.php';
?>

<div class="container py-xl">
    <div class="row justify-content-center">
        <div class="col-lg-6">

            <div class="d-flex align-items-center gap-3 mb-4">
                <a href="/compte/commande?numero=<?= e($commande['numero_commande']) ?>" class="btn btn-ghost btn-sm">
                    ← Retour
                </a>
                <h1 class="h4 mb-0" style="color:var(--color-primary);">
                    Modifier la commande
                    <code><?= e($commande['numero_commande']) ?></code>
                </h1>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2 class="h5 mb-0" style="color:#fff;">
                        Nouvelle date et heure
                    </h2>
                </div>
                <div class="card-body">

                    <!-- Valeurs actuelles -->
                    <div class="alerte alerte-info mb-4" style="font-size:var(--font-size-sm);">
                        Valeurs actuelles :
                        <strong>
                            <?= date_fr($commande['date_prestation']) ?>
                            à <?= e($commande['heure_livraison']) ?>
                        </strong>
                    </div>

                    <form action="/compte/modifier" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_generer()) ?>">
                        <input type="hidden" name="numero" value="<?= e($commande['numero_commande']) ?>">

                        <div class="form-group">
                            <label for="date_prestation">
                                Nouvelle date de prestation *
                            </label>
                            <input type="date" id="date_prestation" name="date_prestation"
                                value="<?= e($commande['date_prestation']) ?>"
                                min="<?= date('Y-m-d', strtotime('+3 days')) ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="heure_livraison">
                                Nouvelle heure de livraison *
                            </label>
                            <input type="time" id="heure_livraison" name="heure_livraison"
                                value="<?= e($commande['heure_livraison']) ?>" required>
                        </div>

                        <div class="alerte alerte-info mb-3" style="font-size:var(--font-size-sm);">
                            ⚠️ Seuls la date et l'heure sont modifiables.
                            Le menu ne peut pas être changé.
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            Enregistrer les modifications
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

</main>
<?php require dirname(__DIR__) . '/partials/footer.php'; ?>