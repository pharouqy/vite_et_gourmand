<?php

declare(strict_types=1);

/**
 * Récupère tous les horaires d'ouverture.
 * Retourne un tableau indexé par jour.
 */
function horaires_tous(): array
{
    $pdo = getPDO();
    $stmt = $pdo->query('SELECT * FROM horaire ORDER BY horaire_id');
    return $stmt->fetchAll();
}

/**
 * Enregistre les horaires de toute la semaine en une seule transaction :
 * soit les 7 jours sont mis à jour, soit aucun.
 * $lignes : [['id' => 1, 'ouverture' => '09:00'|null, 'fermeture' => '18:00'|null], ...]
 * NULL pour les deux heures = jour fermé.
 */
function horaires_enregistrer(array $lignes): void
{
    $pdo = getPDO();
    $pdo->beginTransaction();

    try {
        $stmt = $pdo->prepare('
            UPDATE horaire
            SET heure_ouverture = ?, heure_fermeture = ?
            WHERE horaire_id = ?
        ');

        foreach ($lignes as $l) {
            $stmt->execute([$l['ouverture'], $l['fermeture'], $l['id']]);
        }

        $pdo->commit();

    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
}