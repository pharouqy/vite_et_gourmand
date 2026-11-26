<?php

declare(strict_types=1);

/**
 * Tous les plats, par ordre alphabétique.
 */
function plats_tous(): array
{
    return getPDO()
        ->query('SELECT * FROM plat ORDER BY titre_plat')
        ->fetchAll();
}

/**
 * Un plat par son identifiant, ou null s'il n'existe pas.
 */
function plat_par_id(int $plat_id): ?array
{
    $stmt = getPDO()->prepare('SELECT * FROM plat WHERE plat_id = ?');
    $stmt->execute([$plat_id]);
    $plat = $stmt->fetch();

    return $plat ?: null;
}

/**
 * Liste détaillée pour l'écran de gestion : chaque plat avec
 * le nombre de menus qui l'utilisent et ses allergènes.
 */
function plats_liste_detail(): array
{
    return getPDO()->query("
        SELECT
            p.plat_id,
            p.titre_plat,
            p.photo,
            (SELECT COUNT(*)
               FROM compose c
              WHERE c.plat_id = p.plat_id) AS nb_menus,
            (SELECT GROUP_CONCAT(a.libelle ORDER BY a.libelle SEPARATOR ', ')
               FROM contient ct
               JOIN allergene a ON a.allergene_id = ct.allergene_id
              WHERE ct.plat_id = p.plat_id) AS allergenes
        FROM plat p
        ORDER BY p.titre_plat
    ")->fetchAll();
}

/**
 * Crée un plat. Retourne son identifiant.
 * $photo : chemin relatif (ex. /uploads/menus/xxx.jpg) ou null.
 */
function plat_creer(string $titre, ?string $photo = null): int
{
    $pdo = getPDO();
    $pdo->prepare('INSERT INTO plat (titre_plat, photo) VALUES (?, ?)')
        ->execute([$titre, $photo]);

    return (int) $pdo->lastInsertId();
}

/**
 * Modifie le nom d'un plat. La photo se change séparément.
 */
function plat_modifier(int $plat_id, string $titre): void
{
    getPDO()->prepare('UPDATE plat SET titre_plat = ? WHERE plat_id = ?')
        ->execute([$titre, $plat_id]);
}

/**
 * Change (ou retire, avec null) la photo d'un plat.
 */
function plat_maj_photo(int $plat_id, ?string $photo): void
{
    getPDO()->prepare('UPDATE plat SET photo = ? WHERE plat_id = ?')
        ->execute([$photo, $plat_id]);
}

/**
 * Supprime un plat et ses liaisons avec les allergènes.
 * Retourne false si le plat est utilisé dans un menu
 * (la clé étrangère de la base le refuse) : rien n'est alors supprimé.
 */
function plat_supprimer(int $plat_id): bool
{
    $pdo = getPDO();
    $pdo->beginTransaction();

    try {
        $pdo->prepare('DELETE FROM contient WHERE plat_id = ?')
            ->execute([$plat_id]);

        $stmt = $pdo->prepare('DELETE FROM plat WHERE plat_id = ?');
        $stmt->execute([$plat_id]);

        $pdo->commit();
        return $stmt->rowCount() === 1;

    } catch (PDOException $e) {
        $pdo->rollBack();

        // 23000 = violation d'intégrité : un menu contient ce plat
        if ($e->getCode() === '23000') {
            return false;
        }
        throw $e;
    }
}