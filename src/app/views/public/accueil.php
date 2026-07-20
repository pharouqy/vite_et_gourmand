<?php
$titre_page = $titre_page ?? 'Accueil';
require dirname(__DIR__) . '/partials/header.php';
?>

<main class="container py-xl text-center">
    <h1>Bienvenue chez Vite & Gourmand</h1>
    <p class="text-muted mt-3">Traiteur événementiel haut de gamme.</p>
    <a href="/menus" class="btn btn-primary mt-3">Découvrir nos menus</a>
</main>

<?php require dirname(__DIR__) . '/partials/footer.php'; ?>