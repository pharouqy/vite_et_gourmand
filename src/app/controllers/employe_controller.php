<?php

declare(strict_types=1);

function employe_commandes_liste(): void
{
    require_employe_ou_admin();

    $statut = get_param('statut', '');
    $client = get_param('client', '');

    // Liste blanche : un statut inconnu dans l'URL est ignoré
    if (!array_key_exists($statut, TRANSITIONS_STATUT)) {
        $statut = '';
    }

    render('employe/commandes-liste', [
        'titre_page' => 'Commandes',
        'commandes' => commandes_filtrees([
            'statut' => $statut,
            'client' => $client,
        ]),
        'statuts' => array_keys(TRANSITIONS_STATUT),
        'filtre_statut' => $statut,
        'filtre_client' => $client,
    ]);
}

// Squelettes Sprint 4
function employe_menus_liste(): void
{
    require_employe_ou_admin();

    render('employe/menus-liste', [
        'titre_page' => 'Gestion des menus',
        'menus' => menus_liste(),
    ]);
}
// ══════════════════════════════════════════════════════
// MENUS — création et modification
// ══════════════════════════════════════════════════════

function employe_menu_creer_form(): void
{
    require_employe_ou_admin();

    $saisie = _menu_saisie_precedente();

    render('employe/menu-form', [
        'titre_page' => 'Nouveau menu',
        'saisie' => $saisie,
        'regimes' => regimes_tous(),
        'themes' => themes_tous(),
        'plats' => plats_tous(),
        'plats_du_menu' => $saisie !== null ? _menu_ids($saisie['plats'] ?? []) : [],
    ]);
}

function employe_menu_creer_traiter(): void
{
    require_employe_ou_admin();
    csrf_verifier();

    $donnees = _menu_extraire_donnees();
    $erreurs = _menu_valider($donnees);

    if (!empty($erreurs)) {
        _menu_garder_saisie();
        _employe_flash_redirect('danger', implode(' ', $erreurs), '/employe/menus/creer');
    }

    $menu_id = menu_creer($donnees);
    menu_maj_plats($menu_id, _menu_ids($_POST['plats'] ?? []));

    _employe_flash_redirect('success', 'Menu créé.', '/employe/menus');
}

function employe_menu_modifier_form(): void
{
    require_employe_ou_admin();

    $menu = menu_par_id((int) get_param('id', 0));

    if (!$menu) {
        _employe_flash_redirect('danger', 'Menu introuvable.', '/employe/menus');
    }

    $saisie = _menu_saisie_precedente();

    render('employe/menu-form', [
        'titre_page' => 'Modifier le menu',
        'menu' => $menu,
        'saisie' => $saisie,
        'regimes' => regimes_tous(),
        'themes' => themes_tous(),
        'plats' => plats_tous(),
        'plats_du_menu' => $saisie !== null
            ? _menu_ids($saisie['plats'] ?? [])
            : array_map('intval', array_column($menu['plats'], 'plat_id')),
    ]);
}

function employe_menu_modifier_traiter(): void
{
    require_employe_ou_admin();
    csrf_verifier();

    $menu_id = (int) post_param('menu_id', 0);

    if (!menu_par_id($menu_id)) {
        _employe_flash_redirect('danger', 'Menu introuvable.', '/employe/menus');
    }

    $donnees = _menu_extraire_donnees();
    $erreurs = _menu_valider($donnees);

    if (!empty($erreurs)) {
        _menu_garder_saisie();
        _employe_flash_redirect(
            'danger',
            implode(' ', $erreurs),
            '/employe/menus/modifier?id=' . $menu_id
        );
    }

    menu_modifier($menu_id, $donnees);
    menu_maj_plats($menu_id, _menu_ids($_POST['plats'] ?? []));

    _employe_flash_redirect('success', 'Menu modifié.', '/employe/menus');
}

// ── Fonctions privées des menus ────────────────────────────────────

/**
 * Lit les champs du formulaire et les convertit dans le bon type.
 */
function _menu_extraire_donnees(): array
{
    $regime = post_param('regime_id', '');
    $theme = post_param('theme_id', '');

    return [
        'titre' => post_param('titre', ''),
        'description' => post_param('description', ''),
        'prix_par_personne' => (float) post_param('prix_par_personne', 0),
        'nombre_personne_minimum' => (int) post_param('nombre_personne_minimum', 0),
        'quantite_restante' => (int) post_param('quantite_restante', 0),
        'regime_id' => $regime === '' ? null : (int) $regime,
        'theme_id' => $theme === '' ? null : (int) $theme,
    ];
}

