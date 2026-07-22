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
// CONNEXION
// ══════════════════════════════════════════════════════

function auth_connexion_form(): void
{
    // Si déjà connecté → rediriger vers l'espace correspondant
    if (est_connecte()) {
        auth_rediriger_selon_role();
    }

    render('auth/connexion', ['titre_page' => 'Connexion']);
}

function auth_connexion_traiter(): void
{
    // ── 1. Vérification CSRF ─────────────────────────────────────────
    csrf_verifier();

    // ── 2. Récupération des champs ───────────────────────────────────
    $email    = post_param('email', '');
    $password = post_raw('password', '');
    $redirect = post_param('redirect', '');

    // ── 3. Validation minimale ───────────────────────────────────────
    if (empty($email) || empty($password)) {
        $_SESSION['flash'] = [
            'type'    => 'danger',
            'message' => 'Veuillez remplir tous les champs.',
        ];
        redirect('/connexion');
    }

    // ── 4. Recherche de l'utilisateur ────────────────────────────────
    $utilisateur = utilisateur_par_email($email);

    // ── 5. Vérification du mot de passe ─────────────────────────────
    // On vérifie TOUJOURS le hash, même si l'email est introuvable.
    // Sans ça, un attaquant mesure le temps de réponse pour savoir
    // si l'email existe (timing attack).
    $hash_factice = '$2y$12$fakehashpourprevenirtimingattackXXXXXXXXXXXXXXXXXXXXXX';
    $hash_reel    = $utilisateur['password'] ?? $hash_factice;

    $mdp_valide = password_verify($password, $hash_reel);

    if (!$utilisateur || !$mdp_valide) {
        // Message IDENTIQUE que ce soit l'email ou le mdp qui soit faux
        $_SESSION['flash'] = [
            'type'    => 'danger',
            'message' => 'Identifiants incorrects.',
        ];
        redirect('/connexion');
    }

    // ── 6. Démarrage de session sécurisé ─────────────────────────────
    // Régénère l'ID de session pour prévenir la session fixation
    session_regenerate_id(true);

    // ── 7. Stockage des données en session ───────────────────────────
    $_SESSION['utilisateur_id'] = $utilisateur['utilisateur_id'];
    $_SESSION['nom']            = $utilisateur['nom'];
    $_SESSION['prenom']         = $utilisateur['prenom'];
    $_SESSION['email']          = $utilisateur['email'];
    $_SESSION['role_id']        = (int) $utilisateur['role_id'];

    // ── 8. Redirection selon le contexte ─────────────────────────────
    // Si l'utilisateur venait d'une page protégée, on l'y renvoie
    if (!empty($redirect) && str_starts_with($redirect, '/')) {
        redirect($redirect);
    }

    auth_rediriger_selon_role();
}

// ══════════════════════════════════════════════════════
// DÉCONNEXION
// ══════════════════════════════════════════════════════

function auth_deconnexion(): void
{
    // Vider toutes les variables de session
    $_SESSION = [];

    // Détruire le cookie de session côté navigateur
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    // Détruire la session côté serveur
    session_destroy();

    redirect('/connexion');
}

// ══════════════════════════════════════════════════════
// UTILITAIRE — Redirection selon le rôle
// ══════════════════════════════════════════════════════

function auth_rediriger_selon_role(): never
{
    $role = role_actuel();

    $destinations = [
        ROLE_ADMIN   => '/admin/statistiques',
        ROLE_EMPLOYE => '/employe/commandes',
        ROLE_CLIENT  => '/compte/commandes',
    ];

    redirect($destinations[$role] ?? '/');
}

// ══════════════════════════════════════════════════════
// MOT DE PASSE OUBLIÉ
// ══════════════════════════════════════════════════════

function auth_oublie_form(): void
{
    render('auth/mot-de-passe-oublie', ['titre_page' => 'Mot de passe oublié']);
}

