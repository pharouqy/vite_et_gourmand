<?php
$titre_page = "Connexion";
require dirname(__DIR__) . '/partials/header.php';
?>

<main class="container py-xl">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">

            <div class="card">
                <div class="card-header">
                    <h1 class="h3 mb-0" style="color:#fff; font-size: var(--font-size-2xl);">
                        Connexion
                    </h1>
                </div>

                <div class="card-body">
                    <form action="/connexion" method="POST" novalidate>

                        <input type="hidden"
                            name="csrf_token"
                            value="<?= e(csrf_generer()) ?>">

                        <!-- Conserver l'URL de redirection après connexion -->
                        <?php if (!empty($_GET['redirect'])): ?>
                            <input type="hidden"
                                name="redirect"
                                value="<?= e($_GET['redirect']) ?>">
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="email">Adresse email</label>
                            <input type="email"
                                id="email"
                                name="email"
                                value="<?= e($_POST['email'] ?? '') ?>"
                                required
                                autocomplete="email"
                                autofocus>
                        </div>

                        <div class="form-group">
                            <label for="password">Mot de passe</label>
                            <input type="password"
                                id="password"
                                name="password"
                                required
                                autocomplete="current-password">
                        </div>

                        <div class="d-flex justify-content-end mb-3">
                            <a href="/mot-de-passe-oublie"
                                style="font-size: var(--font-size-sm);">
                                Mot de passe oublié ?
                            </a>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            Se connecter
                        </button>

                        <p class="text-center mt-3"
                            style="font-size: var(--font-size-sm);">
                            Pas encore de compte ?
                            <a href="/inscription">S'inscrire</a>
                        </p>

                    </form>
                </div>
            </div>

        </div>
    </div>
</main>

<?php require dirname(__DIR__) . '/partials/footer.php'; ?>