/**
 * Retourne la liste des messages d'erreur (vide si tout est valide).
 */
function _menu_valider(array $d): array
{
    $erreurs = [];

    if (mb_strlen($d['titre']) < 3 || mb_strlen($d['titre']) > 150) {
        $erreurs[] = 'Le titre doit contenir entre 3 et 150 caractères.';
    }
    if ($d['prix_par_personne'] <= 0) {
        $erreurs[] = 'Le prix par personne doit être supérieur à 0.';
    }
    if ($d['nombre_personne_minimum'] < 1) {
        $erreurs[] = 'Le nombre minimum de personnes doit être au moins 1.';
    }
    if ($d['quantite_restante'] < 0) {
        $erreurs[] = 'La disponibilité ne peut pas être négative.';
    }

    // Le régime et le thème doivent exister (sinon : erreur de clé étrangère)
    $regimes = array_map('intval', array_column(regimes_tous(), 'regime_id'));
    if ($d['regime_id'] !== null && !in_array($d['regime_id'], $regimes, true)) {
        $erreurs[] = 'Régime alimentaire invalide.';
    }

    $themes = array_map('intval', array_column(themes_tous(), 'theme_id'));
    if ($d['theme_id'] !== null && !in_array($d['theme_id'], $themes, true)) {
        $erreurs[] = 'Thème invalide.';
    }

    return $erreurs;
}

/**
 * Convertit n'importe quelle valeur en liste d'entiers
 * (protège contre un champ "plats" falsifié qui ne serait pas un tableau).
 */
function _menu_ids(mixed $valeur): array
{
    return is_array($valeur) ? array_map('intval', $valeur) : [];
}

/**
 * Garde la saisie en session pour la réafficher après une erreur.
 */
function _menu_garder_saisie(): void
{
    $saisie = $_POST;
    unset($saisie['csrf_token']);
    $_SESSION['menu_saisie'] = $saisie;
}

/**
 * Récupère (et efface) la saisie gardée après une erreur.
 */
function _menu_saisie_precedente(): ?array
{
    $saisie = $_SESSION['menu_saisie'] ?? null;
    unset($_SESSION['menu_saisie']);
    return $saisie;
}
function employe_menu_supprimer(): void
{
    require_employe_ou_admin();
    csrf_verifier();

    $menu_id = (int) post_param('menu_id', 0);
    $menu = menu_par_id($menu_id);

    if (!$menu) {
        _employe_flash_redirect('danger', 'Menu introuvable.', '/employe/menus');
    }

    if (menu_supprimer($menu_id)) {
        _employe_flash_redirect(
            'success',
            "Menu « {$menu['titre']} » supprimé.",
            '/employe/menus'
        );
    }

    // La base a refusé : des commandes sont liées à ce menu
    _employe_flash_redirect(
        'danger',
        "Le menu « {$menu['titre']} » a déjà été commandé : il ne peut pas être supprimé. "
        . 'Pour ne plus le proposer, mettez sa disponibilité à 0.',
        '/employe/menus'
    );
}
// ══════════════════════════════════════════════════════
// PLATS
// ══════════════════════════════════════════════════════

function employe_plats_liste(): void
{
    require_employe_ou_admin();

    // ?id=5 : le formulaire passe en mode modification
    $plat_edition = null;
    $edition_id = (int) get_param('id', 0);

    if ($edition_id > 0) {
        $plat_edition = plat_par_id($edition_id);

        if (!$plat_edition) {
            _employe_flash_redirect('danger', 'Plat introuvable.', '/employe/plats');
        }
    }

    render('employe/plats-liste', [
        'titre_page' => 'Gestion des plats',
        'plats' => plats_liste_detail(),
        'allergenes' => allergenes_tous(),
        'plat_edition' => $plat_edition,
        'allergenes_plat' => $plat_edition
            ? allergenes_du_plat((int) $plat_edition['plat_id'])
            : [],
    ]);
}

