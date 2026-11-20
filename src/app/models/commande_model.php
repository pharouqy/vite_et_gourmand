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
        bin2hex(random_bytes(4)),
        0,
        6
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
        ':numero' => $numero,
        ':date_prestation' => $donnees['date_prestation'],
        ':heure_livraison' => $donnees['heure_livraison'],
        ':prix_menu' => $donnees['prix_menu'],
        ':nombre_personne' => $donnees['nombre_personne'],
        ':prix_livraison' => $donnees['prix_livraison'],
        ':menu_id' => $donnees['menu_id'],
        ':utilisateur_id' => $donnees['utilisateur_id'],
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
    $pdo = getPDO();
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
    $pdo = getPDO();
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
    if (!$commande)
        return null;

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
 * 0 € si ville = Bordeaux, sinon forfait + km.
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
    int $nb_personnes,
    int $nb_minimum
): array {
    $remise_appliquee = false;
    $taux_remise = 0.0;

    // Remise 10% si nb_personnes >= minimum + 5
    if ($nb_personnes >= ($nb_minimum + REMISE_SEUIL_PERSONNES)) {
        $remise_appliquee = true;
        $taux_remise = REMISE_TAUX;
    }

    $prix_brut = $prix_par_personne * $nb_personnes;
    $montant_remise = $remise_appliquee ? $prix_brut * $taux_remise : 0;
    $prix_net = $prix_brut - $montant_remise;

    return [
        'prix_brut' => $prix_brut,
        'remise_appliquee' => $remise_appliquee,
        'montant_remise' => $montant_remise,
        'prix_net' => $prix_net,
        'taux_remise' => $taux_remise * 100,
    ];
}

/**
 * Change le statut d'une commande et insère dans l'historique.
 */
function commande_changer_statut(
    string $numero,
    string $statut,
    ?string $commentaire = null
): void {
    $pdo = getPDO();
    $pdo->prepare("
        UPDATE commande SET statut = ? WHERE numero_commande = ?
    ")->execute([$statut, $numero]);

    historique_statut_ajouter($numero, $statut, $commentaire);
}

/**
 * Modifie la date et l'heure d'une commande.
 */
function commande_modifier_dates(
    string $numero,
    string $date,
    string $heure
): void {
    getPDO()->prepare("
        UPDATE commande
        SET date_prestation = ?, heure_livraison = ?
        WHERE numero_commande = ?
    ")->execute([$date, $heure, $numero]);
}

/**
 * Liste des commandes pour l'espace employé, avec filtres optionnels.
 * $filtres['statut'] : un statut exact (ou vide = tous)
 * $filtres['client'] : texte recherché dans nom, prénom ou email
 */
function commandes_filtrees(array $filtres = []): array
{
    $where = ['1=1'];
    $params = [];

    if (!empty($filtres['statut'])) {
        $where[] = 'c.statut = :statut';
        $params[':statut'] = $filtres['statut'];
    }

    if (!empty($filtres['client'])) {
        $where[] = '(u.nom LIKE :client_nom
                                OR u.prenom LIKE :client_prenom
                                OR u.email LIKE :client_email)';
        $motif = '%' . $filtres['client'] . '%';
        $params[':client_nom'] = $motif;
        $params[':client_prenom'] = $motif;
        $params[':client_email'] = $motif;
    }

    $sql = "
        SELECT
            c.numero_commande,
            c.date_commande,
            c.date_prestation,
            c.heure_livraison,
            c.prix_menu,
            c.prix_livraison,
            c.nombre_personne,
            c.statut,
            m.titre  AS menu_titre,
            u.nom    AS client_nom,
            u.prenom AS client_prenom,
            u.email  AS client_email
        FROM commande c
        JOIN menu        m ON m.menu_id        = c.menu_id
        JOIN utilisateur u ON u.utilisateur_id = c.utilisateur_id
        WHERE " . implode(' AND ', $where) . "
        ORDER BY c.date_commande DESC
    ";

    $stmt = getPDO()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Détail d'une commande pour l'employé : commande + client + historique.
 * Contrairement à commande_par_numero(), on joint ici les coordonnées
 * du client (l'employé doit pouvoir l'appeler ou lui écrire).
 */
function commande_detail_employe(string $numero): ?array
{
    $pdo = getPDO();
    $stmt = $pdo->prepare("
        SELECT
            c.*,
            m.titre  AS menu_titre,
            u.nom    AS client_nom,
            u.prenom AS client_prenom,
            u.email  AS client_email,
            u.telephone AS client_telephone
        FROM commande c
        JOIN menu        m ON m.menu_id        = c.menu_id
        JOIN utilisateur u ON u.utilisateur_id = c.utilisateur_id
        WHERE c.numero_commande = ?
    ");
    $stmt->execute([$numero]);
    $commande = $stmt->fetch();

    if (!$commande) {
        return null;
    }

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
 * Annulation par un employé : statut + mode de contact + motif,
 * puis ligne d'historique. Le tout dans une transaction :
 * soit les deux écritures réussissent, soit aucune.
 */
function commande_annuler_employe(
    string $numero,
    string $mode_contact,
    string $motif
): void {
    $pdo = getPDO();
    $pdo->beginTransaction();

    try {
        $pdo->prepare("
            UPDATE commande
            SET statut                  = ?,
                mode_contact_annulation = ?,
                motif_annulation        = ?
            WHERE numero_commande = ?
        ")->execute([STATUT_ANNULEE, $mode_contact, $motif, $numero]);

        historique_statut_ajouter(
            $numero,
            STATUT_ANNULEE,
            "Annulée par l'équipe — client contacté par {$mode_contact}."
        );

        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
}

/**
 * Indique si du matériel est prêté pour cette commande.
 */
function commande_maj_materiel(string $numero, int $pret): void
{
    getPDO()->prepare("
        UPDATE commande SET pret_materiel = ? WHERE numero_commande = ?
    ")->execute([$pret, $numero]);
}

/**
 * Marque le matériel comme restitué.
 */
function commande_marquer_restitution(string $numero): void
{
    getPDO()->prepare("
        UPDATE commande SET restitution_materiel = 1 WHERE numero_commande = ?
    ")->execute([$numero]);
}