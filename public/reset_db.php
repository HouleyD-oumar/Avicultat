<?php
require_once dirname(__DIR__) . '/config/config.php';

try {
    // Connexion à MySQL sans sélectionner de base de données
    $pdo = new PDO(
        "mysql:host=" . DB_HOST,
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    // Supprimer la base de données si elle existe
    $pdo->exec("DROP DATABASE IF EXISTS " . DB_NAME);
    echo "Base de données supprimée si elle existait.\n";
    
    // Créer la base de données
    $pdo->exec("CREATE DATABASE " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Base de données créée.\n";
    
    // Sélectionner la base de données
    $pdo->exec("USE " . DB_NAME);
    
    // Lire et exécuter le script SQL
    $sql = file_get_contents(dirname(__DIR__) . '/sql/aviculture.sql');
    $pdo->exec($sql);
    
    echo "Base de données réinitialisée avec succès.\n";
    
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
} 