function auth_oublie_traiter(): void
{
    csrf_verifier();

    $email = post_param('email', '');

    // ⚠️ Réponse IDENTIQUE que l'email existe ou non (sécurité)
    // On ne révèle jamais si un email est enregistré
    $message_generique = [
        'type'    => 'success',
        'message' => 'Si cette adresse est associée à un compte, '
                   . 'vous recevrez un email dans quelques instants.',
    ];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['flash'] = $message_generique;
        redirect('/mot-de-passe-oublie');
    }

    $utilisateur = utilisateur_par_email($email);

    if ($utilisateur) {
        // Générer et stocker le token
        $token = token_creer((int) $utilisateur['utilisateur_id']);

        // Construire le lien de réinitialisation
        $base_url   = getenv('APP_URL') ?: 'http://localhost:8080';
        $lien_reset = $base_url . '/reinitialisation?token=' . $token;

        // Envoyer le mail
        auth_envoyer_mail_reset(
            $utilisateur['prenom'],
            $utilisateur['email'],
            $lien_reset
        );
    }

    // Même réponse dans tous les cas
    $_SESSION['flash'] = $message_generique;
    redirect('/mot-de-passe-oublie');
}

function auth_reset_form(): void
{
    $token = get_param('token', '');

    // Vérifier que le token est valide AVANT d'afficher le formulaire
    if (empty($token) || !token_trouver_valide($token)) {
        $_SESSION['flash'] = [
            'type'    => 'danger',
            'message' => 'Ce lien de réinitialisation est invalide ou a expiré. '
                       . 'Veuillez faire une nouvelle demande.',
        ];
        redirect('/mot-de-passe-oublie');
    }

    render('auth/reinitialisation', ['titre_page' => 'Réinitialisation']);
}

function auth_reset_traiter(): void
{
    csrf_verifier();

    $token            = post_param('token', '');
    $password         = post_raw('password', '');
    $password_confirm = post_raw('password_confirm', '');

    // ── 1. Vérifier le token ─────────────────────────────────────────
    $token_data = token_trouver_valide($token);

    if (!$token_data) {
        $_SESSION['flash'] = [
            'type'    => 'danger',
            'message' => 'Ce lien a expiré ou a déjà été utilisé. '
                       . 'Veuillez faire une nouvelle demande.',
        ];
        redirect('/mot-de-passe-oublie');
    }

    // ── 2. Valider le nouveau mot de passe ───────────────────────────
    $regex_mdp = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{10,}$/';

    if (!preg_match($regex_mdp, $password)) {
        $_SESSION['flash'] = [
            'type'    => 'danger',
            'message' => 'Le mot de passe ne respecte pas les critères de sécurité.',
        ];
        redirect('/reinitialisation?token=' . urlencode($token));
    }

    if ($password !== $password_confirm) {
        $_SESSION['flash'] = [
            'type'    => 'danger',
            'message' => 'Les deux mots de passe ne correspondent pas.',
        ];
        redirect('/reinitialisation?token=' . urlencode($token));
    }

    // ── 3. Mettre à jour le mot de passe ────────────────────────────
    $hash = password_hash($password, PASSWORD_BCRYPT);
    utilisateur_maj_password((int) $token_data['utilisateur_id'], $hash);

    // ── 4. Invalider le token (usage unique) ─────────────────────────
    token_invalider($token);

    // ── 5. Flash et redirection ──────────────────────────────────────
    $_SESSION['flash'] = [
        'type'    => 'success',
        'message' => 'Votre mot de passe a été réinitialisé. '
                   . 'Vous pouvez maintenant vous connecter.',
    ];
    redirect('/connexion');
}

/**
 * Envoie le mail de réinitialisation.
 */
function auth_envoyer_mail_reset(
    string $prenom,
    string $email,
    string $lien_reset
): void {
    try {
        $mail = creer_mailer();
        $mail->addAddress($email, $prenom);
        $mail->isHTML(true);
        $mail->Subject = 'Réinitialisation de votre mot de passe — Vite & Gourmand';

        ob_start();
        require dirname(__DIR__) . '/mail_templates/reinitialisation_mdp.php';
        $mail->Body = ob_get_clean();

        $mail->AltBody = "Bonjour {$prenom},\n\n"
                       . "Lien de réinitialisation (valable 1h) :\n{$lien_reset}\n\n"
                       . "Si vous n'avez pas fait cette demande, ignorez ce mail.";

        $mail->send();

    } catch (Exception $e) {
        error_log('[Mail reset] Échec : ' . $e->getMessage());
    }
}