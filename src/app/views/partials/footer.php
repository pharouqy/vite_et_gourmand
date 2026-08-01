<?php

/**
 * Partial footer — horaires chargés dynamiquement
 */

// Chargement des horaires depuis la base
$horaires = horaires_tous();
?>

<footer class="footer mt-auto">
    <div class="container">
        <div class="row">

            <!-- Colonne 1 : présentation -->
            <div class="col-md-4 mb-4">
                <h5 style="color: var(--color-accent);
                           font-family: var(--font-heading);">
                    🍽 Vite & Gourmand
                </h5>
                <p class="mt-2"
                    style="opacity:.8; font-size: var(--font-size-sm);">
                    Traiteur événementiel haut de gamme.<br>
                    Anniversaires, mariages, événements d'entreprise.
                </p>
            </div>

            <!-- Colonne 2 : horaires dynamiques -->
            <div class="col-md-4 mb-4">
                <h6 style="color: var(--color-accent);">Nos horaires</h6>
                <ul class="list-unstyled mt-2"
                    style="font-size: var(--font-size-sm); opacity:.8;">
                    <?php foreach ($horaires as $h): ?>
                        <li>
                            <strong><?= e($h['jour']) ?></strong> :
                            <?php if ($h['heure_ouverture'] === null): ?>
                                Fermé
                            <?php else: ?>
                                <?= e($h['heure_ouverture']) ?>
                                – <?= e($h['heure_fermeture']) ?>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Colonne 3 : liens légaux -->
            <div class="col-md-4 mb-4">
                <h6 style="color: var(--color-accent);">Informations</h6>
                <ul class="list-unstyled mt-2"
                    style="font-size: var(--font-size-sm);">
                    <li><a href="/mentions-legales">Mentions légales</a></li>
                    <li><a href="/cgv">Conditions générales de vente</a></li>
                    <li><a href="/contact">Nous contacter</a></li>
                </ul>
            </div>

        </div>

        <hr style="border-color:rgba(255,255,255,0.15);
                   margin: var(--space-md) 0;">
        <p class="text-center"
            style="font-size: var(--font-size-xs); opacity:.6;">
            © <?= date('Y') ?> Vite & Gourmand — Tous droits réservés
        </p>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>

<?php if (isset($scripts)): ?>
    <?php foreach ($scripts as $script): ?>
        <script src="<?= e($script) ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>

</body>

</html>