function employe_plat_enregistrer(): void
{
    require_employe_ou_admin();
    csrf_verifier();

    $plat_id = (int) post_param('plat_id', 0);
    $titre = post_param('titre_plat', '');
    $retour = $plat_id > 0 ? '/employe/plats?id=' . $plat_id : '/employe/plats';

    $plat = $plat_id > 0 ? plat_par_id($plat_id) : null;

    if ($plat_id > 0 && !$plat) {
        _employe_flash_redirect('danger', 'Plat introuvable.', '/employe/plats');
    }

    if (mb_strlen($titre) < 2 || mb_strlen($titre) > 150) {
        _employe_flash_redirect(
            'danger',
            'Le nom du plat doit contenir entre 2 et 150 caractères.',
            $retour
        );
    }

    // La photo est vérifiée AVANT toute écriture en base
    $nouvelle_photo = null;

    if (_plat_photo_envoyee()) {
        $resultat = _plat_enregistrer_photo();

        if (isset($resultat['erreur'])) {
            _employe_flash_redirect('danger', $resultat['erreur'], $retour);
        }
        $nouvelle_photo = $resultat['chemin'];
    }

    // Écritures en base
    if ($plat) {
        plat_modifier($plat_id, $titre);
    } else {
        $plat_id = plat_creer($titre, null);
    }

    plat_maj_allergenes($plat_id, _menu_ids($_POST['allergenes'] ?? []));

    if ($nouvelle_photo !== null) {
        plat_maj_photo($plat_id, $nouvelle_photo);
        _plat_supprimer_fichier($plat['photo'] ?? null);   // l'ancienne photo
    } elseif ($plat && post_param('supprimer_photo') === '1') {
        plat_maj_photo($plat_id, null);
        _plat_supprimer_fichier($plat['photo']);
    }

    _employe_flash_redirect(
        'success',
        $plat ? 'Plat modifié.' : 'Plat créé.',
        '/employe/plats'
    );
}

function employe_plat_supprimer(): void
{
    require_employe_ou_admin();
    csrf_verifier();

    $plat = plat_par_id((int) post_param('plat_id', 0));

    if (!$plat) {
        _employe_flash_redirect('danger', 'Plat introuvable.', '/employe/plats');
    }

    if (plat_supprimer((int) $plat['plat_id'])) {
        _plat_supprimer_fichier($plat['photo']);
        _employe_flash_redirect(
            'success',
            "Plat « {$plat['titre_plat']} » supprimé.",
            '/employe/plats'
        );
    }

    // La base a refusé : un menu contient ce plat
    _employe_flash_redirect(
        'danger',
        "Le plat « {$plat['titre_plat']} » est utilisé dans un ou plusieurs menus : "
        . 'retirez-le d\'abord de ces menus.',
        '/employe/plats'
    );
}

// ── Fonctions privées des plats ────────────────────────────────────

/**
 * Dossier physique où sont rangées les photos.
 * __DIR__ = src/app/controllers, donc dirname(__DIR__, 2) = src.
 */
function _plat_dossier_upload(): string
{
    return dirname(__DIR__, 2) . '/public/uploads/menus';
}

/**
 * Un fichier a-t-il été choisi dans le formulaire ?
 */
function _plat_photo_envoyee(): bool
{
    return isset($_FILES['photo'])
        && ($_FILES['photo']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
}

/**
 * Contrôle puis range la photo envoyée.
 * Retourne ['chemin' => '/uploads/menus/xxx.jpg'] ou ['erreur' => 'message'].
 */
function _plat_enregistrer_photo(): array
{
    $f = $_FILES['photo'];

    // "photo[]" falsifié : error devient un tableau
    if (!isset($f['error']) || is_array($f['error'])) {
        return ['erreur' => 'Envoi de photo invalide.'];
    }

    if ($f['error'] === UPLOAD_ERR_INI_SIZE || $f['error'] === UPLOAD_ERR_FORM_SIZE) {
        return ['erreur' => 'La photo est trop volumineuse (2 Mo maximum).'];
    }
    if ($f['error'] !== UPLOAD_ERR_OK) {
        return ['erreur' => 'L\'envoi de la photo a échoué. Réessayez.'];
    }
    if ($f['size'] > 2 * 1024 * 1024) {
        return ['erreur' => 'La photo est trop volumineuse (2 Mo maximum).'];
    }

    // Type réel du fichier, lu dans son contenu (et non dans son nom)
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);

    $extensions = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    if (!isset($extensions[$mime]) || getimagesize($f['tmp_name']) === false) {
        return ['erreur' => 'Format refusé : JPEG, PNG ou WebP uniquement.'];
    }

    $dossier = _plat_dossier_upload();

    if (!is_dir($dossier) || !is_writable($dossier)) {
        error_log('[Upload photo] Dossier non accessible en écriture : ' . $dossier);
        return ['erreur' => 'Le dossier d\'envoi n\'est pas accessible. Prévenez l\'administrateur.'];
    }

    // Nom aléatoire + extension tirée du type réel
    $nom = bin2hex(random_bytes(16)) . '.' . $extensions[$mime];

    if (!move_uploaded_file($f['tmp_name'], $dossier . '/' . $nom)) {
        return ['erreur' => 'L\'enregistrement de la photo a échoué.'];
    }

    return ['chemin' => '/uploads/menus/' . $nom];
}

