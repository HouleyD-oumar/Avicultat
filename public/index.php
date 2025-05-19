<?php
/**
 * Point d'entrée de l'application
 */

// Démarrage de la session
session_start();

// Définition du chemin de base
define('BASE_PATH', dirname(__DIR__));

// Chargement des fichiers de configuration
require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/config/database.php';

// Chargement des classes de base
require_once BASE_PATH . '/core/Database.php';
require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/core/Model.php';
require_once BASE_PATH . '/core/App.php';

// Chargement des modèles de base
require_once BASE_PATH . '/app/models/BaseModel.php';

// Chargement des helpers
require_once BASE_PATH . '/app/helpers/functions.php';

// Initialisation et exécution de l'application
App::run();