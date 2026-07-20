<?php
$titre_page = "Inscription";
require dirname(__DIR__) . '/partials/header.php';
?>

<main class="container py-xl">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">

            <div class="card">
                <div class="card-header">
                    <h1 class="h3 mb-0" style="color:#fff; font-size: var(--font-size-2xl);">
                        Créer un compte
                    </h1>
                </div>

                <div class="card-body">
                    <form id="form-inscription"
                        action="/inscription"
                        method="POST"
                        novalidate>

                        <!-- Token CSRF (protection contre les attaques cross-site) -->
                        <input type="hidden"
                            name="csrf_token"
                            value="<?= e(csrf_generer()) ?>">

                        <!-- ── Identité ──────────────────────────────── -->
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="nom">Nom *</label>
                                    <input type="text"
                                        id="nom"
                                        name="nom"
                                        value="<?= e($_POST['nom'] ?? '') ?>"
                                        required
                                        autocomplete="family-name">
                                    <span class="form-error" id="err-nom"></span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="prenom">Prénom *</label>
                                    <input type="text"
                                        id="prenom"
                                        name="prenom"
                                        value="<?= e($_POST['prenom'] ?? '') ?>"
                                        required
                                        autocomplete="given-name">
                                    <span class="form-error" id="err-prenom"></span>
                                </div>
                            </div>
                        </div>

                        <!-- ── Contact ───────────────────────────────── -->
                        <div class="form-group">
                            <label for="email">Adresse email *</label>
                            <input type="email"
                                id="email"
                                name="email"
                                value="<?= e($_POST['email'] ?? '') ?>"
                                required
                                autocomplete="email">
                            <span class="form-error" id="err-email"></span>
                        </div>

                        <div class="form-group">
                            <label for="telephone">Téléphone / GSM *</label>
                            <input type="tel"
                                id="telephone"
                                name="telephone"
                                value="<?= e($_POST['telephone'] ?? '') ?>"
                                required
                                autocomplete="tel">
                            <span class="form-error" id="err-telephone"></span>
                        </div>

                        <!-- ── Adresse ────────────────────────────────── -->
                        <div class="form-group">
                            <label for="adresse_postale">Adresse postale *</label>
                            <input type="text"
                                id="adresse_postale"
                                name="adresse_postale"
                                value="<?= e($_POST['adresse_postale'] ?? '') ?>"
                                required
                                autocomplete="street-address">
                            <span class="form-error" id="err-adresse"></span>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="ville">Ville *</label>
                                    <input type="text"
                                        id="ville"
                                        name="ville"
                                        value="<?= e($_POST['ville'] ?? '') ?>"
                                        required
                                        autocomplete="address-level2">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="pays">Pays *</label>
                                    <input type="text"
                                        id="pays"
                                        name="pays"
                                        value="<?= e($_POST['pays'] ?? 'France') ?>"
                                        required
                                        autocomplete="country-name">
                                </div>
                            </div>
                        </div>

                        <!-- ── Mot de passe ───────────────────────────── -->
                        <div class="form-group">
                            <label for="password">Mot de passe *</label>
                            <input type="password"
                                id="password"
                                name="password"
                                required
                                autocomplete="new-password">
                            <!-- Indicateur de force -->
                            <div id="force-mdp" class="mt-1" style="font-size: var(--font-size-xs);"></div>
                            <span class="form-error" id="err-password"></span>
                            <small class="text-muted">
                                10 caractères minimum, 1 majuscule, 1 minuscule,
                                1 chiffre, 1 caractère spécial.
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="password_confirm">Confirmer le mot de passe *</label>
                            <input type="password"
                                id="password_confirm"
                                name="password_confirm"
                                required
                                autocomplete="new-password">
                            <span class="form-error" id="err-confirm"></span>
                        </div>

                        <!-- ── Consentement RGPD ──────────────────────── -->
                        <div class="form-group">
                            <div class="d-flex align-items-start gap-2">
                                <input type="checkbox"
                                    id="rgpd"
                                    name="rgpd"
                                    style="margin-top: 4px; accent-color: var(--color-primary);"
                                    required>
                                <label for="rgpd" style="font-weight: 400; font-size: var(--font-size-sm);">
                                    J'accepte les
                                    <a href="/mentions-legales" target="_blank">mentions légales</a>
                                    et les
                                    <a href="/cgv" target="_blank">conditions générales de vente</a>
                                    de Vite & Gourmand. *
                                </label>
                            </div>
                            <span class="form-error" id="err-rgpd"></span>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mt-2">
                            Créer mon compte
                        </button>

                        <p class="text-center mt-3" style="font-size: var(--font-size-sm);">
                            Déjà un compte ?
                            <a href="/connexion">Se connecter</a>
                        </p>

                    </form>
                </div>
            </div>

        </div>
    </div>
</main>

<?php
$scripts = ['/assets/js/validation-form.js'];
require dirname(__DIR__) . '/partials/footer.php';
?>