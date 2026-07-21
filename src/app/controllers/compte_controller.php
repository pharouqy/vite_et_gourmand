<?php

declare(strict_types=1);

function compte_mes_commandes(): void
{
    require_auth();
    render('compte/mes-commandes', ['titre_page' => 'Mes commandes']);
}

// Squelettes — implémentés en Sprint 3
function compte_commande_detail(): void
{
    require_auth();
    echo "Détail commande — Sprint 3";
}
function compte_annuler(): void
{
    require_auth();
    redirect('/compte/commandes');
}
function compte_modifier_form(): void
{
    require_auth();
    echo "Modifier commande — Sprint 3";
}
function compte_modifier_traiter(): void
{
    require_auth();
    redirect('/compte/commandes');
}
function compte_profil(): void
{
    require_auth();
    echo "Profil — Sprint 3";
}
function compte_profil_traiter(): void
{
    require_auth();
    redirect('/compte/profil');
}
function compte_avis_form(): void
{
    require_auth();
    echo "Avis — Sprint 3";
}
function compte_avis_traiter(): void
{
    require_auth();
    redirect('/compte/commandes');
}