/**
 * Efface le fichier d'une photo. basename() empêche toute sortie du dossier.
 */
function _plat_supprimer_fichier(?string $chemin): void
{
    if ($chemin === null || $chemin === '') {
        return;
    }

    $fichier = _plat_dossier_upload() . '/' . basename($chemin);

    if (is_file($fichier)) {
        @unlink($fichier);
    }
}
// ══════════════════════════════════════════════════════
// HORAIRES
// ══════════════════════════════════════════════════════

function employe_horaires(): void
{
    require_employe_ou_admin();

    render('employe/horaires', [
        'titre_page' => 'Horaires d\'ouverture',
        'horaires' => horaires_tous(),
    ]);
}

function employe_horaires_traiter(): void
{
    require_employe_ou_admin();
    csrf_verifier();

    $resultat = _horaires_lire_formulaire(horaires_tous());

    // Une seule erreur : rien n'est enregistré
    if (!empty($resultat['erreurs'])) {
        _employe_flash_redirect(
            'danger',
            implode(' ', $resultat['erreurs']),
            '/employe/horaires'
        );
    }

    horaires_enregistrer($resultat['lignes']);

    _employe_flash_redirect(
        'success',
        'Horaires mis à jour. Ils sont visibles dans le pied de page du site.',
        '/employe/horaires'
    );
}

/**
 * Lit et valide le formulaire, en partant des jours qui existent en base.
 * Retourne ['lignes' => [...], 'erreurs' => [...]].
 */
function _horaires_lire_formulaire(array $existants): array
{
    // Un champ falsifié qui ne serait pas un tableau est ignoré
    $ferme_post = is_array($_POST['ferme'] ?? null) ? $_POST['ferme'] : [];
    $ouv_post = is_array($_POST['ouverture'] ?? null) ? $_POST['ouverture'] : [];
    $ferm_post = is_array($_POST['fermeture'] ?? null) ? $_POST['fermeture'] : [];

    $format = '/^([01]\d|2[0-3]):[0-5]\d$/';   // HH:MM de 00:00 à 23:59
    $lignes = [];
    $erreurs = [];

    foreach ($existants as $h) {
        $id = (int) $h['horaire_id'];
        $jour = $h['jour'];

        // Jour fermé : les heures saisies sont ignorées
        if (isset($ferme_post[$id])) {
            $lignes[] = ['id' => $id, 'ouverture' => null, 'fermeture' => null];
            continue;
        }

        $ouv = is_string($ouv_post[$id] ?? null) ? trim($ouv_post[$id]) : '';
        $ferm = is_string($ferm_post[$id] ?? null) ? trim($ferm_post[$id]) : '';

        if (!preg_match($format, $ouv) || !preg_match($format, $ferm)) {
            $erreurs[] = "{$jour} : indiquez des heures valides (ou cochez « Fermé »).";
        } elseif ($ferm <= $ouv) {
            // Comparaison de texte : correcte car HH:MM est toujours sur 5 caractères
            $erreurs[] = "{$jour} : la fermeture doit être après l'ouverture.";
        } else {
            $lignes[] = ['id' => $id, 'ouverture' => $ouv, 'fermeture' => $ferm];
        }
    }

    return ['lignes' => $lignes, 'erreurs' => $erreurs];
}

