<?php
/**
 * Contrôleur UserController
 * Gère les opérations liées aux utilisateurs (inscription, connexion, profil)
 */
class UserController extends Controller {
    private $userModel;
    
    /**
     * Constructeur - initialise le modèle User
     */
    public function __construct() {
        $this->userModel = new User();
    }
    
    /**
     * Affiche le formulaire d'inscription
     */
    public function register() {
        // Générer un token CSRF
        $data = [
            'pageTitle' => 'Inscription',
            'csrf_token' => $this->generateCsrfToken()
        ];
        
        $this->render('users/register', $data);
    }
    
    /**
     * Traite le formulaire d'inscription
     */
    public function processRegister() {
        // Vérifier si la requête est de type POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(APP_URL . '/user/register');
        }
        
        // Vérifier le token CSRF
        if (!$this->verifyCsrfToken($_POST['csrf_token'])) {
            $this->setFlash('danger', 'Erreur de sécurité. Veuillez réessayer.');
            $this->redirect(APP_URL . '/user/register');
        }
        
        // Récupérer et filtrer les données du formulaire
        $data = $this->getPostData();
        
        // Validation des données
        $errors = [];
        
        if (empty($data['nom'])) {
            $errors[] = 'Le nom est requis';
        }
        
        if (empty($data['prenom'])) {
            $errors[] = 'Le prénom est requis';
        }
        
        if (empty($data['email'])) {
            $errors[] = 'L\'email est requis';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'L\'email n\'est pas valide';
        }
        
        if (empty($data['mot_de_passe'])) {
            $errors[] = 'Le mot de passe est requis';
        } elseif (strlen($data['mot_de_passe']) < 6) {
            $errors[] = 'Le mot de passe doit contenir au moins 6 caractères';
        }
        
        if ($data['mot_de_passe'] !== $data['confirmer_mot_de_passe']) {
            $errors[] = 'Les mots de passe ne correspondent pas';
        }
        
        // S'il y a des erreurs, afficher le formulaire avec les erreurs
        if (!empty($errors)) {
            $data = [
                'pageTitle' => 'Inscription',
                'errors' => $errors,
                'input' => $data,
                'csrf_token' => $this->generateCsrfToken()
            ];
            
            $this->render('users/register', $data);
            return;
        }
        
        // Supprimer les champs inutiles
        unset($data['csrf_token']);
        unset($data['confirmer_mot_de_passe']);
        
        // Enregistrer l'utilisateur
        $userId = $this->userModel->register($data);
        
