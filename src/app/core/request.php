<?php

declare(strict_types=1);

/**
 * Récupère et assainit un paramètre GET.
 */
function get_param(string $key, mixed $default = null): mixed
{
    return isset($_GET[$key]) ? trim($_GET[$key]) : $default;
}

/**
 * Récupère et assainit un paramètre POST.
 */
function post_param(string $key, mixed $default = null): mixed
{
    return isset($_POST[$key]) ? trim($_POST[$key]) : $default;
}

/**
 * Récupère la valeur brute d'un POST
 * (pour les cas où trim() serait indésirable).
 */
function post_raw(string $key, mixed $default = null): mixed
{
    return $_POST[$key] ?? $default;
}

/**
 * Retourne true si la requête courante est de type POST.
 */
function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST';
}

/**
 * Retourne true si la requête attend du JSON (AJAX).
 */
function is_ajax(): bool
{
    return (
        ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest'
        || str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')
    );
}
