<?php

declare(strict_types=1);

function home_index(): void
{
    render('public/accueil', ['titre_page' => 'Accueil']);
}

function home_mentions_legales(): void
{
    render('public/mentions-legales', ['titre_page' => 'Mentions légales']);
}

function home_cgv(): void
{
    render('public/cgv', ['titre_page' => 'CGV']);
}
