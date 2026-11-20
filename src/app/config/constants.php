<?php

declare(strict_types=1);

// ── Frais de livraison (US-3.1/3.2) ─────────────────────────────────
const FRAIS_LIVRAISON_BASE = 5.00;    // Forfait de base en euros
const FRAIS_LIVRAISON_KM = 0.59;    // Par kilomètre hors Bordeaux
const VILLE_REFERENCE = 'bordeaux';

// ── Règle de remise (US-3.2) ─────────────────────────────────────────
const REMISE_SEUIL_PERSONNES = 5;       // +5 personnes au-delà du minimum
const REMISE_TAUX = 0.10;    // 10%

// ── Matériel (US-4.2/4.3) ────────────────────────────────────────────
const PENALITE_MATERIEL = 600.00;  // Euros
const DELAI_RETOUR_MATERIEL = 10;      // Jours ouvrés

// ── Rôles ────────────────────────────────────────────────────────────
const ROLE_ADMIN = 1;
const ROLE_EMPLOYE = 2;
const ROLE_CLIENT = 3;

// ── Statuts commande ─────────────────────────────────────────────────
const STATUT_EN_ATTENTE = 'en attente';
const STATUT_ACCEPTE = 'accepté';
const STATUT_PREPARATION = 'en préparation';
const STATUT_LIVRAISON = 'en cours de livraison';
const STATUT_LIVRE = 'livré';
const STATUT_MATERIEL = 'en attente du retour de matériel';
const STATUT_TERMINEE = 'terminée';
const STATUT_ANNULEE = 'annulée';

// ── Cycle de vie d'une commande (US-4.2/4.3) ─────────────────────────
// Pour chaque statut : la liste des statuts qui peuvent le suivre.
// L'annulation est gérée à part (voir STATUTS_ANNULABLES) car elle
// exige un motif et un mode de contact.
const TRANSITIONS_STATUT = [
    STATUT_EN_ATTENTE => [STATUT_ACCEPTE],
    STATUT_ACCEPTE => [STATUT_PREPARATION],
    STATUT_PREPARATION => [STATUT_LIVRAISON],
    STATUT_LIVRAISON => [STATUT_LIVRE],
    // Après "livré" : retour de matériel si du matériel a été prêté,
    // sinon la commande est directement terminée.
    // Le filtrage selon pret_materiel est fait dans le contrôleur.
    STATUT_LIVRE => [STATUT_MATERIEL, STATUT_TERMINEE],
    STATUT_MATERIEL => [STATUT_TERMINEE],
    STATUT_TERMINEE => [],
    STATUT_ANNULEE => [],
];

// Statuts depuis lesquels l'employé peut annuler (avec motif obligatoire).
// Une fois la livraison commencée, on n'annule plus.
const STATUTS_ANNULABLES = [
    STATUT_EN_ATTENTE,
    STATUT_ACCEPTE,
    STATUT_PREPARATION,
];

// Modes de contact acceptés pour justifier une annulation.
const MODES_CONTACT = ['téléphone', 'email'];