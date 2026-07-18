<?php

echo "Hello, World!";
echo "This is a sample PHP file for the Vite et Gourmand project.";
    // Récupération de l'URI depuis la variable d'environnement
    $uri = getenv('MONGO_URI');

    echo "MongoDB URI: " . ($uri ?: 'Not set') . "\n";
?>