<?php

declare(strict_types=1);

// ══════════════════════════════════════════════════════
// ÉTAPE 1 — Informations de prestation
// ══════════════════════════════════════════════════════

function commande_prestation_form(): void
{
    require_auth();
    require_role(ROLE_CLIENT);

    // Si menu_id passé en GET (depuis bouton "Commander"),
    // on le pré-stocke en session pour l'étape 2
    $menu_id = (int) get_param('menu_id', 0);
    if ($menu_id > 0) {
        $_SESSION['commande']['menu_id'] = $menu_id;
    }

    render('commande/informations-prestation', [
        'titre_page' => 'Commander — Étape 1',
    ]);
}

function commande_prestation_traiter(): void
{
    require_auth();
    require_role(ROLE_CLIENT);
    csrf_verifier();

    $adresse = post_param('adresse_livraison', '');
    $ville = post_param('ville_livraison', '');
    $date = post_param('date_prestation', '');
    $heure = post_param('heure_livraison', '');

    // ── Validation ────────────────────────────────────────────────
    $erreurs = [];

    if (strlen($adresse) < 5) {
        $erreurs[] = 'Adresse invalide.';
    }
    if (strlen($ville) < 2) {
        $erreurs[] = 'Ville invalide.';
    }
    if (empty($date) || $date < date('Y-m-d', strtotime('+3 days'))) {
        $erreurs[] = 'La date doit être au minimum dans 3 jours.';
    }
    if (empty($heure)) {
        $erreurs[] = 'L\'heure de livraison est obligatoire.';
    }

    if (!empty($erreurs)) {
        $_SESSION['flash'] = [
            'type' => 'danger',
            'message' => implode('<br>', $erreurs),
        ];
        redirect('/commande/prestation');
    }

    // ── Calcul des frais de livraison ─────────────────────────────
    $frais_livraison = calculer_frais_livraison($ville);

    // ── Stockage en session ───────────────────────────────────────
    $_SESSION['commande'] = array_merge(
        $_SESSION['commande'] ?? [],
        [
            'adresse_livraison' => $adresse,
            'ville_livraison' => $ville,
            'date_prestation' => $date,
            'heure_livraison' => $heure,
            'prix_livraison' => $frais_livraison,
        ]
    );

    redirect('/commande/menu');
}

// ══════════════════════════════════════════════════════
// ÉTAPE 2 — Choix du menu (US-3.2 — squelette)
// ══════════════════════════════════════════════════════

function commande_menu_form(): void
{
    require_auth();
    require_role(ROLE_CLIENT);

    // Vérifier que l'étape 1 a été complétée
    if (empty($_SESSION['commande']['date_prestation'])) {
        redirect('/commande/prestation');
    }

    $menus = menus_liste();
    $menu_id = (int) ($_SESSION['commande']['menu_id'] ?? 0);

    render('commande/choix-menu', [
        'titre_page' => 'Commander — Étape 2',
        'menus' => $menus,
        'menu_id' => $menu_id,
    ]);
}

function commande_menu_traiter(): void
{
    require_auth();
    require_role(ROLE_CLIENT);
    csrf_verifier();

    $menu_id = (int) post_param('menu_id', 0);
    $nb_personnes = (int) post_param('nombre_personne', 0);

    if (!$menu_id) {
        $_SESSION['flash'] = [
            'type' => 'danger',
            'message' => 'Veuillez sélectionner un menu.',
        ];
        redirect('/commande/menu');
    }

    $menu = menu_par_id($menu_id);

    if (!$menu) {
        redirect('/commande/menu');
    }

    // Vérification du nombre minimum
    if ($nb_personnes < (int) $menu['nombre_personne_minimum']) {
        $_SESSION['flash'] = [
            'type' => 'danger',
            'message' => 'Le nombre minimum de personnes pour ce menu est '
                . $menu['nombre_personne_minimum'] . '.',
        ];
        redirect('/commande/menu');
    }

    // Calcul du prix avec remise éventuelle
    $prix = calculer_prix_menu(
        (float) $menu['prix_par_personne'],
        $nb_personnes,
        (int) $menu['nombre_personne_minimum']
    );

    $_SESSION['commande'] = array_merge(
        $_SESSION['commande'] ?? [],
        [
            'menu_id' => $menu_id,
            'menu_titre' => $menu['titre'],
            'nombre_personne' => $nb_personnes,
            'prix_par_personne' => (float) $menu['prix_par_personne'],
            'prix_menu' => $prix['prix_net'],
            'prix_brut' => $prix['prix_brut'],
            'remise_appliquee' => $prix['remise_appliquee'],
            'montant_remise' => $prix['montant_remise'],
            'taux_remise' => $prix['taux_remise'],
        ]
    );

    redirect('/commande/recapitulatif');
}

