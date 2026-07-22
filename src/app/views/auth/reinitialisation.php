<?php
$titre_page = "Réinitialisation du mot de passe";
require dirname(__DIR__) . '/partials/header.php';
?>

<main class="container py-xl">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">

            <div class="card">
                <div class="card-header">
                    <h1 class="h3 mb-0" style="color:#fff; font-size: var(--font-size-2xl);">
                        Nouveau mot de passe
                    </h1>
                </div>

                <div class="card-body">
                    <form action="/reinitialisation" method="POST">
                        <input type="hidden"
                            name="csrf_token"
                            value="<?= e(csrf_generer()) ?>">

                        <!-- Token transmis via GET, renvoyé en POST caché -->
                        <input type="hidden"
                            name="token"
                            value="<?= e($_GET['token'] ?? '') ?>">

                        <div class="form-group">
                            <label for="password">Nouveau mot de passe</label>
                            <input type="password"
                                id="password"
                                name="password"
                                required
                                autocomplete="new-password">
                            <small class="text-muted">
                                10 caractères min, 1 majuscule, 1 minuscule,
                                1 chiffre, 1 caractère spécial.
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="password_confirm">Confirmer</label>
                            <input type="password"
                                id="password_confirm"
                                name="password_confirm"
                                required
                                autocomplete="new-password">
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            Réinitialiser le mot de passe
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</main>

<?php require dirname(__DIR__) . '/partials/footer.php'; ?>