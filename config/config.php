<?php
/**
 * Configuration générale de l'application
 */

// Configuration de la base de données
if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
if (!defined('DB_NAME')) define('DB_NAME', 'aviculture_db');
if (!defined('DB_USER')) define('DB_USER', 'root');
if (!defined('DB_PASS')) define('DB_PASS', '');
if (!defined('DB_CHARSET')) define('DB_CHARSET', 'utf8mb4');

// Configuration de l'application
define('APP_NAME', 'Avicultat');
define('APP_URL', 'http://localhost/php/Avicultat');
define('APP_ROOT', dirname(dirname(__FILE__)));
define('APPROOT', dirname(dirname(__FILE__))); // Chemin vers le dossier racine de l'application
define('APP_ENV', 'development');

// Configuration des sessions
define('SESSION_LIFETIME', 120);
define('SESSION_NAME', 'avicultat_session');

// Configuration des logs
define('LOG_PATH', APP_ROOT . '/logs');
define('ERROR_LOG', LOG_PATH . '/error.log');
define('ACCESS_LOG', LOG_PATH . '/access.log');

// Configuration des uploads
define('UPLOAD_PATH', APP_ROOT . '/public/uploads');
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'pdf']);

// Configuration de la sécurité
define('CSRF_TOKEN_NAME', 'csrf_token');
define('PASSWORD_MIN_LENGTH', 8);
define('LOGIN_MAX_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 15); // minutes

// Paramètres de sécurité
define('HASH_COST', 10); // Coût du hashage bcrypt

// Fuseau horaire
date_default_timezone_set('UTC');

// Affichage des erreurs (à désactiver en production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);