<?php

declare(strict_types=1);

/**
 * Génère un token CSRF et le stocke en session.
 * À appeler dans chaque formulaire : value="<?= e(csrf_generer()) ?>"
 */
function csrf_generer(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Vérifie que le token CSRF soumis est valide.
 * À appeler en tête de chaque endpoint POST.
 */
function csrf_verifier(): void
{
    $token_session    = $_SESSION['csrf_token'] ?? '';
    $token_formulaire = $_POST['csrf_token']    ?? '';

    if (empty($token_formulaire) || !hash_equals($token_session, $token_formulaire)) {
        http_response_code(403);
        die('Requête invalide — token CSRF manquant ou expiré.');
    }

    // Renouvelle le token après validation (rotation)
    unset($_SESSION['csrf_token']);
}
