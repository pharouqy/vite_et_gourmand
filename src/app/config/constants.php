<?php

declare(strict_types=1);

// ── Frais de livraison (US-3.1/3.2) ─────────────────────────────────
const FRAIS_LIVRAISON_BASE    = 5.00;    // Forfait de base en euros
const FRAIS_LIVRAISON_KM      = 0.59;    // Par kilomètre hors Bordeaux
const VILLE_REFERENCE         = 'bordeaux';

// ── Règle de remise (US-3.2) ─────────────────────────────────────────
const REMISE_SEUIL_PERSONNES  = 5;       // +5 personnes au-delà du minimum
const REMISE_TAUX             = 0.10;    // 10%

// ── Matériel (US-4.2/4.3) ────────────────────────────────────────────
const PENALITE_MATERIEL       = 600.00;  // Euros
const DELAI_RETOUR_MATERIEL   = 10;      // Jours ouvrés

// ── Rôles ────────────────────────────────────────────────────────────
const ROLE_ADMIN    = 1;
const ROLE_EMPLOYE  = 2;
const ROLE_CLIENT   = 3;

// ── Statuts commande ─────────────────────────────────────────────────
const STATUT_EN_ATTENTE    = 'en attente';
const STATUT_ACCEPTE       = 'accepté';
const STATUT_PREPARATION   = 'en préparation';
const STATUT_LIVRAISON     = 'en cours de livraison';
const STATUT_LIVRE         = 'livré';
const STATUT_MATERIEL      = 'en attente du retour de matériel';
const STATUT_TERMINEE      = 'terminée';
const STATUT_ANNULEE       = 'annulée';
