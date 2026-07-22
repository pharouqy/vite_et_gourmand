<?php

declare(strict_types=1);

/**
 * Crée un token de réinitialisation pour un utilisateur.
 * Supprime d'abord les anciens tokens de cet utilisateur
 * pour éviter l'accumulation.
 */
function token_creer(int $utilisateur_id): string
{
    $pdo = getPDO();

    // Supprimer les anciens tokens de cet utilisateur
    $pdo->prepare('DELETE FROM token_reset WHERE utilisateur_id = ?')
        ->execute([$utilisateur_id]);

    // Générer un token cryptographiquement sûr (64 caractères hex)
    $token     = bin2hex(random_bytes(32));
    $expire_at = date('Y-m-d H:i:s', time() + 3600); // +1 heure

    $stmt = $pdo->prepare("
        INSERT INTO token_reset (token, utilisateur_id, expire_at, utilise)
        VALUES (?, ?, ?, 0)
    ");
    $stmt->execute([$token, $utilisateur_id, $expire_at]);

    return $token;
}

/**
 * Récupère un token valide (non expiré, non utilisé).
 * Retourne le tableau complet ou null.
 */
function token_trouver_valide(string $token): ?array
{
    $pdo  = getPDO();
    $stmt = $pdo->prepare("
        SELECT t.*, u.utilisateur_id, u.email, u.prenom, u.nom
        FROM token_reset t
        JOIN utilisateur u ON u.utilisateur_id = t.utilisateur_id
        WHERE t.token    = ?
          AND t.utilise  = 0
          AND t.expire_at > NOW()
    ");
    $stmt->execute([$token]);
    $result = $stmt->fetch();
    return $result ?: null;
}

/**
 * Invalide un token après utilisation.
 */
function token_invalider(string $token): void
{
    $pdo = getPDO();
    $pdo->prepare('UPDATE token_reset SET utilise = 1 WHERE token = ?')
        ->execute([$token]);
}
