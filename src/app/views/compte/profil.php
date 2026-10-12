<?php
$titre_page = 'Mon profil';
require dirname(__DIR__) . '/partials/header.php';
?>

<div class="container py-xl">
    <div class="row justify-content-center">
        <div class="col-lg-6">

            <h1 class="mb-4" style="color:var(--color-primary);">
                Mon profil
            </h1>

            <div class="card">
                <div class="card-header">
                    <h2 class="h5 mb-0" style="color:#fff;">
                        Informations personnelles
                    </h2>
                </div>
                <div class="card-body">
                    <form action="/compte/profil" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_generer()) ?>">

                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="nom">Nom *</label>
                                    <input type="text" id="nom" name="nom" value="<?= e($user['nom']) ?>" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="prenom">Prénom *</label>
                                    <input type="text" id="prenom" name="prenom" value="<?= e($user['prenom']) ?>"
                                        required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="telephone">Téléphone *</label>
                            <input type="tel" id="telephone" name="telephone" value="<?= e($user['telephone']) ?>"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="adresse_postale">
                                Adresse postale *
                            </label>
                            <input type="text" id="adresse_postale" name="adresse_postale"
                                value="<?= e($user['adresse_postale']) ?>" required>
                        </div>

                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="ville">Ville *</label>
                                    <input type="text" id="ville" name="ville" value="<?= e($user['ville']) ?>"
                                        required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="pays">Pays *</label>
                                    <input type="text" id="pays" name="pays" value="<?= e($user['pays']) ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" value="<?= e($user['email']) ?>" disabled
                                style="background:var(--color-bg);">
                            <small class="text-muted">
                                L'email ne peut pas être modifié.
                            </small>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mt-2">
                            Sauvegarder les modifications
                        </button>
                    </form>
                </div>
            </div>

            <a href="/compte/commandes" class="btn btn-ghost w-100 mt-3">
                ← Mes commandes
            </a>
        </div>
    </div>
</div>

</main>
<?php require dirname(__DIR__) . '/partials/footer.php'; ?>