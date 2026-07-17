#!/bin/bash
set -e

ROOT="vite-et-gourmand"

echo "Création de la structure MVC : $ROOT"
echo "Creation de la structure MVC : $ROOT"

# ── Dossiers ──────────────────────────────────────────────
mkdir -p "$ROOT"
mkdir -p "$ROOT/public/assets/css"
mkdir -p "$ROOT/public/assets/js"
mkdir -p "$ROOT/public/assets/img"
mkdir -p "$ROOT/public/uploads/menus"
mkdir -p "$ROOT/app/config"
mkdir -p "$ROOT/app/core"
mkdir -p "$ROOT/app/controllers"
mkdir -p "$ROOT/app/models"
mkdir -p "$ROOT/app/views/partials"
mkdir -p "$ROOT/app/views/public"
mkdir -p "$ROOT/app/views/auth"
mkdir -p "$ROOT/app/views/commande"
mkdir -p "$ROOT/app/views/compte"
mkdir -p "$ROOT/app/views/employe"
mkdir -p "$ROOT/app/views/admin"
mkdir -p "$ROOT/app/mail_templates"
mkdir -p "$ROOT/sql"
mkdir -p "$ROOT/scripts"
mkdir -p "$ROOT/docs"
mkdir -p "$ROOT/vendor"

# ── Fichiers racine ───────────────────────────────────────
touch "$ROOT/.env.example"
touch "$ROOT/.gitignore"
touch "$ROOT/composer.json"
touch "$ROOT/README.md"

# ── public ────────────────────────────────────────────────
touch "$ROOT/public/index.php"
touch "$ROOT/public/.htaccess"
touch "$ROOT/public/assets/css/style.css"
touch "$ROOT/public/assets/css/responsive.css"
touch "$ROOT/public/assets/js/filtres-menus.js"
touch "$ROOT/public/assets/js/commande.js"
touch "$ROOT/public/assets/js/validation-form.js"
touch "$ROOT/public/assets/js/avis.js"

# ── app/config ────────────────────────────────────────────
touch "$ROOT/app/config/database.php"
touch "$ROOT/app/config/mongo.php"
touch "$ROOT/app/config/mail.php"
touch "$ROOT/app/config/constants.php"

# ── app/core ──────────────────────────────────────────────
touch "$ROOT/app/core/router.php"
touch "$ROOT/app/core/request.php"
touch "$ROOT/app/core/response.php"
touch "$ROOT/app/core/auth_middleware.php"
touch "$ROOT/app/core/csrf.php"
touch "$ROOT/app/core/helpers.php"

# ── app/controllers ───────────────────────────────────────
touch "$ROOT/app/controllers/home_controller.php"
touch "$ROOT/app/controllers/auth_controller.php"
touch "$ROOT/app/controllers/menu_controller.php"
touch "$ROOT/app/controllers/commande_controller.php"
touch "$ROOT/app/controllers/compte_controller.php"
touch "$ROOT/app/controllers/contact_controller.php"
touch "$ROOT/app/controllers/employe_controller.php"
touch "$ROOT/app/controllers/admin_controller.php"
touch "$ROOT/app/controllers/api_controller.php"

# ── app/models ────────────────────────────────────────────
touch "$ROOT/app/models/menu_model.php"
touch "$ROOT/app/models/plat_model.php"
touch "$ROOT/app/models/allergene_model.php"
touch "$ROOT/app/models/commande_model.php"
touch "$ROOT/app/models/historique_statut_model.php"
touch "$ROOT/app/models/utilisateur_model.php"
touch "$ROOT/app/models/role_model.php"
touch "$ROOT/app/models/avis_model.php"
touch "$ROOT/app/models/horaire_model.php"
touch "$ROOT/app/models/token_reset_model.php"
touch "$ROOT/app/models/stats_mongo_model.php"

# ── app/views/partials ────────────────────────────────────
touch "$ROOT/app/views/partials/header.php"
touch "$ROOT/app/views/partials/footer.php"
touch "$ROOT/app/views/partials/alertes.php"

# ── app/views/public ──────────────────────────────────────
touch "$ROOT/app/views/public/accueil.php"
touch "$ROOT/app/views/public/menus-liste.php"
touch "$ROOT/app/views/public/menu-detail.php"
touch "$ROOT/app/views/public/contact.php"
touch "$ROOT/app/views/public/mentions-legales.php"
touch "$ROOT/app/views/public/cgv.php"

# ── app/views/auth ────────────────────────────────────────
touch "$ROOT/app/views/auth/inscription.php"
touch "$ROOT/app/views/auth/connexion.php"
touch "$ROOT/app/views/auth/mot-de-passe-oublie.php"
touch "$ROOT/app/views/auth/reinitialisation.php"

# ── app/views/commande ────────────────────────────────────
touch "$ROOT/app/views/commande/informations-prestation.php"
touch "$ROOT/app/views/commande/choix-menu.php"
touch "$ROOT/app/views/commande/recapitulatif.php"

# ── app/views/compte ──────────────────────────────────────
touch "$ROOT/app/views/compte/mes-commandes.php"
touch "$ROOT/app/views/compte/commande-detail.php"
touch "$ROOT/app/views/compte/profil.php"
touch "$ROOT/app/views/compte/deposer-avis.php"

# ── app/views/employe ─────────────────────────────────────
touch "$ROOT/app/views/employe/menus.php"
touch "$ROOT/app/views/employe/plats.php"
touch "$ROOT/app/views/employe/horaires.php"
touch "$ROOT/app/views/employe/commandes.php"
touch "$ROOT/app/views/employe/moderation-avis.php"

# ── app/views/admin ───────────────────────────────────────
touch "$ROOT/app/views/admin/employes.php"
touch "$ROOT/app/views/admin/statistiques.php"
touch "$ROOT/app/views/admin/chiffre-affaires.php"

# ── app/mail_templates ────────────────────────────────────
touch "$ROOT/app/mail_templates/bienvenue.php"
touch "$ROOT/app/mail_templates/confirmation_commande.php"
touch "$ROOT/app/mail_templates/reinitialisation_mdp.php"
touch "$ROOT/app/mail_templates/compte_employe_cree.php"
touch "$ROOT/app/mail_templates/retour_materiel.php"
touch "$ROOT/app/mail_templates/invitation_avis.php"

# ── sql ───────────────────────────────────────────────────
touch "$ROOT/sql/01_create_tables.sql"
touch "$ROOT/sql/02_seed_data.sql"

# ── scripts ───────────────────────────────────────────────
touch "$ROOT/scripts/sync_stats_mongo.php"

# ── docs ──────────────────────────────────────────────────
touch "$ROOT/docs/manuel-utilisation.pdf"
touch "$ROOT/docs/charte-graphique.pdf"
touch "$ROOT/docs/documentation-technique.pdf"
touch "$ROOT/docs/documentation-gestion-projet.pdf"
echo "Structure creee avec succes !"