<?php
$titre_page = 'Commande ' . $commande['numero_commande'];
require dirname(__DIR__) . '/partials/header.php';

$badge = badge_statut($commande['statut']);
$total = (float)$commande['prix_menu'] + (float)$commande['prix_livraison'];

// Le prêt de matériel n'est modifiable qu'avant la livraison
$avant_livraison = in_array(
    $commande['statut'],
    [STATUT_ACCEPTE, STATUT_PREPARATION, STATUT_LIVRAISON],
    true
);
?>

<div class="container py-xl">

    <div class="d-flex align-items-center gap-3 mb-4 flex-wrap">
        <a href="/employe/commandes" class="btn btn-ghost btn-sm">
            ← Commandes
        </a>
        <h1 class="h4 mb-0" style="color:var(--color-primary);">
            Commande <code><?= e($commande['numero_commande']) ?></code>
        </h1>
        <span class="badge <?= $badge['classe'] ?>">
            <?= e($commande['statut']) ?>
        </span>
    </div>

    <div class="row g-4">

        <!-- ══ Colonne gauche : client + prestation ═════════════════ -->
        <div class="col-lg-7">

            <div class="card mb-4">
                <div class="card-header">
                    <h2 class="h5 mb-0" style="color:#fff;">Client</h2>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <strong>
                            <?= e($commande['client_prenom']) ?>
                            <?= e($commande['client_nom']) ?>
                        </strong>
                    </p>
                    <p class="mb-1">
                        📞
                        <a href="tel:<?= e($commande['client_telephone']) ?>">
                            <?= e($commande['client_telephone']) ?>
                        </a>
                    </p>
                    <p class="mb-0">
                        ✉️
                        <a href="mailto:<?= e($commande['client_email']) ?>">
                            <?= e($commande['client_email']) ?>
                        </a>
                    </p>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2 class="h5 mb-0" style="color:#fff;">Prestation</h2>
                </div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr>
                            <td class="text-muted">Menu</td>
                            <td><strong><?= e($commande['menu_titre']) ?></strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Personnes</td>
                            <td><?= (int)$commande['nombre_personne'] ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Date</td>
                            <td><?= date_fr($commande['date_prestation']) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Heure</td>
                            <td><?= e(substr($commande['heure_livraison'], 0, 5)) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Prix menu</td>
                            <td><?= prix_format((float)$commande['prix_menu']) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Livraison</td>
                            <td><?= prix_format((float)$commande['prix_livraison']) ?></td>
                        </tr>
                        <tr>
                            <td><strong>Total</strong></td>
                            <td>
                                <strong class="card-prix">
                                    <?= prix_format($total) ?>
                                </strong>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Matériel prêté</td>
                            <td>
                                <?= (int)$commande['pret_materiel'] === 1 ? 'Oui' : 'Non' ?>
                                <?php if ((int)$commande['pret_materiel'] === 1): ?>
                                    — restitué :
                                    <?= (int)$commande['restitution_materiel'] === 1 ? 'Oui' : 'Non' ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

        </div>

        <!-- ══ Colonne droite : actions + historique ═══════════════ -->
        <div class="col-lg-5">

            <!-- ── Changement de statut ──────────────────────────── -->
            <div class="card mb-4">
                <div class="card-header">
                    <h2 class="h5 mb-0" style="color:#fff;">Statut</h2>
                </div>
                <div class="card-body">

                    <?php if (empty($suivants)): ?>
                        <p class="text-muted mb-0">
                            Aucune action possible : la commande est
                            <strong><?= e($commande['statut']) ?></strong>.
                        </p>
                    <?php else: ?>
                        <?php foreach ($suivants as $suivant): ?>
                            <form action="/employe/commande/statut"
                                  method="POST" class="mb-2">
                                <input type="hidden" name="csrf_token"
                                       value="<?= e(csrf_generer()) ?>">
                                <input type="hidden" name="numero"
                                       value="<?= e($commande['numero_commande']) ?>">
                                <input type="hidden" name="action" value="changer">
                                <input type="hidden" name="nouveau_statut"
                                       value="<?= e($suivant) ?>">
                                <button type="submit" class="btn btn-primary w-100">
                                    Passer à « <?= e($suivant) ?> »
                                </button>
                            </form>

                            <?php if ($suivant === STATUT_MATERIEL): ?>
                                <p class="text-muted"
                                   style="font-size:var(--font-size-xs);">
                                    Le client recevra un email lui rappelant de
                                    restituer le matériel sous
                                    <?= (int)DELAI_RETOUR_MATERIEL ?> jours ouvrés,
                                    sous peine de
                                    <?= prix_format(PENALITE_MATERIEL) ?> de frais.
                                </p>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>

                </div>
            </div>

            <!-- ── Prêt de matériel (avant livraison) ────────────── -->
            <?php if ($avant_livraison): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h2 class="h5 mb-0" style="color:#fff;">Matériel</h2>
                    </div>
                    <div class="card-body">
                        <form action="/employe/commande/statut" method="POST">
                            <input type="hidden" name="csrf_token"
                                   value="<?= e(csrf_generer()) ?>">
                            <input type="hidden" name="numero"
                                   value="<?= e($commande['numero_commande']) ?>">
                            <input type="hidden" name="action" value="materiel">

                            <label style="display:flex;align-items:center;gap:8px;
                                          font-weight:400;cursor:pointer;">
                                <input type="checkbox" name="pret_materiel" value="1"
                                       <?= (int)$commande['pret_materiel'] === 1 ? 'checked' : '' ?>
                                       style="accent-color:var(--color-primary);">
                                Du matériel est prêté au client
                            </label>

                            <button type="submit" class="btn btn-outline btn-sm mt-3">
                                Enregistrer
                            </button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <!-- ── Annulation avec motif ─────────────────────────── -->
            <?php if ($peut_annuler): ?>
                <div class="card mb-4" style="border:1px solid var(--color-danger);">
                    <div class="card-header"
                         style="background:var(--color-danger);">
                        <h2 class="h5 mb-0" style="color:#fff;">
                            Annuler la commande
                        </h2>
                    </div>
                    <div class="card-body">
                        <p class="text-muted"
                           style="font-size:var(--font-size-sm);">
                            Contactez d'abord le client (téléphone ou email),
                            puis indiquez ici comment vous l'avez joint et
                            pourquoi la commande est annulée.
                        </p>

                        <form action="/employe/commande/statut" method="POST"
                              onsubmit="return confirm('Confirmer l\'annulation de cette commande ?')">
                            <input type="hidden" name="csrf_token"
                                   value="<?= e(csrf_generer()) ?>">
                            <input type="hidden" name="numero"
                                   value="<?= e($commande['numero_commande']) ?>">
                            <input type="hidden" name="action" value="annuler">

                            <div class="form-group">
                                <label for="mode_contact">
                                    Mode de contact utilisé *
                                </label>
                                <select id="mode_contact" name="mode_contact" required>
                                    <option value="">Choisir…</option>
                                    <?php foreach ($modes as $mode): ?>
                                        <option value="<?= e($mode) ?>">
                                            <?= e(ucfirst($mode)) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="motif">Motif de l'annulation *</label>
                                <textarea id="motif" name="motif" rows="3"
                                          required minlength="10"
                                          style="resize:vertical;"></textarea>
                            </div>

                            <button type="submit" class="btn w-100"
                                    style="background:var(--color-danger);color:#fff;">
                                Annuler la commande
                            </button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <!-- ── Motif d'une commande déjà annulée ─────────────── -->
            <?php if ($commande['statut'] === STATUT_ANNULEE): ?>
                <div class="alerte alerte-danger">
                    <strong>Commande annulée</strong><br>
                    Contact :
                    <?= e($commande['mode_contact_annulation'] ?? 'non renseigné') ?><br>
                    Motif :
                    <?= e($commande['motif_annulation'] ?? 'non renseigné') ?>
                </div>
            <?php endif; ?>

            <!-- ── Historique ────────────────────────────────────── -->
            <div class="card">
                <div class="card-header">
                    <h2 class="h5 mb-0" style="color:#fff;">Historique</h2>
                </div>
                <div class="card-body">
                    <?php if (empty($commande['historique'])): ?>
                        <p class="text-muted mb-0">Aucun historique.</p>
                    <?php else: ?>
                        <ul class="list-unstyled mb-0">
                            <?php foreach ($commande['historique'] as $h): ?>
                                <li class="mb-2">
                                    <strong><?= e($h['statut']) ?></strong><br>
                                    <small class="text-muted">
                                        <?= date('d/m/Y à H:i', strtotime($h['created_at'])) ?>
                                    </small>
                                    <?php if (!empty($h['commentaire'])): ?>
                                        <br>
                                        <small style="font-style:italic;">
                                            <?= e($h['commentaire']) ?>
                                        </small>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>

</main>
<?php require dirname(__DIR__) . '/partials/footer.php'; ?>