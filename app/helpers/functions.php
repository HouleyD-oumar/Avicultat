<?php
/**
 * Fichier de fonctions d'aide
 * Contient les fonctions utilitaires utilisées dans l'application
 */

/**
 * Affiche un message flash s'il existe
 * 
 * @return void
 */
function flash() {
    if (isset($_SESSION['flash'])) {
        $type = $_SESSION['flash']['type'];
        $message = $_SESSION['flash']['message'];
        
        echo '<div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">';
        echo $message;
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>';
        echo '</div>';
        
        // Supprimer le message après l'avoir affiché
        unset($_SESSION['flash']);
    }
}

/**
 * Définit un message flash à afficher sur la prochaine page
 * 
 * @param string $type Type de message (success, danger, warning, info)
 * @param string $message Contenu du message
 * @return void
 */
function setFlash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Formate une date pour l'affichage
 * 
 * @param string $date Date à formater (format Y-m-d)
 * @param string|bool $format Format de sortie (par défaut: d/m/Y) ou true pour inclure l'heure
 * @return string Date formatée
 */
function formatDate($date, $format = 'd/m/Y') {
    if (empty($date)) {
        return 'N/A';
    }
    
    // Si $format est un booléen true, utiliser le format avec heure
    if ($format === true) {
        $format = 'd/m/Y à H:i';
    }
    
    $d = new DateTime($date);
    return $d->format($format);
}

/**
 * Génère un token CSRF
 * 
 * @return string Token CSRF
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Vérifie si le token CSRF est valide
 * 
 * @param string $token Token CSRF à vérifier
 * @return bool True si le token est valide, false sinon
 */
function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Redirige vers une URL
 * 
 * @param string $url URL de redirection
 * @return void
 */
function redirect($url) {
    header('Location: ' . $url);
    exit;
}

/**
 * Vérifie si l'utilisateur est connecté
 * 
 * @return bool True si l'utilisateur est connecté, false sinon
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Nettoie une chaîne de caractères pour éviter les injections XSS
 * 
 * @param string $data Données à nettoyer
 * @return string Données nettoyées
 */
function sanitize($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/**
 * Valide une date au format Y-m-d
 * 
 * @param string $date La date à valider
 * @return bool True si la date est valide, false sinon
 */
function validateDate($date) {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

/**
 * Génère une classe de badge Bootstrap en fonction du statut
 * 
 * @param string $status Le statut à évaluer
 * @return string La classe CSS du badge
 */
function getStatusBadgeClass($status) {
    switch (strtolower($status)) {
        case 'actif':
            return 'success';
        case 'vendu':
            return 'info';
        case 'perte totale':
            return 'danger';
        default:
            return 'secondary';
    }
}