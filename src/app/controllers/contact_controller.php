<?php

declare(strict_types=1);

use PHPMailer\PHPMailer\Exception;

function contact_index(): void
{
    render('public/contact', ['titre_page' => 'Contact']);
}

function contact_envoyer(): void
{
    csrf_verifier();

    // ── Récupération et validation ────────────────────────────────
    $nom     = post_param('nom', '');
    $prenom  = post_param('prenom', '');
    $email   = post_param('email', '');
    $sujet   = post_param('sujet', '');
    $message = post_param('message', '');

    $erreurs = [];

    if (strlen($nom) < 2)    $erreurs[] = 'Nom invalide.';
    if (strlen($prenom) < 2) $erreurs[] = 'Prénom invalide.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs[] = 'Email invalide.';
    }
    if (empty($sujet))       $erreurs[] = 'Veuillez choisir un sujet.';
    if (strlen($message) < 10) {
        $erreurs[] = 'Le message doit contenir au moins 10 caractères.';
    }

    if (!empty($erreurs)) {
        $_SESSION['flash'] = [
            'type'    => 'danger',
            'message' => implode('<br>', $erreurs),
        ];
        redirect('/contact');
    }

    // ── Envoi des deux mails ──────────────────────────────────────
    $expediteur = trim($prenom . ' ' . $nom);
    $envoi_ok   = true;

    // Mail 1 → équipe Vite & Gourmand
    try {
        $mail = creer_mailer();
        $mail->addAddress(
            getenv('MAIL_FROM') ?: 'contact@viteetgourmand.fr',
            'Vite & Gourmand'
        );
        $mail->addReplyTo($email, $expediteur);
        $mail->isHTML(true);
        $mail->Subject = "[Contact] {$sujet} — {$expediteur}";
        $mail->Body    = "
            <h2>Nouveau message de contact</h2>
            <p><strong>De :</strong> {$expediteur} ({$email})</p>
            <p><strong>Sujet :</strong> {$sujet}</p>
            <hr>
            <p>" . nl2br(htmlspecialchars($message)) . "</p>
        ";
        $mail->AltBody = "De : {$expediteur} ({$email})\nSujet : {$sujet}\n\n{$message}";
        $mail->send();
    } catch (Exception $e) {
        error_log('[Contact → équipe] Échec : ' . $e->getMessage());
        $envoi_ok = false;
    }

    // Mail 2 → accusé de réception à l'expéditeur
    try {
        $mail2 = creer_mailer();
        $mail2->addAddress($email, $expediteur);
        $mail2->isHTML(true);
        $mail2->Subject = 'Vite & Gourmand — Nous avons bien reçu votre message';
        $mail2->Body    = "
            <div style='font-family:Arial,sans-serif;max-width:580px;margin:0 auto;'>
                <div style='background:#4F345A;padding:24px;text-align:center;'>
                    <h1 style='color:#C9F299;margin:0;font-size:20px;'>
                        🍽 Vite & Gourmand
                    </h1>
                </div>
                <div style='padding:24px;'>
                    <p>Bonjour {$prenom},</p>
                    <p>
                        Nous avons bien reçu votre message concernant
                        <strong>{$sujet}</strong>.
                        Notre équipe vous répondra sous 24h ouvrées.
                    </p>
                    <p style='color:#6b6b8a;font-size:13px;'>
                        Récapitulatif de votre message :<br>
                        <em>" . nl2br(htmlspecialchars($message)) . "</em>
                    </p>
                </div>
                <div style='background:#f0ebf5;padding:16px;text-align:center;font-size:12px;color:#6b6b8a;'>
                    © " . date('Y') . " Vite & Gourmand
                </div>
            </div>
        ";
        $mail2->AltBody = "Bonjour {$prenom}, nous avons bien reçu votre message. Réponse sous 24h.";
        $mail2->send();
    } catch (Exception $e) {
        error_log('[Contact → expéditeur] Échec : ' . $e->getMessage());
    }

    // ── Flash et redirection ──────────────────────────────────────
    $_SESSION['flash'] = [
        'type'    => $envoi_ok ? 'success' : 'danger',
        'message' => $envoi_ok
            ? 'Votre message a bien été envoyé. Nous vous répondrons sous 24h.'
            : 'Une erreur est survenue. Veuillez réessayer ou nous contacter directement par email.',
    ];

    redirect('/contact');
}