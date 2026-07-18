
-- =====================================================================
-- 01_create_tables.sql
-- Schéma complet de la base de données Vite & Gourmand
-- Version corrigée US-0.4
-- =====================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- ── Tables de référence ──────────────────────────────────────────────

CREATE TABLE `regime` (
  `regime_id` INT PRIMARY KEY AUTO_INCREMENT,
  `libelle`   VARCHAR(50) NOT NULL
);

CREATE TABLE `theme` (
  `theme_id` INT PRIMARY KEY AUTO_INCREMENT,
  `libelle`  VARCHAR(50) NOT NULL
);

CREATE TABLE `allergene` (
  `allergene_id` INT PRIMARY KEY AUTO_INCREMENT,
  `libelle`      VARCHAR(50) NOT NULL
);

CREATE TABLE `role` (
  `role_id` INT PRIMARY KEY,
  `libelle` VARCHAR(50) NOT NULL
);

CREATE TABLE `horaire` (
  `horaire_id`      INT PRIMARY KEY AUTO_INCREMENT,
  `jour`            VARCHAR(10)  NOT NULL,
  `heure_ouverture` VARCHAR(5)  NULL COMMENT 'Format HH:MM ou NULL si fermé',
  `heure_fermeture` VARCHAR(5)  NULL COMMENT 'Format HH:MM ou NULL si fermé'
);

-- ── Utilisateurs ─────────────────────────────────────────────────────

CREATE TABLE `utilisateur` (
  `utilisateur_id` INT          PRIMARY KEY AUTO_INCREMENT,
  `nom`            VARCHAR(100) NOT NULL,
  `prenom`         VARCHAR(100) NOT NULL,
  `email`          VARCHAR(150) NOT NULL UNIQUE,
  `password`       VARCHAR(255) NOT NULL,          -- bcrypt = 60 chars min
  `telephone`      VARCHAR(20)  NOT NULL,
  `ville`          VARCHAR(100) NOT NULL,
  `pays`           VARCHAR(100) NOT NULL,
  `adresse_postale`VARCHAR(255) NOT NULL,
  `role_id`        INT          NOT NULL,
  `actif`          TINYINT(1)   NOT NULL DEFAULT 1 -- désactivation compte sans suppression
);

-- ── Token de réinitialisation mot de passe (US-1.3) ──────────────────

CREATE TABLE `token_reset` (
  `token_id`       INT          PRIMARY KEY AUTO_INCREMENT,
  `token`          VARCHAR(100) NOT NULL UNIQUE,
  `utilisateur_id` INT          NOT NULL,
  `expire_at`      DATETIME     NOT NULL,
  `utilise`        TINYINT(1)   NOT NULL DEFAULT 0
);

-- ── Menus et plats ───────────────────────────────────────────────────

CREATE TABLE `menu` (
  `menu_id`                 INT          PRIMARY KEY AUTO_INCREMENT,
  `titre`                   VARCHAR(150) NOT NULL,
  `nombre_personne_minimum` INT          NOT NULL,
  `prix_par_personne`       DOUBLE       NOT NULL,
  `description`             TEXT         NULL,
  `quantite_restante`       INT          NOT NULL DEFAULT 0,
  `regime_id`               INT          NULL,     -- nullable : pas toujours un régime
  `theme_id`                INT          NULL      -- nullable : pas toujours un thème
);

CREATE TABLE `plat` (
  `plat_id`    INT          PRIMARY KEY AUTO_INCREMENT,
  `titre_plat` VARCHAR(150) NOT NULL,
  `photo`      VARCHAR(255) NULL  -- chemin relatif : '/uploads/menus/nom-fichier.jpg'
);

-- Tables de liaison menu <-> plat et plat <-> allergène

CREATE TABLE `compose` (
  `menu_id` INT NOT NULL,
  `plat_id` INT NOT NULL,
  PRIMARY KEY (`menu_id`, `plat_id`)
);

