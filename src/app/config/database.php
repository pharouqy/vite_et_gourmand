<?php

declare(strict_types=1);

function getPDO(): PDO
{
    static $pdo = null;

    if ($pdo !== null) {
        return $pdo;
    }

    $host     = getenv('MYSQL_HOST')     ?: 'db';
    $dbname   = getenv('MYSQL_DATABASE') ?: 'vite_et_gourmand';
    $user     = getenv('MYSQL_USER')     ?: 'farouk';
    $password = getenv('MYSQL_PASSWORD') ?: 'farouk';

    $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";

    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);

    return $pdo;
}