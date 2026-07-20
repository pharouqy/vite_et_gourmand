<?php

declare(strict_types=1);

/**
 * Échappe une chaîne pour l'affichage HTML.
 * À utiliser sur TOUTE variable affichée dans une vue.
 * Protection anti-XSS.
 *
 * Exemple : echo e($user['nom']);
 */
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Vérifie si un utilisateur est connecté.
 */
function est_connecte(): bool
{
    return isset($_SESSION['utilisateur_id']);
}

/**
 * Retourne le rôle de l'utilisateur connecté.
 * 1 = Admin, 2 = Employé, 3 = Client
 */
function role_actuel(): ?int
{
    return $_SESSION['role_id'] ?? null;
}

/**
 * Formate un prix en euros (contexte algérien → DZD si besoin).
 * Exemple : prix_format(2500.00) → "2 500,00 DA"
 */
function prix_format(float $montant): string
{
    return number_format($montant, 2, ',', ' ') . ' DA';
}

/**
 * Formate une date SQL (YYYY-MM-DD) en date lisible française.
 * Exemple : date_fr('2026-06-20') → "20/06/2026"
 */
function date_fr(string $date): string
{
    return date('d/m/Y', strtotime($date));
}
