<?php
$titre_page = "Mot de passe oublié";
require dirname(__DIR__) . '/partials/header.php';
?>

<main class="container py-xl">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">

            <div class="card">
                <div class="card-header">
                    <h1 class="h3 mb-0" style="color:#fff; font-size: var(--font-size-2xl);">
                        Mot de passe oublié
                    </h1>
                </div>

                <div class="card-body">
                    <p class="text-muted mb-4" style="font-size: var(--font-size-sm);">
                        Saisissez votre adresse email. Si un compte existe,
                        vous recevrez un lien de réinitialisation valable
                        <strong>1 heure</strong>.
                    </p>

                    <form action="/mot-de-passe-oublie" method="POST">
                        <input type="hidden"
                            name="csrf_token"
                            value="<?= e(csrf_generer()) ?>">

                        <div class="form-group">
                            <label for="email">Adresse email</label>
                            <input type="email"
                                id="email"
                                name="email"
                                required
                                autocomplete="email"
                                autofocus>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            Envoyer le lien
                        </button>

                        <p class="text-center mt-3"
                            style="font-size: var(--font-size-sm);">
                            <a href="/connexion">← Retour à la connexion</a>
                        </p>
                    </form>
                </div>
            </div>

        </div>
    </div>
</main>

<?php require dirname(__DIR__) . '/partials/footer.php'; ?>