function employe_avis_moderation(): void
{
    require_employe_ou_admin();

    render('employe/avis-moderation', [
        'titre_page' => 'Modération des avis',
        'avis' => avis_a_moderer(),
    ]);
}
function employe_avis_valider(): void
{
    require_employe_ou_admin();
    csrf_verifier();

    $avis_id = (int) post_param('avis_id', 0);
    $decision = post_param('decision', '');

    // Le navigateur envoie un mot ; c'est le serveur qui choisit le statut écrit en base
    $statuts = [
        'publier' => 'publié',
        'refuser' => 'refusé',
    ];

    if ($avis_id <= 0 || !isset($statuts[$decision])) {
        _employe_flash_redirect('danger', 'Demande invalide.', '/employe/avis');
    }

    if (avis_changer_statut($avis_id, $statuts[$decision])) {
        _employe_flash_redirect(
            'success',
            $decision === 'publier' ? 'Avis publié.' : 'Avis refusé.',
            '/employe/avis'
        );
    }

    // L'avis n'était plus "en attente" : un collègue l'a déjà traité, ou il n'existe pas
    _employe_flash_redirect(
        'info',
        'Cet avis a déjà été traité ou n\'existe plus.',
        '/employe/avis'
    );
}

// ══════════════════════════════════════════════════════
// COMMANDES — détail (remplace l'ancienne version)
// ══════════════════════════════════════════════════════

function employe_commande_detail(): void
{
    require_employe_ou_admin();

    $numero = get_param('numero', '');
    $commande = commande_detail_employe($numero);

    if (!$commande) {
        _employe_flash_redirect('danger', 'Commande introuvable.', '/employe/commandes');
    }

    render('employe/commande-statut', [
        'titre_page' => 'Commande ' . $commande['numero_commande'],
        'commande' => $commande,
        'suivants' => _commande_statuts_suivants($commande),
        'peut_annuler' => in_array($commande['statut'], STATUTS_ANNULABLES, true),
        'modes' => MODES_CONTACT,
    ]);
}

// ══════════════════════════════════════════════════════
// COMMANDES — traitement des 3 formulaires (POST)
// ══════════════════════════════════════════════════════

function employe_commande_statut(): void
{
    require_employe_ou_admin();
    csrf_verifier();

    $numero = post_param('numero', '');
    $action = post_param('action', '');
    $commande = commande_detail_employe($numero);

    if (!$commande) {
        _employe_flash_redirect('danger', 'Commande introuvable.', '/employe/commandes');
    }

    $retour = '/employe/commande?numero=' . urlencode($numero);

    switch ($action) {
        case 'changer':
            _commande_action_changer($commande, $retour);
            break;
        case 'materiel':
            _commande_action_materiel($commande, $retour);
            break;
        case 'annuler':
            _commande_action_annuler($commande, $retour);
            break;
        default:
            _employe_flash_redirect('danger', 'Action inconnue.', $retour);
    }
}

// ── Fonctions privées ──────────────────────────────────────────────

/**
 * Flash + redirection en une ligne. Ne retourne jamais.
 */
function _employe_flash_redirect(string $type, string $message, string $url): never
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    redirect($url);
}

/**
 * Statuts qui peuvent suivre celui de la commande.
 * Source unique : utilisée par l'affichage ET par le traitement POST.
 */
function _commande_statuts_suivants(array $commande): array
{
    $suivants = TRANSITIONS_STATUT[$commande['statut']] ?? [];

    // Seule branche qui dépend de la commande : après "livré"
    if ($commande['statut'] === STATUT_LIVRE) {
        $suivants = ((int) $commande['pret_materiel'] === 1)
            ? [STATUT_MATERIEL]
            : [STATUT_TERMINEE];
    }

    return $suivants;
}


/**
 * Envoie le mail lié au nouveau statut, s'il y en a un.
 * Un échec d'envoi est journalisé mais ne bloque jamais l'employé :
 * le statut est déjà enregistré à ce stade.
 */
