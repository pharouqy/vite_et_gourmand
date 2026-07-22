<?php

/**
 * Template mail de réinitialisation de mot de passe
 * Variables : $prenom, $lien_reset
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

        .body {
            padding: 32px;
            color: #2C2C2C;
            line-height: 1.6;
        }

        .body h2 {
            color: #4F345A;
        }

        .btn {
            display: inline-block;
            margin: 24px 0;
            padding: 14px 32px;
            background: #4F345A;
            color: #C9F299;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
        }

        .alerte {
            background: #fff8e1;
            border-left: 4px solid #f1c40f;
            padding: 12px 16px;
            border-radius: 4px;
            font-size: 13px;
            color: #856404;
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
        </div>
        <div class="body">
            <h2>Réinitialisation de votre mot de passe</h2>
            <p>Bonjour <?= htmlspecialchars($prenom) ?>,</p>
            <p>
                Vous avez demandé la réinitialisation de votre mot de passe.
                Cliquez sur le bouton ci-dessous pour en choisir un nouveau.
            </p>

            <a href="<?= htmlspecialchars($lien_reset) ?>" class="btn">
                Réinitialiser mon mot de passe
            </a>

            <div class="alerte">
                ⚠️ Ce lien est valable <strong>1 heure</strong> et
                ne peut être utilisé <strong>qu'une seule fois</strong>.
            </div>

            <p style="margin-top: 24px; font-size: 13px; color: #6b6b8a;">
                Si vous n'avez pas fait cette demande, ignorez ce mail.
                Votre mot de passe reste inchangé.
            </p>
        </div>
        <div class="footer">
            © <?= date('Y') ?> Vite & Gourmand — Tous droits réservés
        </div>
    </div>
</body>

</html>