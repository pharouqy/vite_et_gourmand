<?php

declare(strict_types=1);

function employe_commandes_liste(): void
{
    require_employe_ou_admin();
    echo "<h1>Espace employé — Sprint 4</h1><a href='/deconnexion'>Déconnexion</a>";
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
function employe_commande_detail(): void
{
    require_employe_ou_admin();
    echo "Détail commande";
}
function employe_commande_statut(): void
{
    require_employe_ou_admin();
    redirect('/employe/commandes');
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
