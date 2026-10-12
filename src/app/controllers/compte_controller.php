<?php

declare(strict_types=1);

// ══════════════════════════════════════════════════════
// LISTE DES COMMANDES
// ══════════════════════════════════════════════════════

function compte_mes_commandes(): void
{
    require_auth();
    require_role(ROLE_CLIENT);

    $commandes = commandes_par_utilisateur(
        (int) $_SESSION['utilisateur_id']
    );

    render('compte/mes-commandes', [
        'titre_page' => 'Mes commandes',
        'commandes' => $commandes,
    ]);
}

// ══════════════════════════════════════════════════════
// DÉTAIL D'UNE COMMANDE
// ══════════════════════════════════════════════════════

function compte_commande_detail(): void
{
    require_auth();
    require_role(ROLE_CLIENT);

    $numero = get_param('numero', '');
    $commande = commande_par_numero($numero);

    // Vérifie que la commande appartient bien à ce client
    if (
        !$commande ||
        (int) $commande['utilisateur_id'] !== (int) $_SESSION['utilisateur_id']
    ) {
        redirect('/compte/commandes');
    }

    // Charger l'avis lié à cette commande s'il existe
    $commande['avis_id'] = avis_par_commande($numero);

    render('compte/commande-detail', [
        'titre_page' => 'Commande ' . $numero,
        'commande' => $commande,
    ]);
}

// ══════════════════════════════════════════════════════
// ANNULER UNE COMMANDE
// ══════════════════════════════════════════════════════

function compte_annuler(): void
{
    require_auth();
    require_role(ROLE_CLIENT);
    csrf_verifier();

    $numero = post_param('numero', '');
    $commande = commande_par_numero($numero);

    if (
        !$commande ||
        (int) $commande['utilisateur_id'] !== (int) $_SESSION['utilisateur_id']
    ) {
        redirect('/compte/commandes');
    }

    // Annulation possible seulement si statut "en attente"
    if ($commande['statut'] !== STATUT_EN_ATTENTE) {
        $_SESSION['flash'] = [
            'type' => 'danger',
            'message' => 'Cette commande ne peut plus être annulée '
                . '(déjà prise en charge par notre équipe).',
        ];
        redirect('/compte/commande?numero=' . urlencode($numero));
    }

    commande_changer_statut($numero, STATUT_ANNULEE, 'Annulée par le client.');

    $_SESSION['flash'] = [
        'type' => 'success',
        'message' => "Commande {$numero} annulée avec succès.",
    ];
    redirect('/compte/commandes');
}

// ══════════════════════════════════════════════════════
// MODIFIER UNE COMMANDE
// ══════════════════════════════════════════════════════

function compte_modifier_form(): void
{
    require_auth();
    require_role(ROLE_CLIENT);

    $numero = get_param('numero', '');
    $commande = commande_par_numero($numero);

    if (
        !$commande ||
        (int) $commande['utilisateur_id'] !== (int) $_SESSION['utilisateur_id'] ||
        $commande['statut'] !== STATUT_EN_ATTENTE
    ) {
        $_SESSION['flash'] = [
            'type' => 'danger',
            'message' => 'Modification impossible pour cette commande.',
        ];
        redirect('/compte/commandes');
    }

    render('compte/modifier-commande', [
        'titre_page' => 'Modifier la commande',
        'commande' => $commande,
    ]);
}

function compte_modifier_traiter(): void
{
    require_auth();
    require_role(ROLE_CLIENT);
    csrf_verifier();

    $numero = post_param('numero', '');
    $date_prestation = post_param('date_prestation', '');
    $heure_livraison = post_param('heure_livraison', '');

    $commande = commande_par_numero($numero);

    if (
        !$commande ||
        (int) $commande['utilisateur_id'] !== (int) $_SESSION['utilisateur_id'] ||
        $commande['statut'] !== STATUT_EN_ATTENTE
    ) {
        redirect('/compte/commandes');
    }

    commande_modifier_dates($numero, $date_prestation, $heure_livraison);

    $_SESSION['flash'] = [
        'type' => 'success',
        'message' => 'Commande modifiée avec succès.',
    ];
    redirect('/compte/commande?numero=' . urlencode($numero));
}

