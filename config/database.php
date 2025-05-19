<?php
/**
 * Configuration de la base de données
 */

// Chargement du fichier de configuration principal
require_once __DIR__ . '/config.php';

// Vérification de la connexion à la base de données
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    // Si la base de données n'existe pas, on la crée
    if ($e->getCode() == 1049) {
        try {
            $pdo = new PDO(
                "mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET,
                DB_USER,
                DB_PASS
            );
            
            // Création de la base de données
            $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET " . DB_CHARSET);
            
            // Sélection de la base de données
            $pdo->exec("USE " . DB_NAME);
            
            // Chargement du schéma de la base de données
            $sql = file_get_contents(__DIR__ . '/../sql/aviculture.sql');
            $pdo->exec($sql);
            
            error_log("Base de données créée avec succès");
        } catch (PDOException $e2) {
            error_log("Erreur lors de la création de la base de données : " . $e2->getMessage());
            throw new Exception("Impossible de créer la base de données");
        }
    } else {
        error_log("Erreur de connexion à la base de données : " . $e->getMessage());
        throw new Exception("Impossible de se connecter à la base de données");
    }
} 