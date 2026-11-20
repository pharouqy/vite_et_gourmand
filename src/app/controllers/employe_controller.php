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
    echo "Menus employé";
}
function employe_menu_creer_form(): void
{
    require_employe_ou_admin();
    echo "Créer menu";
}
function employe_menu_creer_traiter(): void
{
    require_employe_ou_admin();
    redirect('/employe/menus');
}
function employe_menu_modifier_form(): void
{
    require_employe_ou_admin();
    echo "Modifier menu";
}
function employe_menu_modifier_traiter(): void
{
    require_employe_ou_admin();
    redirect('/employe/menus');
}
function employe_menu_supprimer(): void
{
    require_employe_ou_admin();
    redirect('/employe/menus');
}
function employe_plats_liste(): void
{
    require_employe_ou_admin();
    echo "Plats";
}
function employe_horaires(): void
{
    require_employe_ou_admin();
    echo "Horaires";
}
function employe_horaires_traiter(): void
{
    require_employe_ou_admin();
    redirect('/employe/horaires');
}

function employe_avis_moderation(): void
{
    require_employe_ou_admin();
    echo "Modération avis";
}
function employe_avis_valider(): void
{
    require_employe_ou_admin();
    redirect('/employe/avis');
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