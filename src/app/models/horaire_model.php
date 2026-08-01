<?php

declare(strict_types=1);

/**
 * Récupère tous les horaires d'ouverture.
 * Retourne un tableau indexé par jour.
 */
function horaires_tous(): array
{
    $pdo  = getPDO();
    $stmt = $pdo->query('SELECT * FROM horaire ORDER BY horaire_id');
    return $stmt->fetchAll();
}
