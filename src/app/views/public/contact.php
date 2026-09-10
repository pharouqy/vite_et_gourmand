<?php
$titre_page = "Contact";
require dirname(__DIR__) . '/partials/header.php';
?>

<div class="container py-xl">
    <div class="row g-5">

        <!-- ── Formulaire ──────────────────────────────────────────── -->
        <div class="col-lg-7">
            <h1 style="color: var(--color-primary);">Nous contacter</h1>
            <p class="text-muted mb-4">
                Une question sur nos menus, une demande de devis ?
                Nous vous répondons sous 24h.
            </p>

            <form action="/contact" method="POST" novalidate>
                <input type="hidden"
                       name="csrf_token"
                       value="<?= e(csrf_generer()) ?>">

                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="nom">Nom *</label>
                            <input type="text"
                                   id="nom"
                                   name="nom"
                                   value="<?= e($_POST['nom'] ?? (est_connecte() ? $_SESSION['nom'] : '')) ?>"
                                   required>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="prenom">Prénom *</label>
                            <input type="text"
                                   id="prenom"
                                   name="prenom"
                                   value="<?= e($_POST['prenom'] ?? (est_connecte() ? $_SESSION['prenom'] : '')) ?>"
                                   required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email"
                           id="email"
                           name="email"
                           value="<?= e($_POST['email'] ?? (est_connecte() ? $_SESSION['email'] : '')) ?>"
                           required>
                </div>

                <div class="form-group">
                    <label for="sujet">Sujet *</label>
                    <select id="sujet" name="sujet" required>
                        <option value="">Choisir un sujet</option>
                        <option value="Demande de devis"
                            <?= (($_POST['sujet'] ?? '') === 'Demande de devis') ? 'selected' : '' ?>>
                            Demande de devis
                        </option>
                        <option value="Question sur un menu"
                            <?= (($_POST['sujet'] ?? '') === 'Question sur un menu') ? 'selected' : '' ?>>
                            Question sur un menu
                        </option>
                        <option value="Suivi de commande"
                            <?= (($_POST['sujet'] ?? '') === 'Suivi de commande') ? 'selected' : '' ?>>
                            Suivi de commande
                        </option>
                        <option value="Autre"
                            <?= (($_POST['sujet'] ?? '') === 'Autre') ? 'selected' : '' ?>>
                            Autre
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="message">Message *</label>
                    <textarea id="message"
                              name="message"
                              rows="5"
                              required
                              style="resize: vertical;"><?= e($_POST['message'] ?? '') ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary">
                    Envoyer le message
                </button>
            </form>
        </div>

        <!-- ── Informations pratiques ──────────────────────────────── -->
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header">
                    <h2 class="h5 mb-0" style="color:#fff;">
                        Informations pratiques
                    </h2>
                </div>
                <div class="card-body">

                    <div class="mb-4">
                        <h3 class="h6" style="color: var(--color-primary);">
                            📍 Adresse
                        </h3>
                        <p class="text-muted mb-0">
                            Bordeaux, France
                        </p>
                    </div>

                    <div class="mb-4">
                        <h3 class="h6" style="color: var(--color-primary);">
                            📧 Email
                        </h3>
                        <p class="text-muted mb-0">
                            <a href="mailto:contact@viteetgourmand.fr">
                                contact@viteetgourmand.fr
                            </a>
                        </p>
                    </div>

                    <div class="mb-4">
                        <h3 class="h6" style="color: var(--color-primary);">
                            🕐 Horaires de réponse
                        </h3>
                        <p class="text-muted mb-0">
                            Lundi – Vendredi : 9h00 – 18h00<br>
                            Réponse sous 24h ouvrées.
                        </p>
                    </div>

                    <div>
                        <h3 class="h6" style="color: var(--color-primary);">
                            🚚 Zone de livraison
                        </h3>
                        <p class="text-muted mb-0">
                            Bordeaux et agglomération.<br>
                            Livraison hors zone : forfait + 0,59 DA/km.
                        </p>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

</main>
<?php require dirname(__DIR__) . '/partials/footer.php'; ?>