// ══════════════════════════════════════════════════════
// PROFIL
// ══════════════════════════════════════════════════════

function compte_profil(): void
{
    require_auth();
    require_role(ROLE_CLIENT);

    $user = utilisateur_par_id((int) $_SESSION['utilisateur_id']);

    render('compte/profil', [
        'titre_page' => 'Mon profil',
        'user' => $user,
    ]);
}

function compte_profil_traiter(): void
{
    require_auth();
    require_role(ROLE_CLIENT);
    csrf_verifier();

    $donnees = [
        'nom' => post_param('nom', ''),
        'prenom' => post_param('prenom', ''),
        'telephone' => post_param('telephone', ''),
        'adresse_postale' => post_param('adresse_postale', ''),
        'ville' => post_param('ville', ''),
        'pays' => post_param('pays', ''),
    ];

    $erreurs = [];
    if (strlen($donnees['nom']) < 2)
        $erreurs[] = 'Nom invalide.';
    if (strlen($donnees['prenom']) < 2)
        $erreurs[] = 'Prénom invalide.';
    if (strlen($donnees['telephone']) < 8) {
        $erreurs[] = 'Téléphone invalide.';
    }

    if (!empty($erreurs)) {
        $_SESSION['flash'] = [
            'type' => 'danger',
            'message' => implode('<br>', $erreurs),
        ];
        redirect('/compte/profil');
    }

    utilisateur_modifier_profil(
        (int) $_SESSION['utilisateur_id'],
        $donnees
    );

    // Mettre à jour la session
    $_SESSION['nom'] = $donnees['nom'];
    $_SESSION['prenom'] = $donnees['prenom'];

    $_SESSION['flash'] = [
        'type' => 'success',
        'message' => 'Profil mis à jour avec succès.',
    ];
    redirect('/compte/profil');
}

// ══════════════════════════════════════════════════════
// AVIS (US-3.7)
// ══════════════════════════════════════════════════════

function compte_avis_form(): void
{
    require_auth();
    require_role(ROLE_CLIENT);

    $numero = get_param('numero', '');
    $commande = commande_par_numero($numero);

    // Vérifications : commande terminée + appartient au client
    if (
        !$commande ||
        (int) $commande['utilisateur_id'] !== (int) $_SESSION['utilisateur_id'] ||
        $commande['statut'] !== STATUT_TERMINEE
    ) {
        redirect('/compte/commandes');
    }

    render('compte/deposer-avis', [
        'titre_page' => 'Laisser un avis',
        'numero_commande' => $numero,
    ]);
}

function compte_avis_traiter(): void
{
    require_auth();
    require_role(ROLE_CLIENT);
    csrf_verifier();

    $numero = post_param('numero', '');
    $note = (int) post_param('note', 0);
    $description = post_param('description', '');

    $commande = commande_par_numero($numero);

    if (
        !$commande ||
        (int) $commande['utilisateur_id'] !== (int) $_SESSION['utilisateur_id'] ||
        $commande['statut'] !== STATUT_TERMINEE
    ) {
        redirect('/compte/commandes');
    }

    if ($note < 1 || $note > 5) {
        $_SESSION['flash'] = [
            'type' => 'danger',
            'message' => 'Note invalide (1 à 5).',
        ];
        redirect('/compte/avis?numero=' . urlencode($numero));
    }

    if (strlen($description) < 10) {
        $_SESSION['flash'] = [
            'type' => 'danger',
            'message' => 'Le commentaire doit faire au moins 10 caractères.',
        ];
        redirect('/compte/avis?numero=' . urlencode($numero));
    }

    avis_deposer(
        $note,
        $description,
        $numero,
        (int) $_SESSION['utilisateur_id']
    );

    $_SESSION['flash'] = [
        'type' => 'success',
        'message' => 'Merci pour votre avis ! '
            . 'Il sera publié après validation par notre équipe.',
    ];
    redirect('/compte/commandes');
}