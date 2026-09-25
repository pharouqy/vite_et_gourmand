<?php

declare(strict_types=1);

/**
 * Insère une nouvelle commande en base.
 * Retourne le numéro de commande généré.
 */
function commande_creer(array $donnees): string
{
    $pdo = getPDO();

    // Génère un numéro unique : CMD-YYYY-XXXX
    $numero = 'CMD-' . date('Y') . '-' . strtoupper(substr(
        bin2hex(random_bytes(4)), 0, 6
    ));

    $stmt = $pdo->prepare("
        INSERT INTO commande (
            numero_commande, date_commande, date_prestation,
            heure_livraison, prix_menu, nombre_personne,
            prix_livraison, statut, pret_materiel,
            restitution_materiel, menu_id, utilisateur_id
        ) VALUES (
            :numero, NOW(), :date_prestation,
            :heure_livraison, :prix_menu, :nombre_personne,
            :prix_livraison, 'en attente', 0,
            0, :menu_id, :utilisateur_id
        )
    ");

    $stmt->execute([
        ':numero'           => $numero,
        ':date_prestation'  => $donnees['date_prestation'],
        ':heure_livraison'  => $donnees['heure_livraison'],
        ':prix_menu'        => $donnees['prix_menu'],
        ':nombre_personne'  => $donnees['nombre_personne'],
        ':prix_livraison'   => $donnees['prix_livraison'],
        ':menu_id'          => $donnees['menu_id'],
        ':utilisateur_id'   => $donnees['utilisateur_id'],
    ]);

    // Insérer le premier statut dans l'historique
    historique_statut_ajouter($numero, STATUT_EN_ATTENTE);

    return $numero;
}

/**
 * Ajoute une entrée dans l'historique des statuts.
 */
function historique_statut_ajouter(
    string $numero_commande,
    string $statut,
    ?string $commentaire = null
): void {
    $pdo = getPDO();
    $pdo->prepare("
        INSERT INTO historique_statut_commande
            (numero_commande, statut, commentaire)
        VALUES (?, ?, ?)
    ")->execute([$numero_commande, $statut, $commentaire]);
}

/**
 * Récupère toutes les commandes d'un utilisateur.
 */
function commandes_par_utilisateur(int $utilisateur_id): array
{
    $pdo  = getPDO();
    $stmt = $pdo->prepare("
        SELECT c.*, m.titre AS menu_titre
        FROM commande c
        JOIN menu m ON m.menu_id = c.menu_id
        WHERE c.utilisateur_id = ?
        ORDER BY c.date_commande DESC
    ");
    $stmt->execute([$utilisateur_id]);
    return $stmt->fetchAll();
}

/**
 * Récupère une commande par son numéro.
 */
function commande_par_numero(string $numero): ?array
{
    $pdo  = getPDO();
    $stmt = $pdo->prepare("
        SELECT c.*, m.titre AS menu_titre,
               m.nombre_personne_minimum,
               m.prix_par_personne
        FROM commande c
        JOIN menu m ON m.menu_id = c.menu_id
        WHERE c.numero_commande = ?
    ");
    $stmt->execute([$numero]);
    $commande = $stmt->fetch();
    if (!$commande) return null;

    // Historique des statuts
    $stmt2 = $pdo->prepare("
        SELECT * FROM historique_statut_commande
        WHERE numero_commande = ?
        ORDER BY created_at ASC
    ");
    $stmt2->execute([$numero]);
    $commande['historique'] = $stmt2->fetchAll();

    return $commande;
}

/**
 * Calcule les frais de livraison.
 * 0 DA si ville = Bordeaux, sinon forfait + km.
 * Pour simplifier : on se base sur la ville saisie.
 */
function calculer_frais_livraison(string $ville): float
{
    $ville_normalisee = strtolower(trim($ville));

    if ($ville_normalisee === strtolower(VILLE_REFERENCE)) {
        return 0.0;
    }

    // Distance approximative fixe hors Bordeaux
    // En production : appel API de géocodage
    // Pour l'ECF : forfait fixe hors ville
    return FRAIS_LIVRAISON_BASE + (50 * FRAIS_LIVRAISON_KM);
}

/**
 * Calcule le prix total du menu avec remise éventuelle.
 */
function calculer_prix_menu(
    float $prix_par_personne,
    int   $nb_personnes,
    int   $nb_minimum
): array {
    $remise_appliquee = false;
    $taux_remise      = 0.0;

    // Remise 10% si nb_personnes >= minimum + 5
    if ($nb_personnes >= ($nb_minimum + REMISE_SEUIL_PERSONNES)) {
        $remise_appliquee = true;
        $taux_remise      = REMISE_TAUX;
    }

    $prix_brut   = $prix_par_personne * $nb_personnes;
    $montant_remise = $remise_appliquee ? $prix_brut * $taux_remise : 0;
    $prix_net    = $prix_brut - $montant_remise;

    return [
        'prix_brut'        => $prix_brut,
        'remise_appliquee' => $remise_appliquee,
        'montant_remise'   => $montant_remise,
        'prix_net'         => $prix_net,
        'taux_remise'      => $taux_remise * 100,
    ];
}