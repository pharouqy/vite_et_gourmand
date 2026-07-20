<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/models/utilisateur_model.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// ══════════════════════════════════════════════════════
// INSCRIPTION
// ══════════════════════════════════════════════════════

function auth_inscription_form(): void
{
    render('auth/inscription', ['titre_page' => 'Inscription']);
}

function auth_inscription_traiter(): void
{
    // ── 1. Vérification CSRF ─────────────────────────────────────────
    csrf_verifier();

    // ── 2. Récupération des champs ───────────────────────────────────
    $nom              = post_param('nom', '');
    $prenom           = post_param('prenom', '');
    $email            = post_param('email', '');
    $telephone        = post_param('telephone', '');
    $adresse_postale  = post_param('adresse_postale', '');
    $ville            = post_param('ville', '');
    $pays             = post_param('pays', '');
    $password         = post_raw('password', '');
    $password_confirm = post_raw('password_confirm', '');
    $rgpd             = post_param('rgpd', '');

    // ── 3. Revalidation serveur (ne jamais faire confiance au client) ─
    $erreurs = [];

    if (strlen($nom) < 2) {
        $erreurs[] = 'Le nom doit contenir au moins 2 caractères.';
    }
    if (strlen($prenom) < 2) {
        $erreurs[] = 'Le prénom doit contenir au moins 2 caractères.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs[] = 'L\'adresse email est invalide.';
    }
    if (strlen($telephone) < 8) {
        $erreurs[] = 'Le numéro de téléphone est invalide.';
    }
    if (strlen($adresse_postale) < 5) {
        $erreurs[] = 'L\'adresse postale est invalide.';
    }
    if (empty($ville)) {
        $erreurs[] = 'La ville est obligatoire.';
    }
    if (empty($pays)) {
        $erreurs[] = 'Le pays est obligatoire.';
    }
    if (empty($rgpd)) {
        $erreurs[] = 'Vous devez accepter les conditions générales.';
    }

    // ── 4. Validation du mot de passe (regex sujet) ──────────────────
    $regex_mdp = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{10,}$/';

    if (!preg_match($regex_mdp, $password)) {
        $erreurs[] = 'Le mot de passe doit contenir au moins 10 caractères, '
            . '1 majuscule, 1 minuscule, 1 chiffre et 1 caractère spécial.';
    }
    if ($password !== $password_confirm) {
        $erreurs[] = 'Les deux mots de passe ne correspondent pas.';
    }

    // ── 5. Unicité de l'email ────────────────────────────────────────
    if (empty($erreurs) && utilisateur_email_existe($email)) {
        $erreurs[] = 'Cette adresse email est déjà associée à un compte.';
    }

    // ── 6. Retour au formulaire si erreurs ───────────────────────────
    if (!empty($erreurs)) {
        $_SESSION['flash'] = [
            'type'    => 'danger',
            'message' => implode('<br>', $erreurs),
        ];
        redirect('/inscription');
    }

    // ── 7. Hachage sécurisé du mot de passe ─────────────────────────
    // PASSWORD_BCRYPT génère un hash de 60 chars → d'où VARCHAR(255) en base
    $hash = password_hash($password, PASSWORD_BCRYPT);

    // ── 8. Insertion en base ─────────────────────────────────────────
    $utilisateur_id = utilisateur_creer([
        'nom'             => $nom,
        'prenom'          => $prenom,
        'email'           => $email,
        'password'        => $hash,
        'telephone'       => $telephone,
        'adresse_postale' => $adresse_postale,
        'ville'           => $ville,
        'pays'            => $pays,
    ]);

    // ── 9. Envoi du mail de bienvenue ────────────────────────────────
    auth_envoyer_mail_bienvenue($prenom, $nom, $email);

    // ── 10. Flash de succès et redirection ───────────────────────────
    $_SESSION['flash'] = [
        'type'    => 'success',
        'message' => "Bienvenue {$prenom} ! Votre compte a été créé. "
            . "Un email de confirmation vous a été envoyé.",
    ];

    redirect('/connexion');
}

/**
 * Envoie le mail de bienvenue via PHPMailer + Mailtrap.
 */
function auth_envoyer_mail_bienvenue(string $prenom, string $nom, string $email): void
{
    try {
        $mail = creer_mailer();
        $mail->addAddress($email, $prenom . ' ' . $nom);
        $mail->isHTML(true);
        $mail->Subject = 'Bienvenue sur Vite & Gourmand !';

        // Capture du template HTML
        ob_start();
        require dirname(__DIR__) . '/mail_templates/bienvenue.php';
        $mail->Body = ob_get_clean();

        // Version texte brut (fallback pour les clients mail sans HTML)
        $mail->AltBody = "Bonjour {$prenom},\n\n"
            . "Votre compte Vite & Gourmand a été créé avec succès.\n"
            . "Connectez-vous sur : " . (getenv('APP_URL') ?: 'http://localhost:8080');

        $mail->send();
    } catch (Exception $e) {
        // Le mail échoue : on log mais on ne bloque PAS l'inscription
        // L'utilisateur est quand même créé — le mail est secondaire
        error_log('[Mail bienvenue] Échec : ' . $e->getMessage());
    }
}

// ══════════════════════════════════════════════════════
// CONNEXION (squelettes — implémentés en US-1.2)
// ══════════════════════════════════════════════════════

function auth_connexion_form(): void
{
    render('auth/connexion', ['titre_page' => 'Connexion']);
}

function auth_connexion_traiter(): void
{
    redirect('/connexion');
}
function auth_deconnexion(): void
{
    redirect('/');
}
function auth_oublie_form(): void
{
    redirect('/');
}
function auth_oublie_traiter(): void
{
    redirect('/');
}
function auth_reset_form(): void
{
    redirect('/');
}
function auth_reset_traiter(): void
{
    redirect('/');
}