CREATE TABLE `contient` (
  `plat_id`      INT NOT NULL,
  `allergene_id` INT NOT NULL,
  PRIMARY KEY (`plat_id`, `allergene_id`)
);

-- ── Commandes ────────────────────────────────────────────────────────

CREATE TABLE `commande` (
  `numero_commande`         VARCHAR(50)  PRIMARY KEY,
  `date_commande`           DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_prestation`         DATE         NOT NULL,
  `heure_livraison`         TIME         NOT NULL,
  `prix_menu`               DOUBLE       NOT NULL,
  `nombre_personne`         INT          NOT NULL,
  `prix_livraison`          DOUBLE       NOT NULL DEFAULT 0,
  `statut` ENUM(
    'en attente',
    'accepté',
    'en préparation',
    'en cours de livraison',
    'livré',
    'en attente du retour de matériel',
    'terminée',
    'annulée'
  ) NOT NULL DEFAULT 'en attente',
  `pret_materiel`               TINYINT(1)   NOT NULL DEFAULT 0,
  `restitution_materiel`        TINYINT(1)   NOT NULL DEFAULT 0,
  `mode_contact_annulation`     VARCHAR(50)  NULL,
  `motif_annulation`            TEXT         NULL,
  `menu_id`                     INT          NOT NULL,
  `utilisateur_id`              INT          NOT NULL
);

-- ── Historique des statuts (US-3.1) ──────────────────────────────────

CREATE TABLE `historique_statut_commande` (
  `historique_id`   INT          PRIMARY KEY AUTO_INCREMENT,
  `numero_commande` VARCHAR(50)  NOT NULL,
  `statut`          VARCHAR(100) NOT NULL,
  `commentaire`     TEXT         NULL,
  `created_at`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ── Avis ─────────────────────────────────────────────────────────────

CREATE TABLE `avis` (
  `avis_id`        INT        PRIMARY KEY AUTO_INCREMENT,
  `note`           TINYINT UNSIGNED NOT NULL,  -- valeur entre 1 et 5
  `description`    VARCHAR(500) NULL,
  `statut`         ENUM('en attente', 'publié', 'refusé') NOT NULL DEFAULT 'en attente',
  `commande_id`    VARCHAR(50)  NOT NULL,       -- lien vers la commande concernée
  `utilisateur_id` INT          NOT NULL
);

-- ── Clés étrangères ──────────────────────────────────────────────────

ALTER TABLE `utilisateur`
  ADD FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`);

ALTER TABLE `token_reset`
  ADD FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`utilisateur_id`);

ALTER TABLE `menu`
  ADD FOREIGN KEY (`regime_id`) REFERENCES `regime` (`regime_id`),
  ADD FOREIGN KEY (`theme_id`)  REFERENCES `theme`  (`theme_id`);

ALTER TABLE `commande`
  ADD FOREIGN KEY (`menu_id`)       REFERENCES `menu`        (`menu_id`),
  ADD FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`utilisateur_id`);

ALTER TABLE `historique_statut_commande`
  ADD FOREIGN KEY (`numero_commande`) REFERENCES `commande` (`numero_commande`);

ALTER TABLE `avis`
  ADD FOREIGN KEY (`commande_id`)    REFERENCES `commande`    (`numero_commande`),
  ADD FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`utilisateur_id`);

ALTER TABLE `compose`
  ADD FOREIGN KEY (`menu_id`) REFERENCES `menu` (`menu_id`),
  ADD FOREIGN KEY (`plat_id`) REFERENCES `plat` (`plat_id`);

ALTER TABLE `contient`
  ADD FOREIGN KEY (`plat_id`)      REFERENCES `plat`      (`plat_id`),
  ADD FOREIGN KEY (`allergene_id`) REFERENCES `allergene` (`allergene_id`);

SET FOREIGN_KEY_CHECKS = 1;