<?php
/**
 * Template mail confirmation commande
 * Variables : $prenom, $numero, $date_prestation,
 *             $heure, $adresse, $ville,
 *             $menu_titre, $nb_personnes,
 *             $prix_menu, $prix_livraison, $total,
 *             $remise_appliquee, $montant_remise
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body      { font-family: Arial, sans-serif;
                    background:#F8F6FA; margin:0; padding:0; }
        .wrapper  { max-width:580px; margin:40px auto;
                    background:#fff; border-radius:12px;
                    overflow:hidden;
                    box-shadow:0 4px 12px rgba(0,0,0,.08); }
        .header   { background:#4F345A; padding:32px;
                    text-align:center; }
        .header h1{ color:#C9F299; margin:0; font-size:22px; }
        .header p { color:rgba(255,255,255,.8);
                    margin:8px 0 0; font-size:14px; }
        .body     { padding:32px; color:#2C2C2C; }
        .body h2  { color:#4F345A; font-size:18px; }
        table     { width:100%; border-collapse:collapse;
                    margin:16px 0; }
        td        { padding:8px 12px;
                    border-bottom:1px solid #e8e3ee;
                    font-size:14px; }
        td:first-child { color:#6b6b8a; width:45%; }
        td:last-child  { font-weight:600; }
        .total    { background:#f3eef7; }
        .total td { font-size:16px; color:#4F345A; }
        .alerte   { background:#fff8e1;
                    border-left:4px solid #f1c40f;
                    padding:12px 16px; border-radius:4px;
                    font-size:13px; color:#856404;
                    margin-top:16px; }
        .btn      { display:inline-block; margin:24px 0;
                    padding:14px 32px; background:#4F345A;
                    color:#C9F299; text-decoration:none;
                    border-radius:8px; font-weight:bold; }
        .footer   { background:#f0ebf5; padding:16px 32px;
                    text-align:center;
                    font-size:12px; color:#6b6b8a; }
        .badge    { display:inline-block; padding:4px 12px;
                    background:#C9F299; color:#4F345A;
                    border-radius:99px; font-weight:700;
                    font-size:13px; }
    </style>
</head>
<body>
<div class="wrapper">

    <div class="header">
        <h1>🍽 Vite & Gourmand</h1>
        <p>Confirmation de commande</p>
    </div>

    <div class="body">
        <h2>Bonjour <?= htmlspecialchars($prenom) ?> !</h2>
        <p>
            Votre commande a bien été enregistrée.
            Notre équipe la traitera dans les plus brefs délais.
        </p>

        <p>
            Numéro de commande :
            <span class="badge"><?= htmlspecialchars($numero) ?></span>
        </p>

        <!-- Détails prestation -->
        <h3 style="color:#4F345A;font-size:15px;margin-top:24px;">
            📍 Prestation
        </h3>
        <table>
            <tr>
                <td>Date</td>
                <td><?= htmlspecialchars(
                    date('d/m/Y', strtotime($date_prestation))
                ) ?></td>
            </tr>
            <tr>
                <td>Heure de livraison</td>
                <td><?= htmlspecialchars($heure) ?></td>
            </tr>
            <tr>
                <td>Adresse</td>
                <td>
                    <?= htmlspecialchars($adresse) ?>,
                    <?= htmlspecialchars($ville) ?>
                </td>
            </tr>
        </table>

        <!-- Détails commande -->
        <h3 style="color:#4F345A;font-size:15px;margin-top:24px;">
            🍽 Commande
        </h3>
        <table>
            <tr>
                <td>Menu</td>
                <td><?= htmlspecialchars($menu_titre) ?></td>
            </tr>
            <tr>
                <td>Nombre de personnes</td>
                <td><?= (int)$nb_personnes ?></td>
            </tr>
            <tr>
                <td>Sous-total menu</td>
                <td><?= htmlspecialchars($prix_menu) ?></td>
            </tr>
            <?php if ($remise_appliquee): ?>
                <tr>
                    <td>Remise 10%</td>
                    <td style="color:#2e7d32;">
                        − <?= htmlspecialchars($montant_remise) ?>
                    </td>
                </tr>
            <?php endif; ?>
            <tr>
                <td>Frais de livraison</td>
                <td>
                    <?= (float)str_replace(' €', '', $prix_livraison) === 0.0
                        ? '<span style="color:#2e7d32;">Gratuit</span>'
                        : htmlspecialchars($prix_livraison) ?>
                </td>
            </tr>
            <tr class="total">
                <td>TOTAL</td>
                <td><?= htmlspecialchars($total) ?></td>
            </tr>
        </table>

        <!-- Conditions matériel -->
        <div class="alerte">
            ⚠️ <strong>Rappel conditions générales :</strong>
            En cas de prêt de matériel, celui-ci devra être
            restitué sous <strong>10 jours ouvrés</strong>.
            Passé ce délai, une pénalité de
            <strong>600 €</strong> sera facturée.
        </div>

        <a href="<?= getenv('APP_URL') ?: 'http://localhost:8080' ?>/compte/commandes"
           class="btn">
            Suivre ma commande
        </a>
    </div>

    <div class="footer">
        © <?= date('Y') ?> Vite & Gourmand — Tous droits réservés
    </div>

</div>
</body>
</html>