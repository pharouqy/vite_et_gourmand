<?php

declare(strict_types=1);

/**
 * Tous les allergènes, par ordre alphabétique.
 */
function allergenes_tous(): array
{
    return getPDO()
        ->query('SELECT * FROM allergene ORDER BY libelle')
        ->fetchAll();
}

/**
 * Identifiants des allergènes d'un plat.
 */
function allergenes_du_plat(int $plat_id): array
{
    $stmt = getPDO()->prepare(
        'SELECT allergene_id FROM contient WHERE plat_id = ?'
    );
    $stmt->execute([$plat_id]);

    return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
}

/**
 * Remplace la liste des allergènes d'un plat.
 * Les identifiants inexistants sont ignorés (pas d'erreur).
 */
function plat_maj_allergenes(int $plat_id, array $allergene_ids): void
{
    $pdo = getPDO();
    $pdo->beginTransaction();

    try {
        $pdo->prepare('DELETE FROM contient WHERE plat_id = ?')
            ->execute([$plat_id]);

        $ids = array_unique(array_map('intval', $allergene_ids));
        $stmt = $pdo->prepare('
            INSERT INTO contient (plat_id, allergene_id)
            SELECT ?, allergene_id FROM allergene WHERE allergene_id = ?
        ');

        foreach ($ids as $allergene_id) {
            $stmt->execute([$plat_id, $allergene_id]);
        }

        $pdo->commit();

    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
}