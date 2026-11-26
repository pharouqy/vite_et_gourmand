<?php
$titre_page = 'Modération des avis';
require dirname(__DIR__) . '/partials/header.php';
?>

<div class="container py-xl">

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h1 style="color:var(--color-primary);">Modération des avis</h1>
        <a href="/employe/commandes" class="btn btn-ghost btn-sm">
            ← Commandes
        </a>
    </div>

    <p class="text-muted mb-4" style="font-size:var(--font-size-sm);">
        Un avis <strong>publié</strong> apparaît dans le carrousel de la page
        d'accueil. Un avis <strong>refusé</strong> n'est jamais affiché.
    </p>

    <?php if (empty($avis)): ?>

        <div class="text-center py-xl">
            <p style="font-size:2.5rem;">✅</p>
            <p class="text-muted">Aucun avis en attente de modération.</p>
        </div>

    <?php else: ?>

        <p class="text-muted mb-3" style="font-size:var(--font-size-sm);">
            <?= count($avis) ?>
            avis en attente
        </p>

        <div class="row g-4">
            <?php foreach ($avis as $a): ?>
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-body d-flex flex-column">

                            <!-- Note -->
                            <div class="etoiles mb-2" aria-label="Note : <?= (int) $a['note'] ?> sur 5">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <?= $i <= (int) $a['note'] ? '★' : '☆' ?>
                                <?php endfor; ?>
                                <span class="text-muted" style="font-size:var(--font-size-sm);">
                                    <?= (int) $a['note'] ?>/5
                                </span>
                            </div>

                            <!-- Commentaire -->
                            <blockquote class="mb-3" style="font-style:italic;">
                                « <?= e($a['description'] ?? '') ?> »
                            </blockquote>

                            <!-- Contexte : qui, quel menu, quelle commande -->
                            <p class="text-muted mb-3" style="font-size:var(--font-size-sm);">
                                <strong>
                                    <?= e($a['prenom']) ?>         <?= e($a['nom']) ?>
                                </strong><br>
                                Menu : <?= e($a['menu_titre']) ?><br>
                                Commande :
                                <a href="/employe/commande?numero=<?= urlencode($a['commande_id']) ?>">
                                    <code><?= e($a['commande_id']) ?></code>
                                </a>
                            </p>

                            <!-- Décision -->
                            <form action="/employe/avis/valider" method="POST" class="d-flex gap-2 mt-auto">
                                <input type="hidden" name="csrf_token" value="<?= e(csrf_generer()) ?>">
                                <input type="hidden" name="avis_id" value="<?= (int) $a['avis_id'] ?>">

                                <button type="submit" name="decision" value="publier" class="btn btn-primary flex-fill">
                                    ✅ Publier
                                </button>

                                <button type="submit" name="decision" value="refuser" class="btn flex-fill"
                                    style="background:var(--color-danger);color:#fff;"
                                    onclick="return confirm('Refuser cet avis ? Il ne sera jamais affiché.')">
                                    ❌ Refuser
                                </button>
                            </form>

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    <?php endif; ?>

</div>

</main>
<?php require dirname(__DIR__) . '/partials/footer.php'; ?>