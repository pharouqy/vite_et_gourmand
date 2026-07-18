-- =====================================================================
-- 02_seed_data.sql
-- Jeu de données de test — Vite & Gourmand
-- ⚠️  À exécuter APRÈS 01_create_tables.sql
-- =====================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- 1. TABLES DE RÉFÉRENCE (pas de dépendances)
-- ---------------------------------------------------------------------

-- role_id sans AUTO_INCREMENT → valeurs forcées
INSERT INTO `role` (`role_id`, `libelle`) VALUES
  (1, 'Administrateur'),
  (2, 'Employé'),
  (3, 'Client');

INSERT INTO `theme` (`theme_id`, `libelle`) VALUES
  (1, 'Anniversaire'),
  (2, 'Mariage'),
  (3, 'Entreprise'),
  (4, 'Fête de fin d''année');

INSERT INTO `regime` (`regime_id`, `libelle`) VALUES
  (1, 'Sans gluten'),
  (2, 'Végétarien'),
  (3, 'Halal'),
  (4, 'Végan');

INSERT INTO `allergene` (`allergene_id`, `libelle`) VALUES
  (1, 'Gluten'),
  (2, 'Lactose'),
  (3, 'Fruits à coque'),
  (4, 'Oeufs'),
  (5, 'Crustacés');

