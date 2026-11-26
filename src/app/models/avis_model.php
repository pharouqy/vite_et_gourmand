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

/**
 * Avis en attente de modération, du plus ancien au plus récent.
 * On joint le client et le menu : l'employé doit savoir de quelle
 * prestation parle l'avis avant de le publier.
 */
function avis_a_moderer(): array
{
    $stmt = getPDO()->query("
        SELECT
            a.avis_id,
            a.note,
            a.description,
            a.commande_id,
            u.prenom,
            u.nom,
            m.titre AS menu_titre
        FROM avis a
        JOIN utilisateur u ON u.utilisateur_id   = a.utilisateur_id
        JOIN commande    c ON c.numero_commande  = a.commande_id
        JOIN menu        m ON m.menu_id          = c.menu_id
        WHERE a.statut = 'en attente'
        ORDER BY a.avis_id ASC
    ");

    return $stmt->fetchAll();
}

/**
 * Publie ou refuse un avis.
 * Retourne true si un avis a bien été modifié, false sinon
 * (identifiant inexistant, ou avis déjà modéré par un collègue).
 */
function avis_changer_statut(int $avis_id, string $statut): bool
{
    if (!in_array($statut, ['publié', 'refusé'], true)) {
        throw new InvalidArgumentException("Statut d'avis invalide : {$statut}");
    }

    $stmt = getPDO()->prepare("
        UPDATE avis
        SET statut = ?
        WHERE avis_id = ?
          AND statut = 'en attente'
    ");
    $stmt->execute([$statut, $avis_id]);

    return $stmt->rowCount() === 1;
}