        if ($userId) {
            $this->setFlash('success', 'Inscription réussie ! Vous pouvez maintenant vous connecter.');
            $this->redirect(APP_URL . '/user/login');
        } else {
            $this->setFlash('danger', 'Cette adresse email est déjà utilisée.');
            $this->redirect(APP_URL . '/user/register');
        }
    }
    
    /**
     * Affiche le formulaire de connexion
     */
    public function login() {
        // Si l'utilisateur est déjà connecté, rediriger vers la page d'accueil
        if ($this->isLoggedIn()) {
            $this->redirect(APP_URL);
        }
        
        // Générer un token CSRF
        $data = [
            'pageTitle' => 'Connexion',
            'csrf_token' => $this->generateCsrfToken()
        ];
        
        $this->render('users/login', $data);
    }
    
    /**
     * Traite le formulaire de connexion
     */
    public function processLogin() {
        // Vérifier si la requête est de type POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(APP_URL . '/user/login');
        }
        
        // Vérifier le token CSRF
        if (!$this->verifyCsrfToken($_POST['csrf_token'])) {
            $this->setFlash('danger', 'Erreur de sécurité. Veuillez réessayer.');
            $this->redirect(APP_URL . '/user/login');
        }
        
        // Récupérer et filtrer les données du formulaire
        $data = $this->getPostData();
        
        // Validation des données
        $errors = [];
        
        if (empty($data['email'])) {
            $errors[] = 'L\'email est requis';
        }
        
        if (empty($data['mot_de_passe'])) {
            $errors[] = 'Le mot de passe est requis';
        }
        
        // S'il y a des erreurs, afficher le formulaire avec les erreurs
        if (!empty($errors)) {
            $data = [
                'pageTitle' => 'Connexion',
                'errors' => $errors,
                'input' => $data,
                'csrf_token' => $this->generateCsrfToken()
            ];
            
            $this->render('users/login', $data);
            return;
        }
        
        // Authentifier l'utilisateur
        $user = $this->userModel->login($data['email'], $data['mot_de_passe']);
        
        if ($user) {
            // Créer la session utilisateur
            $_SESSION['user_id'] = $user['id_user'];
            $_SESSION['user_name'] = $user['nom'] . ' ' . $user['prenom'];
            $_SESSION['user_role'] = $user['role'];
            
            $this->setFlash('success', 'Connexion réussie !');
            $this->redirect(APP_URL);
        } else {
            $this->setFlash('danger', 'Email ou mot de passe incorrect.');
            $this->redirect(APP_URL . '/user/login');
        }
    }
    
    /**
     * Déconnecte l'utilisateur
     */
    public function logout() {
        // Détruire la session
        session_destroy();
        
        // Rediriger vers la page de connexion
        $this->redirect(APP_URL . '/user/login');
    }
    
    /**
     * Affiche le profil de l'utilisateur
     */
    public function profile() {
        // Vérifier si l'utilisateur est connecté
        if (!$this->isLoggedIn()) {
            $this->setFlash('warning', 'Vous devez être connecté pour accéder à cette page.');
            $this->redirect(APP_URL . '/user/login');
        }
        
        // Récupérer les données de l'utilisateur
        $user = $this->userModel->findById($_SESSION['user_id']);
        
        if (!$user) {
            $this->setFlash('danger', 'Utilisateur non trouvé.');
            $this->redirect(APP_URL);
        }
        
        // Générer un token CSRF
        $data = [
            'pageTitle' => 'Mon Profil',
            'user' => $user,
            'csrf_token' => $this->generateCsrfToken()
        ];
        
        $this->render('users/profile', $data);
    }
    
    /**
     * Traite la mise à jour du profil
     */
    public function updateProfile() {
        // Vérifier si l'utilisateur est connecté
        if (!$this->isLoggedIn()) {
            $this->setFlash('warning', 'Vous devez être connecté pour accéder à cette page.');
            $this->redirect(APP_URL . '/user/login');
        }
        
        // Vérifier si la requête est de type POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(APP_URL . '/user/profile');
        }
        
        // Vérifier le token CSRF
        if (!$this->verifyCsrfToken($_POST['csrf_token'])) {
            $this->setFlash('danger', 'Erreur de sécurité. Veuillez réessayer.');
            $this->redirect(APP_URL . '/user/profile');
        }
        
        // Récupérer et filtrer les données du formulaire
        $data = $this->getPostData();
        
        // Validation des données
        $errors = [];
        
        if (empty($data['nom'])) {
            $errors[] = 'Le nom est requis';
        }
        
        if (empty($data['prenom'])) {
            $errors[] = 'Le prénom est requis';
        }
        
        if (empty($data['email'])) {
            $errors[] = 'L\'email est requis';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'L\'email n\'est pas valide';
        }
        
        // Vérifier si les mots de passe correspondent (si fournis)
        if (!empty($data['mot_de_passe'])) {
            if (strlen($data['mot_de_passe']) < 6) {
                $errors[] = 'Le mot de passe doit contenir au moins 6 caractères';
            }
            
            if ($data['mot_de_passe'] !== $data['confirmer_mot_de_passe']) {
                $errors[] = 'Les mots de passe ne correspondent pas';
            }
        }
        
        // S'il y a des erreurs, afficher le profil avec les erreurs
        if (!empty($errors)) {
            $user = $this->userModel->findById($_SESSION['user_id']);
            
            $data = [
                'pageTitle' => 'Mon Profil',
                'user' => $user,
                'errors' => $errors,
                'csrf_token' => $this->generateCsrfToken()
            ];
            
            $this->render('users/profile', $data);
            return;
        }
        
        // Supprimer les champs inutiles
        unset($data['csrf_token']);
        unset($data['confirmer_mot_de_passe']);
        
        // Mettre à jour le profil
        $success = $this->userModel->updateProfile($_SESSION['user_id'], $data);
        
        if ($success) {
            // Mettre à jour le nom dans la session
            $_SESSION['user_name'] = $data['prenom'] . ' ' . $data['nom'];
            
            $this->setFlash('success', 'Profil mis à jour avec succès !');
        } else {
            $this->setFlash('danger', 'Erreur lors de la mise à jour du profil.');
        }
        
        $this->redirect(APP_URL . '/user/profile');
    }
}