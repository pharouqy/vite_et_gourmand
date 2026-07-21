<?php

declare(strict_types=1);

function admin_statistiques(): void
{
    require_role(ROLE_ADMIN);
    echo "<h1>Espace admin — Sprint 4</h1><a href='/deconnexion'>Déconnexion</a>";
}

// Squelettes Sprint 4
function admin_employes_liste(): void
{
    require_role(ROLE_ADMIN);
    echo "Employés";
}
function admin_employe_creer_form(): void
{
    require_role(ROLE_ADMIN);
    echo "Créer employé";
}
function admin_employe_creer_traiter(): void
{
    require_role(ROLE_ADMIN);
    redirect('/admin/employes');
}
function admin_employe_desactiver(): void
{
    require_role(ROLE_ADMIN);
    redirect('/admin/employes');
}
function admin_ca(): void
{
    require_role(ROLE_ADMIN);
    echo "Chiffre d'affaires";
}
