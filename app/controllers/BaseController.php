<?php
class BaseController {
    protected $model;
    protected $view;
    protected $data = [];
    
    public function __construct() {
        // Initialisation commune
        $this->data['title'] = 'Avicultat';
        $this->data['user'] = $_SESSION['user'] ?? null;
    }
    
    // Méthode pour charger une vue
    protected function view($view, $data = []) {
        $this->data = array_merge($this->data, $data);
        require_once APPROOT . '/views/' . $view . '.php';
    }
    
    // Méthode pour rediriger
    protected function redirect($url) {
        header('Location: ' . APP_URL . '/' . $url);
        exit;
    }
    
    // Méthode pour vérifier l'authentification
    protected function requireAuth() {
        if (!isset($_SESSION['user'])) {
            $this->redirect('users/login');
        }
    }
    
    // Méthode pour vérifier les rôles
    protected function requireRole($roles) {
        $this->requireAuth();
        if (!in_array($_SESSION['user']['role'], (array)$roles)) {
            $this->redirect('errors/unauthorized');
        }
    }
    
    // Méthode pour générer un token CSRF
    protected function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    // Méthode pour vérifier le token CSRF
    protected function verifyCSRFToken($token) {
        if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
            $this->redirect('errors/csrf');
        }
    }
    
    // Méthode pour gérer les messages flash
    protected function setFlash($type, $message) {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message
        ];
    }
} 