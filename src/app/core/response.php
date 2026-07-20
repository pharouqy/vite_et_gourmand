<?php

declare(strict_types=1);

/**
 * Redirige vers une URL et stoppe l'exécution.
 */
function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

/**
 * Retourne une réponse JSON et stoppe l'exécution.
 * Utilisé par les routes /api/*
 */
function json_response(mixed $data, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/**
 * Charge une vue PHP en lui passant des variables.
 * Utilise extract() pour transformer ['titre' => 'X']
 * en variable $titre disponible dans la vue.
 */
function render(string $vue, array $data = []): void
{
    extract($data, EXTR_SKIP);
    require dirname(__DIR__) . '/views/' . $vue . '.php';
}
