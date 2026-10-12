<?php
$titre_page = 'Laisser un avis';
require dirname(__DIR__) . '/partials/header.php';
?>

<div class="container py-xl">
    <div class="row justify-content-center">
        <div class="col-lg-6">

            <h1 class="mb-4" style="color:var(--color-primary);">
                Votre avis
            </h1>
            <p class="text-muted mb-4">
                Commande : <strong>
                    <?= e($numero_commande) ?>
                </strong>
            </p>

            <div class="card">
                <div class="card-body">
                    <form action="/compte/avis" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_generer()) ?>">
                        <input type="hidden" name="numero" value="<?= e($numero_commande) ?>">

                        <!-- Note étoiles -->
                        <div class="form-group">
                            <label>Note *</label>
                            <div class="d-flex gap-3 mt-1" id="etoiles-container">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <label style="cursor:pointer;
                                                  font-size:2rem;">
                                        <input type="radio" name="note" value="<?= $i ?>" style="display:none;" required>
                                        <span class="etoile" data-val="<?= $i ?>" style="color:var(--color-border);
                                                     transition:color .2s;">
                                            ★
                                        </span>
                                    </label>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <!-- Commentaire -->
                        <div class="form-group">
                            <label for="description">
                                Commentaire *
                            </label>
                            <textarea id="description" name="description" rows="4" required maxlength="500"
                                placeholder="Décrivez votre expérience..." style="resize:vertical;"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            Envoyer mon avis
                        </button>
                        <a href="/compte/commandes" class="btn btn-ghost w-100 mt-2">
                            Annuler
                        </a>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

</main>
<?php
$scripts = ['/assets/js/avis.js'];
require dirname(__DIR__) . '/partials/footer.php';
?>