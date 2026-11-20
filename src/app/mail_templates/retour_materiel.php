<?php
/**
 * Mail envoyé quand la commande passe à "en attente du retour de matériel".
 * Variables : $prenom, $numero, $delai, $penalite
 */
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #F8F6FA;
            margin: 0;
            padding: 0;
        }

        .wrapper {
            max-width: 580px;
            margin: 40px auto;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .08);
        }

        .header {
            background: #4F345A;
            padding: 32px;
            text-align: center;
        }

        .header h1 {
            color: #C9F299;
            margin: 0;
            font-size: 22px;
        }

        .header p {
            color: rgba(255, 255, 255, .8);
            margin: 8px 0 0;
            font-size: 14px;
        }

        .body {
            padding: 32px;
            color: #2C2C2C;
            line-height: 1.6;
        }

        .body h2 {
            color: #4F345A;
            font-size: 18px;
        }

        .alerte {
            background: #fff8e1;
            border-left: 4px solid #f1c40f;
            padding: 12px 16px;
            border-radius: 4px;
            font-size: 14px;
            color: #856404;
            margin: 20px 0;
        }

        .footer {
            background: #f0ebf5;
            padding: 16px 32px;
            text-align: center;
            font-size: 12px;
            color: #6b6b8a;
        }
    </style>
</head>

<body>
    <div class="wrapper">

        <div class="header">
            <h1>🍽 Vite & Gourmand</h1>
            <p>Retour de matériel</p>
        </div>

        <div class="body">
            <h2>Bonjour <?= htmlspecialchars($prenom) ?>,</h2>

            <p>
                Votre commande <strong><?= htmlspecialchars($numero) ?></strong>
                a bien été livrée. Du matériel vous a été prêté à cette occasion
                et doit nous être restitué.
            </p>

            <div class="alerte">
                ⚠️ Merci de restituer le matériel sous
                <strong><?= (int) $delai ?> jours ouvrés</strong>.<br>
                Passé ce délai, des frais de
                <strong><?= htmlspecialchars($penalite) ?></strong>
                vous seront facturés, conformément à nos conditions générales de vente.
            </div>

            <p>
                Pour organiser la restitution, contactez notre équipe :
                <a href="mailto:contact@viteetgourmand.fr">contact@viteetgourmand.fr</a>
            </p>

            <p style="font-size:13px; color:#6b6b8a;">
                Si vous avez déjà rendu le matériel, vous pouvez ignorer ce message.
            </p>
        </div>

        <div class="footer">
            © <?= date('Y') ?> Vite & Gourmand — Tous droits réservés
        </div>

    </div>
</body>

</html>