# Vite & Gourmand

Projet ECF — Titre Professionnel Développeur Web et Web Mobile (Studi)
Application web de commande en ligne pour un traiteur événementiel, développée en PHP MVC natif.

---

## Sommaire

1. [Description du projet](#description-du-projet)
2. [Contexte et objectifs](#contexte-et-objectifs)
3. [Architecture du projet](#architecture-du-projet)
4. [Technologies utilisées](#technologies-utilisées)
5. [Prérequis](#prérequis)
6. [Installation et configuration](#installation-et-configuration)
7. [Variables d'environnement](#variables-denvironnement)
8. [Base de données](#base-de-données)
9. [Lancement du projet](#lancement-du-projet)
10. [Structure MVC détaillée](#structure-mvc-détaillée)
11. [Fonctionnalités réalisées](#fonctionnalités-réalisées)
12. [Fonctionnalités en cours de développement](#fonctionnalités-en-cours-de-développement)
13. [Choix techniques et principes d'architecture](#choix-techniques-et-principes-darchitecture)
14. [Conventions de développement](#conventions-de-développement)
15. [Améliorations futures](#améliorations-futures)
16. [Auteur](#auteur)
17. [Licence](#licence)

---

## Description du projet

**Vite & Gourmand** est une application web développée dans le cadre de l'ECF (Évaluation en Cours de Formation) du Titre Professionnel Développeur Web et Web Mobile (RNCP).

Elle permet à une entreprise de traiteur événementiel de :

- présenter son catalogue de menus de manière attractive,
- recevoir des commandes en ligne avec un calcul tarifaire automatisé,
- piloter son activité via des espaces dédiés (employé, administrateur).

Le projet respecte les exigences du sujet : conformité RGPD, accessibilité RGAA, double base de données (MySQL + MongoDB), déploiement en ligne et production de quatre livrables documentaires.

---

## Contexte et objectifs

### Contexte métier

La société **Vite & Gourmand**, traiteur événementiel basé à Bordeaux, souhaite moderniser son processus de commande. L'application cible quatre types d'utilisateurs :

| Rôle | Description |
|---|---|
| Visiteur | Consulte les menus, contacte l'entreprise |
| Utilisateur (client) | Passe des commandes, suit leur évolution, dépose un avis |
| Employé | Gère les menus, les commandes et la modération des avis |
| Administrateur | Pilote les comptes employés, consulte les statistiques et le chiffre d'affaires |

### Objectifs techniques

- Mettre en œuvre une architecture **MVC native en PHP 8**
- Utiliser **deux paradigmes de bases de données** : MySQL (données relationnelles) et MongoDB (statistiques analytiques)
- Sécuriser l'application contre les failles courantes (injection SQL, XSS, CSRF, IDOR)
- Respecter les critères d'accessibilité **RGAA**
- Déployer l'application sur un hébergeur en ligne

---

## Architecture du projet

Le projet suit le patron **MVC (Modèle — Vue — Contrôleur)** sans framework tiers.

```
vite-et-gourmand/
├── public/              ← Point d'entrée unique (document root du vhost)
│   ├── index.php        ← Routeur frontal
│   ├── .htaccess        ← Réécriture d'URL (mod_rewrite)
│   ├── assets/          ← CSS, JavaScript, images
│   └── uploads/         ← Galeries d'images uploadées par les employés
│
├── app/                 ← Code métier (inaccessible depuis le navigateur)
│   ├── config/          ← Connexions BDD, mail, constantes métier
│   ├── core/            ← Composants transversaux : routeur, requête, réponse, middleware, CSRF
│   ├── controllers/     ← Un contrôleur par domaine fonctionnel
│   ├── models/          ← Un modèle par entité de base de données
│   ├── views/           ← Templates PHP organisés par rôle
│   └── mail_templates/  ← Templates d'emails transactionnels
│
├── sql/                 ← Scripts de création et de peuplement de la base MySQL
├── scripts/             ← Script de synchronisation MySQL → MongoDB
├── docs/                ← Livrables documentaires (charte, manuels, docs techniques)
└── vendor/              ← Dépendances Composer (PHPMailer, driver MongoDB)
```

Seul le dossier `public/` est exposé au web. Tout le reste reste inaccessible depuis le navigateur.

---

## Technologies utilisées

| Couche | Technologie |
|---|---|
| Langage back-end | PHP 8 |
| Base de données relationnelle | MySQL |
| Base de données NoSQL | MongoDB |
| Accès données (MySQL) | PDO avec requêtes préparées |
| Accès données (MongoDB) | Driver officiel `mongodb/mongodb` (Composer) |
| Front-end | HTML5, CSS3, JavaScript vanilla (Fetch API) |
| Framework CSS | Bootstrap 5 |
| Envoi d'emails | PHPMailer via SMTP |
| Visualisation de données | Chart.js |
| Gestion des dépendances | Composer |
| Versionnement | Git / GitHub |
| Déploiement | Railway (ou fly.io / o2switch selon configuration retenue) |

---

## Prérequis

Avant d'installer le projet, assurez-vous de disposer des éléments suivants sur votre machine :

- **PHP** >= 8.0 avec les extensions `pdo_mysql`, `mongodb`, `mbstring`, `openssl`
- **MySQL** >= 8.0
- **MongoDB Community** >= 6.0
- **Composer** >= 2.x
- Un serveur local compatible PHP : [Laragon](https://laragon.org/) (recommandé sous Windows), XAMPP ou MAMP
- **Git**

Pour vérifier vos versions :

```bash
php -v
mysql --version
mongosh --version
composer --version
```

---

## Installation et configuration

### 1. Cloner le dépôt

```bash
git clone https://github.com/pharouqy/vite_et_gourmand.git
cd vite_et_gourmand
```

### 2. Basculer sur la branche de développement

```bash
git checkout develop
```

### 3. Installer les dépendances PHP

```bash
composer install
```

### 4. Créer le fichier de configuration locale

```bash
cp .env.example .env
```

Renseignez ensuite les valeurs dans `.env` (voir section [Variables d'environnement](#variables-denvironnement)).

### 5. Configurer le virtual host

Pointez le document root de votre serveur local vers le dossier `public/` du projet.

Exemple avec Laragon : créez un virtual host `vite-et-gourmand.test` pointant vers `…/vite_et_gourmand/public`.

---

## Variables d'environnement

Copiez `.env.example` en `.env` et renseignez chaque variable. **Ne committez jamais le fichier `.env`** (il est exclu par `.gitignore`).

```dotenv
# Base de données MySQL
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=vite_et_gourmand
DB_USER=root
DB_PASS=

# MongoDB
MONGO_URI=mongodb://127.0.0.1:27017
MONGO_DB=vite_et_gourmand_stats

# SMTP (Mailtrap en développement)
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USER=votre_user_mailtrap
MAIL_PASS=votre_pass_mailtrap
MAIL_FROM=contact@viteetgourmand.fr
MAIL_FROM_NAME="Vite & Gourmand"

# URL de l'application
APP_URL=http://vite-et-gourmand.test
APP_ENV=local
```

---

## Base de données

### Base relationnelle (MySQL)

Le schéma complet se trouve dans `sql/`. Exécutez les scripts dans l'ordre suivant :

```bash
# Connexion à MySQL
mysql -u root -p

# Création de la base
CREATE DATABASE vite_et_gourmand CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE vite_et_gourmand;

# Création des tables
SOURCE sql/01_create_tables.sql;

# Insertion des données de test
SOURCE sql/02_seed_data.sql;
```

Le script `01_create_tables.sql` crée l'intégralité des tables du MLD, y compris la table `historique_statut_commande` ajoutée en Sprint 3 pour répondre au besoin de traçabilité des changements de statut (non prévu dans le MCD fourni par le sujet).

**Tables principales :**

| Table | Rôle |
|---|---|
| `utilisateur` | Comptes utilisateurs (clients, employés, admin) |
| `role` | Rôles de l'application |
| `menu` | Catalogue des menus proposés |
| `plat` | Plats composant les menus |
| `allergene` | Allergènes associés aux plats |
| `commande` | Commandes passées par les clients |
| `historique_statut_commande` | Traçabilité horodatée des changements de statut |
| `avis` | Avis clients (soumis à modération) |
| `horaire` | Horaires d'ouverture affichés en pied de page |

Le jeu de données de test (`02_seed_data.sql`) inclut au minimum : 3 rôles, 4 utilisateurs de test (1 admin, 1 employé, 2 clients), 5 menus avec plats et allergènes variés, plusieurs commandes à différents statuts.

### Base NoSQL (MongoDB)

La collection `stats_menus` stocke les statistiques analytiques (nombre de commandes par menu) utilisées dans l'espace Administrateur.

```bash
# Vérifier la connexion
mongosh

# La base et la collection sont créées automatiquement
# au premier lancement du script de synchronisation
php scripts/sync_stats_mongo.php
```

---

## Lancement du projet

Une fois l'installation terminée et les variables d'environnement configurées :

1. Démarrez votre serveur PHP local (Apache/Laragon) en pointant sur `public/`
2. Démarrez le service MySQL
3. Démarrez le service MongoDB
4. Accédez à l'application via `http://vite-et-gourmand.test` (ou l'URL configurée)

### Comptes de test (après exécution du seed)

| Rôle | Email | Mot de passe |
|---|---|---|
| Administrateur | admin@viteetgourmand.fr | `Admin@1234!` |
| Employé | employe@viteetgourmand.fr | `Employe@1234!` |
| Client | client@example.com | `Client@1234!` |

> Ces identifiants sont uniquement destinés à l'environnement de développement local.

---

## Structure MVC détaillée

### Contrôleurs — `app/controllers/`

| Fichier | Rôle | Sprints concernés |
|---|---|---|
| `home_controller.php` | Page d'accueil, avis validés | S2 |
| `auth_controller.php` | Inscription, connexion, reset mot de passe, RGPD | S1 |
| `menu_controller.php` | Catalogue public, filtres dynamiques, détail menu | S2 |
| `commande_controller.php` | Tunnel de commande complet (3 étapes) | S3 |
| `compte_controller.php` | Espace utilisateur : historique, profil, avis | S3 |
| `contact_controller.php` | Formulaire de contact | S2 |
| `employe_controller.php` | CRUD menus/plats/horaires, statuts, modération | S4 |
| `admin_controller.php` | Comptes employés, statistiques, chiffre d'affaires | S4 |
| `api_controller.php` | Endpoints JSON (menus publics, stats admin) | S2, S4 |

### Modèles — `app/models/`

Chaque modèle encapsule les requêtes PDO relatives à son entité. Les requêtes préparées sont utilisées systématiquement.

| Fichier | Entité gérée |
|---|---|
| `menu_model.php` | Lecture, filtres, CRUD des menus |
| `plat_model.php` | Gestion des plats |
| `allergene_model.php` | Allergènes et liaisons many-to-many |
| `commande_model.php` | Commandes, agrégation du CA |
| `historique_statut_model.php` | Traçabilité des changements de statut |
| `utilisateur_model.php` | Comptes, inscription, rôles |
| `role_model.php` | Table des rôles |
| `avis_model.php` | Dépôt et modération des avis |
| `horaire_model.php` | Horaires d'ouverture |
| `token_reset_model.php` | Tokens de réinitialisation de mot de passe |
| `stats_mongo_model.php` | Lecture/écriture des statistiques MongoDB |

### Vues — `app/views/`

Les vues sont organisées par espace fonctionnel :

```
views/
├── partials/        ← Header, footer, alertes (inclus sur toutes les pages)
├── public/          ← Pages accessibles à tous (accueil, menus, contact, mentions légales, CGV)
├── auth/            ← Formulaires d'authentification
├── commande/        ← Tunnel de commande (3 étapes)
├── compte/          ← Espace client connecté
├── employe/         ← Back-office employé
└── admin/           ← Back-office administrateur
```

### Composants transversaux — `app/core/`

| Fichier | Rôle |
|---|---|
| `router.php` | Table de routes et dispatch des requêtes |
| `request.php` | Abstraction de `$_GET` / `$_POST` |
| `response.php` | Helpers `redirect()` et `json()` |
| `auth_middleware.php` | Contrôle d'accès par rôle (RBAC) |
| `csrf.php` | Génération et vérification du token CSRF |
| `helpers.php` | Fonctions utilitaires, échappement XSS |

---

## Fonctionnalités réalisées

Les fonctionnalités ci-dessous correspondent aux User Stories complétées dans le cadre des sprints terminés.

### Sprint 0 — Cadrage et conception

- Backlog produit structuré (Trello)
- Environnement de développement local opérationnel (PHP 8, MySQL, MongoDB)
- Dépôt Git initialisé avec workflow de branches (`main` / `develop`)
- MLD dérivé du MCD fourni — script SQL de création des tables
- Jeu de données de test (`seed.sql`)
- Modèle NoSQL des statistiques (collection MongoDB)
- Diagrammes UML : cas d'utilisation, séquence « Tunnel de commande »
- Wireframes basse-fidélité (desktop + mobile) — 3 pages clés
- Mockups haute-fidélité (desktop + mobile)
- Charte graphique (palette, typographie, export PDF)

### Sprint 1 — Authentification et comptes

- Formulaire d'inscription avec validation client (JavaScript / regex) et validation serveur
- Hachage du mot de passe avec `password_hash()` (bcrypt)
- Envoi du mail de bienvenue via PHPMailer / SMTP
- Connexion avec `password_verify()`, régénération de l'ID de session (`session_regenerate_id()`)
- Middleware de contrôle d'accès par rôle (RBAC)
- Réinitialisation du mot de passe par lien tokenisé (token cryptographique, expiration 1 h)
- Protection CSRF sur tous les formulaires POST
- Cookies de session configurés en `httponly`
- Mention de consentement RGPD avec case obligatoire

### Sprint 2 — Vitrine publique

- Header responsive avec menu burger (Bootstrap 5)
- Footer dynamique affichant les horaires depuis la base de données
- Page d'accueil : présentation de l'entreprise, avis clients validés
- Endpoint API `GET /api/menus` retournant le catalogue en JSON
- Vue globale des menus avec filtres dynamiques sans rechargement (Fetch API + URLSearchParams)
- Vue détaillée d'un menu (galerie, plats, allergènes, conditions)
- Bouton « Commander » avec redirection conditionnelle (connecté / non connecté)
- Formulaire de contact avec envoi par PHPMailer et confirmation asynchrone

### Sprint 3 — Tunnel de commande et espace utilisateur

- Extension du MCD : table `historique_statut_commande` (traçabilité horodatée des statuts)
- Étape 1 — Informations de prestation : pré-remplissage depuis la session, validation de la date
- Calcul des frais de livraison selon la distance via API Nominatim (OpenStreetMap)
- Étape 2 — Sélection du menu et nombre de personnes : calcul du prix en temps réel (JavaScript)
- Règle de remise : -10 % à partir de `minimum + 5` personnes (calcul client ET serveur)
- Étape 3 — Récapitulatif : total détaillé (prix menu, remise, frais de livraison)
- Création de la commande en base via transaction SQL (cohérence commande + historique)
- Email de confirmation de commande
- Espace utilisateur : liste des commandes, détail, modification des informations personnelles
- Annulation et modification de commande (uniquement avant acceptation)
- Frise chronologique de suivi des statuts
- Dépôt d'avis (formulaire de notation 1 à 5 étoiles, statut « en attente » à la création)

### Sprint 4 — Espace employé et espace administrateur

- CRUD complet des menus (avec upload et validation MIME des images)
- CRUD des plats avec gestion des allergènes (relation many-to-many)
- CRUD des horaires d'ouverture
- Workflow de statuts des commandes (accepté → en préparation → en cours de livraison → livré → terminée)
- Chaque changement de statut insère une ligne dans `historique_statut_commande`
- Envoi automatique de l'email « retour de matériel » (délai 10 j ouvrés, pénalité 600 €)
- Filtres de commandes par statut et par client (AJAX, sans rechargement)
- Modération des avis (validation / refus, mise à jour immédiate sur la page d'accueil)
- Création et désactivation de comptes employés
- Notification de création de compte par email (sans mot de passe en clair)
- Héritage des droits Employé pour l'Administrateur
- Script de synchronisation MySQL → MongoDB (`scripts/sync_stats_mongo.php`)
- Endpoint `GET /api/admin/stats-menus` (lecture depuis MongoDB uniquement)
- Graphique comparatif en barres (Chart.js) alimenté par les données NoSQL
- Calcul du chiffre d'affaires par menu avec filtres (menu, plage de dates)

---

## Fonctionnalités en cours de développement

Ces éléments sont planifiés dans le Sprint 5 et sont en cours de réalisation ou de finalisation :

| Fonctionnalité | User Story | État |
|---|---|---|
| Pages légales (mentions légales, CGV) | US-5.1 | En cours |
| Audit de sécurité transversal (SQL, XSS, CSRF, accès) | US-5.2 | En cours |
| Audit et corrections accessibilité RGAA (alt, focus, contrastes, sémantique HTML5) | US-5.3 | En cours |
| Déploiement en ligne (Railway / o2switch) | US-5.4 | En cours |
| Finalisation du README et vérification du dépôt Git | US-5.5 | En cours |
| Manuel d'utilisation PDF (4 rôles, captures d'écran) | US-5.6 | Planifié |
| Documentation technique PDF (choix, MLD, UML, déploiement) | US-5.7 | Planifié |
| Documentation de gestion de projet PDF | US-5.8 | Planifié |

---

## Choix techniques et principes d'architecture

### PHP MVC natif

Le cadre impose un couple SGBD relationnel + NoSQL sans préciser de framework. PHP 8 natif avec PDO a été retenu pour maîtriser pleinement chaque couche de l'application, démontrer la compréhension des mécanismes de base (routage, sessions, requêtes préparées) et éviter une dépendance framework non justifiée dans un contexte ECF.

### Double base de données

MySQL gère les données transactionnelles (utilisateurs, commandes, menus). MongoDB stocke les statistiques de commandes par menu : ces données sont de nature analytique et bénéficient de la flexibilité documentaire. La synchronisation est assurée par un script PHP dédié (`sync_stats_mongo.php`).

### Sécurité

Toutes les interactions avec MySQL utilisent des **requêtes préparées PDO** (aucune concaténation de variable utilisateur dans le SQL). La protection contre le CSRF est appliquée à tous les formulaires POST via un token stocké en session. Les sorties HTML passent systématiquement par `htmlspecialchars()` pour prévenir les injections XSS. Le contrôle d'accès par rôle est centralisé dans `auth_middleware.php`.

### Calcul tarifaire

Le prix final est toujours recalculé côté serveur avant insertion en base, indépendamment des données envoyées par le client. Cette règle garantit que la remise (−10 % à partir de `minimum + 5` personnes) et les frais de livraison (5 € + 0,59 €/km hors Bordeaux) ne peuvent pas être manipulés côté front.

### Extension du MCD fourni

Le MCD du sujet ne prévoit qu'un champ `statut` simple sur la commande, incompatible avec l'exigence de suivi horodaté. La table `historique_statut_commande` a été ajoutée et documentée pour répondre à ce besoin.

---

## Conventions de développement

- **Branches :** `main` (production), `develop` (intégration), branches de fonctionnalité nommées `feature/nom-de-la-fonctionnalite`
- **Commits :** messages en français, au présent, décrivant ce qui est fait (ex. `Ajoute la validation CSRF sur le formulaire de commande`)
- **Nommage des fichiers :** `snake_case` pour les fichiers PHP, `kebab-case` pour les assets CSS/JS
- **Sécurité :** toute variable utilisateur affichée dans une vue est passée dans `htmlspecialchars()` ; toute variable utilisateur dans une requête SQL utilise les marqueurs `?` ou `:param` de PDO
- **Séparation des responsabilités :** aucune requête SQL dans les vues, aucune logique métier dans les templates

---

## Améliorations futures

Ces pistes d'amélioration dépassent le périmètre de l'ECF mais pourraient faire l'objet d'évolutions ultérieures :

- Mise en place de tests unitaires (PHPUnit) sur les modèles et les règles de calcul tarifaire
- Refactoring vers un micro-framework PHP (Slim, Lumen) pour bénéficier d'un routeur plus robuste
- Système de paiement en ligne (Stripe ou PayPlug)
- Interface de sélection des plats à l'intérieur d'un menu (personnalisation de la commande)
- Notifications en temps réel sur les changements de statut (WebSockets ou SSE)
- Export CSV du chiffre d'affaires pour l'administrateur
- Audit RGAA approfondi avec assistance d'un lecteur d'écran (NVDA / JAWS)

---

## Auteur

**Younsi Farouk** — Étudiant en Titre Professionnel Développeur Web et Web Mobile (Studi)

- GitHub : [@pharouqy](https://github.com/pharouqy)
- LinkedIn : [faroukyounsi](https://linkedin.com/in/faroukyounsi)

---

## Licence

Ce projet est réalisé dans un cadre pédagogique (ECF Studi). Il n'est pas destiné à un usage commercial.