-- heure_ouverture / heure_fermeture : VARCHAR(5) format HH:MM
-- Dimanche fermé → NULL (plus propre qu'une chaîne 'Fermé')
INSERT INTO `horaire` (`horaire_id`, `jour`, `heure_ouverture`, `heure_fermeture`) VALUES
  (1, 'Lundi',     '09:00', '18:00'),
  (2, 'Mardi',     '09:00', '18:00'),
  (3, 'Mercredi',  '09:00', '18:00'),
  (4, 'Jeudi',     '09:00', '18:00'),
  (5, 'Vendredi',  '09:00', '19:00'),
  (6, 'Samedi',    '10:00', '15:00'),
  (7, 'Dimanche',  NULL,    NULL);

-- ---------------------------------------------------------------------
-- 2. UTILISATEURS (dépend de role)
--    Ajout colonne `nom` + password VARCHAR(255)
--    Les hashs sont des placeholders — en dev réel, générés par password_hash()
-- ---------------------------------------------------------------------

INSERT INTO `utilisateur`
  (`utilisateur_id`, `nom`, `prenom`, `email`, `password`,
   `telephone`, `ville`, `pays`, `adresse_postale`, `role_id`, `actif`)
VALUES
  (1, 'Benali',   'Nadia',  'admin@vitegourmand.dz',
   '$2y$12$abcdefghijklmnopqrstuuVb1z2Y3X4W5V6U7T8S9R0Q1P2O3N4M5',
   '0550112233', 'Alger',  'Algérie', '12 Rue Didouche Mourad',       1, 1),

  (2, 'Meziane',  'Karim',  'employe@vitegourmand.dz',
   '$2y$12$abcdefghijklmnopqrstuuAb1z2Y3X4W5V6U7T8S9R0Q1P2O3N4M5',
   '0661223344', 'Blida',  'Algérie', '5 Cité des Frères Boudjellal', 2, 1),

  (3, 'Amrani',   'Sofia',  'sofia.client@gmail.com',
   '$2y$12$abcdefghijklmnopqrstuuBb1z2Y3X4W5V6U7T8S9R0Q1P2O3N4M5',
   '0770334455', 'Khemis', 'Algérie', '18 Route Nationale 1',         3, 1),

  (4, 'Oualiken', 'Yanis',  'yanis.client@gmail.com',
   '$2y$12$abcdefghijklmnopqrstuuCb1z2Y3X4W5V6U7T8S9R0Q1P2O3N4M5',
   '0551445566', 'Oran',   'Algérie', '27 Boulevard Front de Mer',    3, 1);

-- ---------------------------------------------------------------------
-- 3. MENUS (dépend de regime, theme)
--    description → TEXT (plus de limite à 50 chars)
--    regime_id et theme_id nullable (Menu Découverte = cas limite)
-- ---------------------------------------------------------------------

INSERT INTO `menu`
  (`menu_id`, `titre`, `nombre_personne_minimum`, `prix_par_personne`,
   `description`, `quantite_restante`, `regime_id`, `theme_id`)
VALUES
  (1, 'Menu Célébration Halal',     20, 2500.00,
   'Menu complet halal pour grandes occasions familiales. Service à table inclus, vaisselle fournie.',
   15, 3, 1),

  (2, 'Menu Mariage Prestige',      50, 4500.00,
   'Menu raffiné pour mariage avec service à table, décoration florale et chef cuisinier sur place.',
   5, NULL, 2),

  (3, 'Menu Entreprise Végétarien', 10, 1800.00,
   'Buffet végétarien adapté aux réunions professionnelles. Produits frais et de saison.',
   25, 2, 3),

  (4, 'Menu Sans Gluten Festif',    15, 3000.00,
   'Menu 100% sans gluten certifié pour fêtes de fin d''année. Idéal pour les personnes intolérantes.',
   10, 1, 4),

  (5, 'Menu Découverte',             5,  900.00,
   'Petit menu simple et accessible, sans contrainte diététique particulière. Parfait pour une première expérience.',
   30, NULL, NULL);

-- ---------------------------------------------------------------------
-- 4. PLATS (indépendant)
--    photo → VARCHAR(255) chemin relatif (plus de BLOB)
--    NULL = photo non encore uploadée
-- ---------------------------------------------------------------------

INSERT INTO `plat` (`plat_id`, `titre_plat`, `photo`) VALUES
  (1, 'Chorba frik',              NULL),
  (2, 'Couscous royal',           NULL),
  (3, 'Bourek au fromage',        NULL),
  (4, 'Salade composée aux noix', NULL),
  (5, 'Tajine de légumes',        NULL),
  (6, 'Gâteau aux amandes',       NULL),
  (7, 'Plateau de fruits frais',  NULL),
  (8, 'Chapati grillé nature',    NULL);

-- ---------------------------------------------------------------------
-- 5. COMMANDES (dépend de menu, utilisateur)
--    statut → valeurs ENUM exactes du DDL
--    heure_livraison → format TIME 'HH:MM:SS'
--    6 commandes pour couvrir TOUS les statuts,
--    y compris 'terminée' (nécessaire pour les avis)
-- ---------------------------------------------------------------------

INSERT INTO `commande`
  (`numero_commande`, `date_commande`, `date_prestation`, `heure_livraison`,
   `prix_menu`, `nombre_personne`, `prix_livraison`, `statut`,
   `pret_materiel`, `restitution_materiel`,
   `mode_contact_annulation`, `motif_annulation`,
   `menu_id`, `utilisateur_id`)
VALUES
  -- Sofia | en attente | matériel prêté, pas encore restitué
  ('CMD-2026-0001', '2026-06-10', '2026-06-20', '12:00:00',
   50000.00,  20, 2000.00, 'en attente',
   1, 0, NULL, NULL, 1, 3),

  -- Yanis | accepté | matériel prêté
  ('CMD-2026-0002', '2026-06-11', '2026-07-15', '13:30:00',
   225000.00, 50, 5000.00, 'accepté',
   1, 0, NULL, NULL, 2, 4),

  -- Sofia | en préparation | sans matériel
  ('CMD-2026-0003', '2026-06-12', '2026-06-18', '09:00:00',
   18000.00,  10, 1000.00, 'en préparation',
   0, 0, NULL, NULL, 3, 3),

  -- Yanis | annulée | contact par téléphone
  ('CMD-2026-0004', '2026-05-15', '2026-05-22', '11:00:00',
    4500.00,   5,  800.00, 'annulée',
   0, 0, 'téléphone', 'Menu non disponible à cette date, manque de personnel.', 5, 4),

  -- Sofia | en attente du retour de matériel
  ('CMD-2026-0005', '2026-05-01', '2026-05-10', '14:00:00',
   45000.00,  15, 1500.00, 'en attente du retour de matériel',
   1, 0, NULL, NULL, 4, 3),

  -- Yanis | terminée → peut laisser un avis ✅
  ('CMD-2026-0006', '2026-04-10', '2026-04-20', '18:00:00',
   45000.00,  15, 1500.00, 'terminée',
   1, 1, NULL, NULL, 4, 4);

-- ---------------------------------------------------------------------
-- 6. HISTORIQUE STATUTS (dépend de commande)
--    Trace le cycle de vie complet de CMD-2026-0006 (commande terminée)
--    C'est la table qui alimente la "frise de suivi" côté client (US-3.6)
-- ---------------------------------------------------------------------

INSERT INTO `historique_statut_commande`
  (`numero_commande`, `statut`, `commentaire`, `created_at`)
VALUES
  ('CMD-2026-0006', 'en attente',             NULL,                              '2026-04-10 09:00:00'),
  ('CMD-2026-0006', 'accepté',                'Commande validée par l''équipe.', '2026-04-11 10:30:00'),
  ('CMD-2026-0006', 'en préparation',         NULL,                              '2026-04-19 08:00:00'),
  ('CMD-2026-0006', 'en cours de livraison',  NULL,                              '2026-04-20 16:00:00'),
  ('CMD-2026-0006', 'livré',                  NULL,                              '2026-04-20 18:15:00'),
  ('CMD-2026-0006', 'en attente du retour de matériel',
   'Matériel prêté : 10 chafing dishes. Retour attendu sous 10 jours ouvrés.', '2026-04-20 18:20:00'),
  ('CMD-2026-0006', 'terminée',               'Matériel restitué le 25/04.',     '2026-04-25 11:00:00');

-- ---------------------------------------------------------------------
-- 7. AVIS (dépend de commande ET utilisateur)
--    note → TINYINT (1-5), pas de VARCHAR
--    statut → ENUM('en attente', 'publié', 'refusé')
--    commande_id obligatoire → uniquement sur commandes 'terminée'
--    ici CMD-2026-0006 (Yanis, menu Sans Gluten Festif)
-- ---------------------------------------------------------------------

INSERT INTO `avis` (`note`, `description`, `statut`, `commande_id`, `utilisateur_id`) VALUES
  (5, 'Service impeccable, plats délicieux et livraison à l''heure. Je recommande vivement !',
   'publié',     'CMD-2026-0006', 4),

  (3, 'Bonne prestation dans l''ensemble, mais le matériel rendu avec deux jours de retard.',
   'en attente', 'CMD-2026-0006', 4);

-- Note : un seul utilisateur peut avoir plusieurs avis SI plusieurs commandes terminées.
-- Ici Yanis (id=4) a deux avis sur la même commande pour tester la modération.
-- En prod, on ajoutera une contrainte UNIQUE(commande_id) côté applicatif.

-- ---------------------------------------------------------------------
-- 8. COMPOSE (menu ↔ plat) — inchangé
-- ---------------------------------------------------------------------

INSERT INTO `compose` (`menu_id`, `plat_id`) VALUES
  (1, 1), (1, 2), (1, 6),           -- Menu Célébration Halal
  (2, 2), (2, 4), (2, 6), (2, 7),   -- Menu Mariage Prestige
  (3, 4), (3, 5), (3, 7),           -- Menu Entreprise Végétarien
  (4, 5), (4, 7), (4, 8),           -- Menu Sans Gluten Festif
  (5, 3), (5, 4);                   -- Menu Découverte

-- ---------------------------------------------------------------------
-- 9. CONTIENT (plat ↔ allergène) — inchangé
--    plat_id 7 (fruits frais) sans allergène : cas limite volontaire
-- ---------------------------------------------------------------------

INSERT INTO `contient` (`plat_id`, `allergene_id`) VALUES
  (1, 1),                -- Chorba frik          : Gluten
  (2, 1),                -- Couscous royal       : Gluten
  (3, 1), (3, 2), (3, 4),-- Bourek               : Gluten, Lactose, Oeufs
  (4, 3),                -- Salade aux noix      : Fruits à coque
  (5, 2),                -- Tajine de légumes    : Lactose
  (6, 2), (6, 3), (6, 4),-- Gâteau aux amandes   : Lactose, Fruits à coque, Oeufs
  (8, 1);                -- Chapati              : Gluten
  -- plat_id 7 : aucun allergène → cas limite pour les filtres côté client

SET FOREIGN_KEY_CHECKS = 1;