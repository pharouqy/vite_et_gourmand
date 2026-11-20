<?php
/**
 * Mail envoyé quand la commande passe à "terminée".
 * Variables : $prenom, $numero, $lien_avis
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
            <p>Votre avis nous intéresse</p>
        </div>

        <div class="body">
            <h2>Bonjour <?= htmlspecialchars($prenom) ?>,</h2>

            <p>
                Votre commande <strong><?= htmlspecialchars($numero) ?></strong>
                est maintenant terminée. Nous espérons que tout s'est bien passé !
            </p>

            <p>
                Vous pouvez dès à présent vous connecter à votre compte pour nous
                donner votre avis : une note de 1 à 5 et un commentaire.
            </p>

            <a href="<?= htmlspecialchars($lien_avis) ?>" class="btn">
                Donner mon avis
            </a>

            <p style="font-size:13px; color:#6b6b8a;">
                Votre avis sera publié après validation par notre équipe.
            </p>
        </div>

        <div class="footer">
            © <?= date('Y') ?> Vite & Gourmand — Tous droits réservés
        </div>

    </div>
</body>

</html>