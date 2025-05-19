<?php
require_once __DIR__ . '/../config/config.php';

try {
    // Connexion à MySQL sans sélectionner de base de données
    $pdo = new PDO(
        "mysql:host=" . DB_HOST,
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    // Création de la base de données si elle n'existe pas
    $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    
    // Sélection de la base de données
    $pdo->exec("USE " . DB_NAME);
    
    // Lecture du fichier SQL
    $sql = file_get_contents(__DIR__ . '/../sql/poultry_batches.sql');
    
    // Exécution des requêtes SQL
    $pdo->exec($sql);
    
    echo "La table poultry_batches a été créée avec succès !";
    
} catch (PDOException $e) {
    die("Erreur lors de la création de la table poultry_batches : " . $e->getMessage());
} 