function _commande_notifier_statut(array $commande, string $nouveau): void
{
    if ($nouveau === STATUT_MATERIEL) {
        $gabarit = 'retour_materiel';
        $sujet = 'Retour de matériel — commande ' . $commande['numero_commande'];
    } elseif ($nouveau === STATUT_TERMINEE) {
        $gabarit = 'invitation_avis';
        $sujet = 'Donnez votre avis — commande ' . $commande['numero_commande'];
    } else {
        return; // les autres statuts n'envoient pas de mail
    }

    // Variables utilisées par les gabarits (chacun prend ce dont il a besoin)
    $prenom = $commande['client_prenom'];
    $numero = $commande['numero_commande'];
    $delai = DELAI_RETOUR_MATERIEL;
    $penalite = prix_format(PENALITE_MATERIEL);
    $base_url = getenv('APP_URL') ?: 'http://localhost:8080';
    $lien_avis = $base_url . '/compte/commande?numero=' . urlencode($numero);

    try {
        $mail = creer_mailer();
        $mail->addAddress(
            $commande['client_email'],
            $commande['client_prenom'] . ' ' . $commande['client_nom']
        );
        $mail->isHTML(true);
        $mail->Subject = $sujet;

        ob_start();
        require dirname(__DIR__) . '/mail_templates/' . $gabarit . '.php';
        $mail->Body = ob_get_clean();

        $mail->AltBody = ($gabarit === 'retour_materiel')
            ? "Bonjour {$prenom},\n\nDu matériel vous a été prêté pour la commande {$numero}. "
            . "Merci de le restituer sous {$delai} jours ouvrés, sans quoi {$penalite} "
            . "de frais vous seront facturés.\nContact : contact@viteetgourmand.fr"
            : "Bonjour {$prenom},\n\nVotre commande {$numero} est terminée. "
            . "Donnez votre avis depuis votre compte :\n{$lien_avis}";

        $mail->send();

    } catch (Throwable $e) {
        // ob_start() est peut-être resté ouvert si le gabarit a planté
        if (ob_get_level() > 0) {
            ob_end_clean();
        }
        error_log('[Mail statut ' . $nouveau . '] Échec : ' . $e->getMessage());
    }
}

function _commande_action_changer(array $commande, string $retour): void
{
    $nouveau = post_param('nouveau_statut', '');

    // On recalcule ce qui est permis : on ne fait pas confiance au formulaire
    if (!in_array($nouveau, _commande_statuts_suivants($commande), true)) {
        _employe_flash_redirect('danger', 'Changement de statut non autorisé.', $retour);
    }

    commande_changer_statut(
        $commande['numero_commande'],
        $nouveau,
        'Mis à jour par ' . ($_SESSION['prenom'] ?? 'l\'équipe') . '.'
    );

    // Fin du circuit matériel : on enregistre la restitution
    if ($commande['statut'] === STATUT_MATERIEL && $nouveau === STATUT_TERMINEE) {
        commande_marquer_restitution($commande['numero_commande']);
    }

    // Mails automatiques : retour de matériel / invitation à donner un avis
    _commande_notifier_statut($commande, $nouveau);

    _employe_flash_redirect('success', "Commande passée à « {$nouveau} ».", $retour);
}


function _commande_action_materiel(array $commande, string $retour): void
{
    // Le prêt ne se modifie qu'avant la livraison (comme dans la vue)
    $modifiable = [STATUT_ACCEPTE, STATUT_PREPARATION, STATUT_LIVRAISON];

    if (!in_array($commande['statut'], $modifiable, true)) {
        _employe_flash_redirect(
            'danger',
            'Le prêt de matériel ne peut plus être modifié à ce stade.',
            $retour
        );
    }

    $pret = (post_param('pret_materiel') === '1') ? 1 : 0;
    commande_maj_materiel($commande['numero_commande'], $pret);

    _employe_flash_redirect(
        'success',
        $pret ? 'Prêt de matériel enregistré.' : 'Prêt de matériel retiré.',
        $retour
    );
}

function _commande_action_annuler(array $commande, string $retour): void
{
    if (!in_array($commande['statut'], STATUTS_ANNULABLES, true)) {
        _employe_flash_redirect('danger', 'Cette commande ne peut plus être annulée.', $retour);
    }

    $mode = post_param('mode_contact', '');
    $motif = post_param('motif', '');

    if (!in_array($mode, MODES_CONTACT, true)) {
        _employe_flash_redirect('danger', 'Indiquez le mode de contact utilisé.', $retour);
    }
    if (mb_strlen($motif) < 10) {
        _employe_flash_redirect(
            'danger',
            'Le motif doit contenir au moins 10 caractères.',
            $retour
        );
    }

    commande_annuler_employe($commande['numero_commande'], $mode, $motif);

    _employe_flash_redirect('success', 'Commande annulée.', $retour);
}