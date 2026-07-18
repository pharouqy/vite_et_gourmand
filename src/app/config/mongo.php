<?php
/**
 * Connexion MongoDB
 * Utilise le driver officiel mongodb/mongodb installé via Composer
 */

declare(strict_types=1);

function getMongoClient(): MongoDB\Client
{
    // Récupération de l'URI depuis la variable d'environnement
    $uri = getenv('MONGO_URI');

    if (!$uri) {
        throw new RuntimeException('Variable MONGO_URI non définie.');
    }

    return new MongoDB\Client($uri);
}

function getMongoDB(): MongoDB\Database
{
    return getMongoClient()->selectDatabase('vite_et_gourmand');
}

function getMongoCollection(string $collection): MongoDB\Collection
{
    return getMongoDB()->selectCollection($collection);
}