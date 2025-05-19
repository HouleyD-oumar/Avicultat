<?php
require_once dirname(__DIR__) . '/config/config.php';

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    // Vérifier la structure de la table users
    $stmt = $pdo->query("DESCRIBE users");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Structure de la table users :\n";
    print_r($columns);
    
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
} 