<?php
$titre_page = "Commande — Étape 1";
require dirname(__DIR__) . '/partials/header.php';
?>

<div class="container py-xl">

    <!-- ── Indicateur d'étapes ───────────────────────────────────── -->
    <div class="d-flex align-items-center justify-content-center gap-3 mb-4">
        <div class="d-flex align-items-center gap-2">
            <span style="width:32px;height:32px;border-radius:50%;
                         background:var(--color-primary);color:#fff;
                         display:flex;align-items:center;
                         justify-content:center;font-weight:700;">
                1
            </span>
            <span style="font-weight:600;color:var(--color-primary);">
                Prestation
            </span>
        </div>
        <div style="height:2px;width:40px;
                    background:var(--color-border);"></div>
        <div class="d-flex align-items-center gap-2">
            <span style="width:32px;height:32px;border-radius:50%;
                         background:var(--color-border);color:var(--color-text-muted);
                         display:flex;align-items:center;
                         justify-content:center;font-weight:700;">
                2
            </span>
            <span style="color:var(--color-text-muted);">Menu</span>
        </div>
        <div style="height:2px;width:40px;
                    background:var(--color-border);"></div>
        <div class="d-flex align-items-center gap-2">
            <span style="width:32px;height:32px;border-radius:50%;
                         background:var(--color-border);color:var(--color-text-muted);
                         display:flex;align-items:center;
                         justify-content:center;font-weight:700;">
                3
            </span>
            <span style="color:var(--color-text-muted);">
                Récapitulatif
            </span>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">
                    <h1 class="h4 mb-0" style="color:#fff;">
                        Étape 1 — Informations de prestation
                    </h1>
                </div>
                <div class="card-body">

                    <form action="/commande/prestation"
                          method="POST"
                          novalidate>

                        <input type="hidden"
                               name="csrf_token"
                               value="<?= e(csrf_generer()) ?>">

                        <!-- ── Identité (auto-rempli) ─────────────── -->
                        <h2 class="h6 mb-3"
                            style="color:var(--color-primary);
                                   border-bottom:1px solid var(--color-border);
                                   padding-bottom:var(--space-sm);">
                            Vos coordonnées
                        </h2>

                        <div class="row g-3 mb-3">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Prénom</label>
                                    <input type="text"
                                           value="<?= e($_SESSION['prenom'] ?? '') ?>"
                                           disabled
                                           style="background:var(--color-bg);">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Nom</label>
                                    <input type="text"
                                           value="<?= e($_SESSION['nom'] ?? '') ?>"
                                           disabled
                                           style="background:var(--color-bg);">
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email"
                                           value="<?= e($_SESSION['email'] ?? '') ?>"
                                           disabled
                                           style="background:var(--color-bg);">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Téléphone</label>
                                    <?php
                                    $user = utilisateur_par_id(
                                        (int)$_SESSION['utilisateur_id']
                                    );
                                    ?>
                                    <input type="tel"
                                           value="<?= e($user['telephone'] ?? '') ?>"
                                           disabled
                                           style="background:var(--color-bg);">
                                </div>
                            </div>
                        </div>

                        <!-- ── Lieu de prestation ─────────────────── -->
                        <h2 class="h6 mb-3"
                            style="color:var(--color-primary);
                                   border-bottom:1px solid var(--color-border);
                                   padding-bottom:var(--space-sm);">
                            Lieu et date de la prestation
                        </h2>

                        <div class="form-group">
                            <label for="adresse_livraison">
                                Adresse de livraison *
                            </label>
                            <input type="text"
                                   id="adresse_livraison"
                                   name="adresse_livraison"
                                   value="<?= e($_SESSION['commande']['adresse_livraison'] ?? '') ?>"
                                   required
                                   placeholder="12 Rue de la Paix">
                        </div>

                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="ville_livraison">
                                        Ville *
                                    </label>
                                    <input type="text"
                                           id="ville_livraison"
                                           name="ville_livraison"
                                           value="<?= e($_SESSION['commande']['ville_livraison'] ?? '') ?>"
                                           required
                                           placeholder="Bordeaux">
                                    <small class="text-muted">
                                        Livraison gratuite dans Bordeaux.
                                    </small>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="date_prestation">
                                        Date de la prestation *
                                    </label>
                                    <input type="date"
                                           id="date_prestation"
                                           name="date_prestation"
                                           value="<?= e($_SESSION['commande']['date_prestation'] ?? '') ?>"
                                           min="<?= date('Y-m-d', strtotime('+3 days')) ?>"
                                           required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="heure_livraison">
                                Heure de livraison souhaitée *
                            </label>
                            <input type="time"
                                   id="heure_livraison"
                                   name="heure_livraison"
                                   value="<?= e($_SESSION['commande']['heure_livraison'] ?? '') ?>"
                                   required>
                        </div>

                        <button type="submit"
                                class="btn btn-primary w-100 mt-2">
                            Continuer → Choisir le menu
                        </button>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</main>
<?php require dirname(__DIR__) . '/partials/footer.php'; ?>