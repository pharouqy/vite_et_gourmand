<?php

declare(strict_types=1);

/**
 * Récupère les avis validés pour l'affichage public.
 * Joint la table utilisateur pour afficher le prénom du client.
 */
function avis_publies(int $limite = 6): array
{
    $pdo = getPDO();
    $stmt = $pdo->prepare("
        SELECT
            a.avis_id,
            a.note,
            a.description,
            u.prenom,
            LEFT(u.nom, 1) AS initiale_nom
        FROM avis a
        JOIN utilisateur u ON u.utilisateur_id = a.utilisateur_id
        WHERE a.statut = 'publié'
        ORDER BY a.avis_id DESC
        LIMIT ?
    ");
    $stmt->execute([$limite]);
    return $stmt->fetchAll();
}

/**
 * Dépose un avis sur une commande.
 */
function avis_deposer(
    int $note,
    string $description,
    string $commande_id,
    int $utilisateur_id
): void {
    getPDO()->prepare("
        INSERT INTO avis
            (note, description, statut, commande_id, utilisateur_id)
        VALUES (?, ?, 'en attente', ?, ?)
    ")->execute([$note, $description, $commande_id, $utilisateur_id]);
}

/**
 * Vérifie si un avis existe déjà pour une commande.
 * Retourne l'avis_id ou null.
 */
function avis_par_commande(string $commande_id): ?int
{
    $stmt = getPDO()->prepare("
        SELECT avis_id FROM avis WHERE commande_id = ?
    ");
    $stmt->execute([$commande_id]);
    $result = $stmt->fetchColumn();
    return $result ? (int) $result : null;
}