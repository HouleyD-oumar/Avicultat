<?php
/**
 * Fonctions utilitaires pour l'application
 */

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
 * Formate une date pour l'affichage
 * 
 * @param string $date La date à formater
 * @param string $format Le format de sortie (par défaut: d/m/Y)
 * @return string La date formatée
 */
function formatDate($date, $format = 'd/m/Y') {
    if (empty($date)) return '';
    $d = new DateTime($date);
    return $d->format($format);
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

/**
 * Vérifie si un token CSRF est valide
 * 
 * @param string $token Le token à vérifier
 * @return bool True si le token est valide, false sinon
 */
function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Génère un nouveau token CSRF
 * 
 * @return string Le token généré
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Définit un message flash
 * 
 * @param string $type Le type de message (success, error, warning, info)
 * @param string $message Le message à afficher
 */
function setFlash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Affiche et supprime le message flash
 */
function flash() {
    if (isset($_SESSION['flash'])) {
        $type = $_SESSION['flash']['type'];
        $message = $_SESSION['flash']['message'];
        unset($_SESSION['flash']);
        
        echo '<div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">';
        echo $message;
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
    }
} 