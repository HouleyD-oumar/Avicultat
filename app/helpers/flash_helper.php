<?php
/**
 * Fonction pour afficher les messages flash
 */
function flash() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'] ?? 'info';
        
        // Suppression du message après affichage
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
        
        // Affichage du message avec la classe Bootstrap appropriée
        echo '<div class="alert alert-' . htmlspecialchars($type) . ' alert-dismissible fade show" role="alert">';
        echo htmlspecialchars($message);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
    }
}

/**
 * Fonction pour définir un message flash
 */
function setFlash($message, $type = 'info') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
} 