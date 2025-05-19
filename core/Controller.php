<?php
/**
 * Classe de base Controller
 * Sert de classe mère pour tous les contrôleurs de l'application
 * Gère le rendu des vues et les redirections
 */
class Controller {
    /**
     * Charge et affiche une vue
     * 
     * @param string $view Chemin de la vue à charger
     * @param array $data Données à passer à la vue
     * @return void
     */
    protected function render($view, $data = []) {
        // Extraction des données pour les rendre disponibles dans la vue
        extract($data);
        
        // Chemin de base des vues
        $viewPath = dirname(__DIR__) . '/app/views/';
        
        // Inclusion du header
        require_once APPROOT . '/includes/header.php';
        
        // Inclusion de la vue demandée
        if (file_exists($viewPath . $view . '.php')) {
            require_once $viewPath . $view . '.php';
        } else {
            // Gestion d'erreur si la vue n'existe pas
            die("La vue '{$view}' n'existe pas.");
        }
        
        // Inclusion du footer
        require_once APPROOT . '/includes/footer.php';
    }
    
    /**
     * Redirige vers une URL
     * 
     * @param string $url URL de redirection
     * @return void
     */
    protected function redirect($url) {
        header('Location: ' . $url);
        exit;
    }
    
    /**
     * Vérifie si l'utilisateur est connecté
     * 
     * @return bool True si l'utilisateur est connecté, false sinon
     */
    protected function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    /**
     * Vérifie si l'utilisateur a un rôle spécifique
     * 
     * @param string|array $roles Rôle(s) autorisé(s)
     * @return bool True si l'utilisateur a le rôle requis, false sinon
     */
    protected function hasRole($roles) {
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        // Conversion en tableau si un seul rôle est passé
        if (!is_array($roles)) {
            $roles = [$roles];
        }
        
        return in_array($_SESSION['user_role'], $roles);
    }
    
    /**
     * Génère un token CSRF et le stocke en session
     * 
     * @return string Token CSRF généré
     */
    protected function generateCSRFToken() {
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
    protected function validateCSRFToken($token) {
        if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
            return false;
        }
        return true;
    }
    
    /**
     * Alias pour validateCSRFToken pour maintenir la compatibilité
     * 
     * @param string $token Token CSRF à vérifier
     * @return bool True si le token est valide, false sinon
     */
    protected function verifyCsrfToken($token) {
        return $this->validateCSRFToken($token);
    }
    
    /**
     * Affiche un message flash
     * 
     * @param string $type Type de message (success, error, warning, info)
     * @param string $message Contenu du message
     * @return void
     */
    protected function setFlash($type, $message) {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message
        ];
    }
    
    /**
     * Récupère les données POST filtrées
     * 
     * @return array Données POST filtrées
     */
    protected function getPostData() {
        return filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);
    }
    
    /**
     * Récupère les données GET filtrées
     * 
     * @return array Données GET filtrées
     */
    protected function getQueryData() {
        return filter_input_array(INPUT_GET, FILTER_SANITIZE_SPECIAL_CHARS);
    }
}