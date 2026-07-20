<?php

/**
 * Affiche et vide les messages flash stockés en session.
 * Usage dans un contrôleur :
 *   $_SESSION['flash'] = ['type' => 'success', 'message' => 'Inscription réussie !'];
 */
if (!empty($_SESSION['flash'])): ?>
    <div class="container mt-3">
        <?php
        $flash = $_SESSION['flash'];
        $classe = match ($flash['type']) {
            'success' => 'alerte-success',
            'danger'  => 'alerte-danger',
            default   => 'alerte-info',
        };
        ?>
        <div class="alerte <?= $classe ?>">
            <?= e($flash['message']) ?>
        </div>
    </div>
<?php
    // Vider le flash après affichage — il ne doit apparaître qu'une fois
    unset($_SESSION['flash']);
endif;
?>