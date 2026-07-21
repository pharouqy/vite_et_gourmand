<?php require dirname(__DIR__) . '/partials/header.php'; ?>
<main class="container py-xl">
    <h1>Mes commandes</h1>
    <p class="text-muted">
        Bonjour <?= e($_SESSION['prenom'] ?? '') ?> —
        votre espace sera disponible au Sprint 3.
    </p>
    <a href="/deconnexion" class="btn btn-outline mt-3">Se déconnecter</a>
</main>
<?php require dirname(__DIR__) . '/partials/footer.php'; ?>