// ══════════════════════════════════════════════════════
// ÉTAPE 3 — Récapitulatif (US-3.3 — squelette)
// ══════════════════════════════════════════════════════

function commande_recap(): void
{
    require_auth();
    require_role(ROLE_CLIENT);

    if (empty($_SESSION['commande']['menu_id'])) {
        redirect('/commande/prestation');
    }

    render('commande/recapitulatif', [
        'titre_page' => 'Commander — Étape 3',
        'commande' => $_SESSION['commande'],
    ]);
}

function commande_confirmer(): void
{
    require_auth();
    require_role(ROLE_CLIENT);
    csrf_verifier();

    if (empty($_SESSION['commande']['menu_id'])) {
        redirect('/commande/prestation');
    }

    $c = $_SESSION['commande'];

    // ── Insertion en base ─────────────────────────────────────────
    $numero = commande_creer([
        'date_prestation' => $c['date_prestation'],
        'heure_livraison' => $c['heure_livraison'],
        'prix_menu' => $c['prix_menu'],
        'nombre_personne' => $c['nombre_personne'],
        'prix_livraison' => $c['prix_livraison'],
        'menu_id' => $c['menu_id'],
        'utilisateur_id' => $_SESSION['utilisateur_id'],
    ]);

    // ── Envoi du mail de confirmation ─────────────────────────────
    commande_envoyer_mail_confirmation($numero, $c);

    // ── Nettoyage session ─────────────────────────────────────────
    unset($_SESSION['commande']);

    $_SESSION['flash'] = [
        'type' => 'success',
        'message' => "Commande {$numero} confirmée ! "
            . "Un email de confirmation vous a été envoyé.",
    ];

    redirect('/compte/commandes');
}

/**
 * Envoie le mail de confirmation de commande.
 */
function commande_envoyer_mail_confirmation(
    string $numero,
    array $c
): void {
    try {
        $utilisateur = utilisateur_par_id(
            (int) $_SESSION['utilisateur_id']
        );

        $mail = creer_mailer();
        $mail->addAddress(
            $utilisateur['email'],
            $utilisateur['prenom'] . ' ' . $utilisateur['nom']
        );
        $mail->isHTML(true);
        $mail->Subject = "Confirmation commande {$numero} — Vite & Gourmand";

        // Variables pour le template
        $prenom = $utilisateur['prenom'];
        $date_prestation = $c['date_prestation'];
        $heure = $c['heure_livraison'];
        $adresse = $c['adresse_livraison'];
        $ville = $c['ville_livraison'];
        $menu_titre = $c['menu_titre'];
        $nb_personnes = $c['nombre_personne'];
        $prix_menu = prix_format((float) $c['prix_menu']);
        $prix_livraison = prix_format((float) $c['prix_livraison']);
        $total = prix_format(
            (float) $c['prix_menu'] + (float) $c['prix_livraison']
        );
        $remise_appliquee = $c['remise_appliquee'] ?? false;
        $montant_remise = prix_format((float) ($c['montant_remise'] ?? 0));

        ob_start();
        require dirname(__DIR__) . '/mail_templates/confirmation_commande.php';
        $mail->Body = ob_get_clean();

        $mail->AltBody =
            "Bonjour {$prenom},\n\n"
            . "Votre commande {$numero} est confirmée.\n"
            . "Menu : {$menu_titre}\n"
            . "Date : " . date('d/m/Y', strtotime($date_prestation)) . "\n"
            . "Total : {$total}";

        $mail->send();

    } catch (Exception $e) {
        error_log('[Mail confirmation commande] Échec : ' . $e->getMessage());
    }
}