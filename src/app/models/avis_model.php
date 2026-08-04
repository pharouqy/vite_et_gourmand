<?php

declare(strict_types=1);

/**
 * Récupère les avis validés pour l'affichage public.
 * Joint la table utilisateur pour afficher le prénom du client.
 */
function avis_publies(int $limite = 6): array
{
    $pdo  = getPDO();
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
