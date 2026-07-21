<?php

declare(strict_types=1);

/**
 * Exige qu'un utilisateur soit connecté.
 * Si non connecté, redirige vers /connexion
 * en conservant l'URL demandée pour y revenir après.
 */
function require_auth(): void
{
    if (!est_connecte()) {
        $redirect = urlencode($_SERVER['REQUEST_URI'] ?? '/');
        redirect('/connexion?redirect=' . $redirect);
    }
}

/**
 * Exige un rôle précis.
 * Redirige vers l'accueil si le rôle ne correspond pas.
 *
 * Exemple : require_role(ROLE_EMPLOYE);
 */
function require_role(int $role_requis): void
{
    require_auth();

    if (role_actuel() !== $role_requis) {
        $_SESSION['flash'] = [
            'type'    => 'danger',
            'message' => 'Accès refusé — vous n\'avez pas les droits nécessaires.',
        ];
        redirect('/');
    }
}

/**
 * Exige d'être employé OU admin (admin a tous les droits employé).
 */
function require_employe_ou_admin(): void
{
    require_auth();
    $role = role_actuel();

    if ($role !== ROLE_EMPLOYE && $role !== ROLE_ADMIN) {
        $_SESSION['flash'] = [
            'type'    => 'danger',
            'message' => 'Accès réservé aux employés.',
        ];
        redirect('/');
    }
}

/**
 * Redirige vers l'espace de l'utilisateur s'il est déjà connecté.
 * Utile sur les pages /connexion et /inscription.
 */
function redirect_si_connecte(): void
{
    if (est_connecte()) {
        auth_rediriger_selon_role();
    }
}
