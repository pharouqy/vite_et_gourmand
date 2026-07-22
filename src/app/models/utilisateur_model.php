<?php

declare(strict_types=1);

/**
 * Vérifie si un email est déjà utilisé en base.
 */
function utilisateur_email_existe(string $email): bool
{
    $pdo  = getPDO();
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM utilisateur WHERE email = ?');
    $stmt->execute([$email]);
    return (int) $stmt->fetchColumn() > 0;
}

/**
 * Insère un nouvel utilisateur en base.
 * Retourne l'ID de l'utilisateur créé.
 */
function utilisateur_creer(array $donnees): int
{
    $pdo  = getPDO();
    $stmt = $pdo->prepare("
        INSERT INTO utilisateur
            (nom, prenom, email, password, telephone,
             adresse_postale, ville, pays, role_id, actif)
        VALUES
            (:nom, :prenom, :email, :password, :telephone,
             :adresse_postale, :ville, :pays, :role_id, 1)
    ");

    $stmt->execute([
        ':nom'             => $donnees['nom'],
        ':prenom'          => $donnees['prenom'],
        ':email'           => $donnees['email'],
        ':password'        => $donnees['password'], // déjà hashé
        ':telephone'       => $donnees['telephone'],
        ':adresse_postale' => $donnees['adresse_postale'],
        ':ville'           => $donnees['ville'],
        ':pays'            => $donnees['pays'],
        ':role_id'         => ROLE_CLIENT,           // toujours Client à l'inscription
    ]);

    return (int) $pdo->lastInsertId();
}

/**
 * Récupère un utilisateur par son email.
 * Retourne le tableau de données ou null si introuvable.
 */
function utilisateur_par_email(string $email): ?array
{
    $pdo  = getPDO();
    $stmt = $pdo->prepare('SELECT * FROM utilisateur WHERE email = ? AND actif = 1');
    $stmt->execute([$email]);
    $result = $stmt->fetch();
    return $result ?: null;
}

/**
 * Récupère un utilisateur par son ID.
 */
function utilisateur_par_id(int $id): ?array
{
    $pdo  = getPDO();
    $stmt = $pdo->prepare('SELECT * FROM utilisateur WHERE utilisateur_id = ?');
    $stmt->execute([$id]);
    $result = $stmt->fetch();
    return $result ?: null;
}

/**
 * Met à jour le mot de passe d'un utilisateur.
 */
function utilisateur_maj_password(int $utilisateur_id, string $hash): void
{
    $pdo = getPDO();
    $pdo->prepare('UPDATE utilisateur SET password = ? WHERE utilisateur_id = ?')
        ->execute([$hash, $utilisateur_id]);
}
