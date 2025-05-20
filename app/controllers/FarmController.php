<?php
/**
 * Contrôleur FarmController
 * Gère les opérations CRUD pour les fermes avicoles
 */
class FarmController extends Controller {
    private $farmModel;
    private $poultryBatchModel;
    
    /**
     * Constructeur - initialise les modèles nécessaires
     */
    public function __construct() {
        // Vérifier si l'utilisateur est connecté
        if (!$this->isLoggedIn()) {
            redirect('user/login');
        }
        
        // Vérifier si l'utilisateur a les droits nécessaires (éleveur ou admin)
        if (!in_array($_SESSION['user_role'], ['eleveur', 'admin'])) {
            setFlash('error', 'Vous n\'avez pas les droits nécessaires pour accéder à cette section');
            redirect('home');
        }
        
        // Charger les modèles nécessaires
        require_once dirname(__DIR__) . '/models/Farm.php';
        $this->farmModel = new Farm();
        
        // Charger le modèle PoultryBatch pour compter les lots
        require_once dirname(__DIR__) . '/models/PoultryBatch.php';
        $this->poultryBatchModel = new PoultryBatch();
    }
    
    /**
     * Affiche la liste des fermes de l'utilisateur connecté
     */
    public function index() {
        // Récupérer les fermes de l'utilisateur connecté
        $farms = $this->farmModel->findByUserId($_SESSION['user_id']);
        
        // Pour chaque ferme, récupérer le nombre de lots actifs
        foreach ($farms as &$farm) {
            $farm['active_batches'] = $this->farmModel->countActiveBatches($farm['id_ferme']);
        }
        
        $data = [
            'title' => 'Mes Fermes',
            'farms' => $farms
        ];
        
        $this->render('farms/index', $data);
    }
    
    /**
     * Affiche le formulaire d'ajout d'une ferme
     */
    public function add() {
        $data = [
            'title' => 'Ajouter une ferme',
            'nom_ferme' => '',
            'localisation' => '',
            'date_creation' => date('Y-m-d'),
            'errors' => []
        ];
        
        $this->render('farms/form', $data);
    }
    
    /**
     * Traite la soumission du formulaire d'ajout d'une ferme
     */
    public function create() {
        // Vérifier si la requête est de type POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('farm');
        }
        
        // Vérifier le token CSRF
        $csrf_token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
        if (!validateCSRFToken($csrf_token)) {
            setFlash('error', 'Erreur de sécurité. Veuillez réessayer.');
            redirect('farm/add');
        }
        
        // Récupérer et nettoyer les données du formulaire
        $data = [
            'title' => 'Ajouter une ferme',
            'nom_ferme' => trim(isset($_POST['nom_ferme']) ? $_POST['nom_ferme'] : ''),
            'localisation' => trim(isset($_POST['localisation']) ? $_POST['localisation'] : ''),
            'date_creation' => trim(isset($_POST['date_creation']) ? $_POST['date_creation'] : date('Y-m-d')),
            'id_user' => $_SESSION['user_id'],
            'errors' => []
        ];
        
        // Valider les données
        if (empty($data['nom_ferme'])) {
            $data['errors']['nom_ferme'] = 'Le nom de la ferme est requis';
        }
        
        if (empty($data['localisation'])) {
            $data['errors']['localisation'] = 'La localisation est requise';
        }
        
        if (empty($data['date_creation'])) {
            $data['errors']['date_creation'] = 'La date de création est requise';
        } elseif (!validateDate($data['date_creation'])) {
            $data['errors']['date_creation'] = 'La date de création est invalide';
        }
        
        // S'il y a des erreurs, réafficher le formulaire
        if (!empty($data['errors'])) {
            $this->render('farms/form', $data);
            return;
        }
        
        // Créer la ferme
        if ($this->farmModel->create($data)) {
            setFlash('success', 'Ferme ajoutée avec succès');
            redirect('farm');
        } else {
            setFlash('error', 'Erreur lors de l\'ajout de la ferme');
            $this->render('farms/form', $data);
        }
    }
    
    /**
     * Affiche les détails d'une ferme
     * 
     * @param int $id ID de la ferme
     */
    public function show($id = null) {
        // Vérifier si l'ID est fourni
        if (!$id) {
            setFlash('error', 'ID de ferme non spécifié');
            redirect('farm');
        }
        
        // Récupérer les données de la ferme
        $farm = $this->farmModel->findWithOwner($id);
        
        // Vérifier si la ferme existe
        if (!$farm) {
            setFlash('error', 'Ferme non trouvée');
            redirect('farm');
        }
        
        // Vérifier si l'utilisateur est le propriétaire ou un admin
        if ($farm['id_user'] != $_SESSION['user_id'] && $_SESSION['user_role'] != 'admin') {
            setFlash('error', 'Vous n\'avez pas les droits nécessaires pour accéder à cette ferme');
            redirect('farm');
        }
        
        // Récupérer le nombre de lots actifs
        $activeBatches = $this->farmModel->countActiveBatches($id);
        
        // Récupérer les lots de la ferme
        $batches = $this->poultryBatchModel->findByFarmId($id);
        
        $data = [
            'title' => 'Détails de la ferme',
            'farm' => $farm,
            'active_batches' => $activeBatches,
            'batches' => $batches
        ];
        
        $this->render('farms/show', $data);
    }
    
    /**
     * Affiche le formulaire de modification d'une ferme
     * 
     * @param int $id ID de la ferme
     */
    public function edit($id = null) {
        // Vérifier si l'ID est fourni
        if (!$id) {
            setFlash('error', 'ID de ferme non spécifié');
            redirect('farm');
        }
        
        // Récupérer les données de la ferme
        $farm = $this->farmModel->findById($id);
        
        // Vérifier si la ferme existe
        if (!$farm) {
            setFlash('error', 'Ferme non trouvée');
            redirect('farm');
        }
        
        // Vérifier si l'utilisateur est le propriétaire ou un admin
        if (!$this->farmModel->isOwner($id, $_SESSION['user_id']) && $_SESSION['user_role'] != 'admin') {
            setFlash('error', 'Vous n\'avez pas les droits nécessaires pour modifier cette ferme');
            redirect('farm');
        }
        
        $data = [
            'title' => 'Modifier la ferme',
            'id_ferme' => $farm['id_ferme'],
            'nom_ferme' => $farm['nom_ferme'],
            'localisation' => $farm['localisation'],
            'date_creation' => $farm['date_creation'],
            'errors' => []
        ];
        
        $this->render('farms/form', $data);
    }
    
    /**
     * Traite la soumission du formulaire de modification d'une ferme
     * 
     * @param int $id ID de la ferme
     */
    public function update($id = null) {
        // Vérifier si l'ID est fourni
        if (!$id) {
            setFlash('error', 'ID de ferme non spécifié');
            redirect('farm');
        }
        
        // Vérifier si la requête est de type POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('farm');
        }
        
        // Vérifier le token CSRF
        $csrf_token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
        if (!validateCSRFToken($csrf_token)) {
            setFlash('error', 'Erreur de sécurité. Veuillez réessayer.');
            redirect('farm/edit/' . $id);
        }
        
        // Récupérer les données de la ferme
        $farm = $this->farmModel->findById($id);
        
        // Vérifier si la ferme existe
        if (!$farm) {
            setFlash('error', 'Ferme non trouvée');
            redirect('farm');
        }
        
        // Vérifier si l'utilisateur est le propriétaire ou un admin
        if (!$this->farmModel->isOwner($id, $_SESSION['user_id']) && $_SESSION['user_role'] != 'admin') {
            setFlash('error', 'Vous n\'avez pas les droits nécessaires pour modifier cette ferme');
            redirect('farm');
        }
        
        // Récupérer et nettoyer les données du formulaire
        $data = [
            'nom_ferme' => trim($_POST['nom_ferme'] ?? ''),
            'localisation' => trim($_POST['localisation'] ?? ''),
            'date_creation' => trim($_POST['date_creation'] ?? '')
        ];
        
        // Valider les données
        $errors = [];
        if (empty($data['nom_ferme'])) {
            $errors['nom_ferme'] = 'Le nom de la ferme est requis';
        }
        
        if (empty($data['localisation'])) {
            $errors['localisation'] = 'La localisation est requise';
        }
        
        if (empty($data['date_creation'])) {
            $errors['date_creation'] = 'La date de création est requise';
        } elseif (!validateDate($data['date_creation'])) {
            $errors['date_creation'] = 'La date de création est invalide';
        }
        
        // S'il y a des erreurs, réafficher le formulaire
        if (!empty($errors)) {
            $viewData = [
                'title' => 'Modifier la ferme',
                'id_ferme' => $id,
                'nom_ferme' => $data['nom_ferme'],
                'localisation' => $data['localisation'],
                'date_creation' => $data['date_creation'],
                'errors' => $errors
            ];
            $this->render('farms/form', $viewData);
            return;
        }
        
        // Mettre à jour la ferme
        if ($this->farmModel->update($id, $data)) {
            setFlash('success', 'Ferme mise à jour avec succès');
            redirect('farm/show/' . $id);
        } else {
            setFlash('error', 'Erreur lors de la mise à jour de la ferme');
            $viewData = [
                'title' => 'Modifier la ferme',
                'id_ferme' => $id,
                'nom_ferme' => $data['nom_ferme'],
                'localisation' => $data['localisation'],
                'date_creation' => $data['date_creation'],
                'errors' => []
            ];
            $this->render('farms/form', $viewData);
        }
    }
    
    /**
     * Supprime une ferme
     * 
     * @param int $id ID de la ferme
     */
    public function delete($id = null) {
        // Vérifier si l'ID est fourni
        if (!$id) {
            setFlash('error', 'ID de ferme non spécifié');
            redirect('farm');
        }
        
        // Vérifier si la requête est de type POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('farm');
        }
        
        // Vérifier le token CSRF
        $csrf_token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
        if (!validateCSRFToken($csrf_token)) {
            setFlash('error', 'Erreur de sécurité. Veuillez réessayer.');
            redirect('farm');
        }
        
        // Récupérer les données de la ferme
        $farm = $this->farmModel->findById($id);
        
        // Vérifier si la ferme existe
        if (!$farm) {
            setFlash('error', 'Ferme non trouvée');
            redirect('farm');
        }
        
        // Vérifier si l'utilisateur est le propriétaire ou un admin
        if (!$this->farmModel->isOwner($id, $_SESSION['user_id']) && $_SESSION['user_role'] != 'admin') {
            setFlash('error', 'Vous n\'avez pas les droits nécessaires pour supprimer cette ferme');
            redirect('farm');
        }
        
        // Vérifier si la ferme a des lots actifs
        $activeBatches = $this->farmModel->countActiveBatches($id);
        if ($activeBatches > 0) {
            setFlash('error', 'Impossible de supprimer cette ferme car elle contient des lots actifs');
            redirect('farm/show/' . $id);
        }
        
        // Supprimer la ferme
        if ($this->farmModel->delete($id)) {
            setFlash('success', 'Ferme supprimée avec succès');
        } else {
            setFlash('error', 'Erreur lors de la suppression de la ferme');
        }
        
        redirect('farm');
    }
    
    /**
     * Affiche la liste de toutes les fermes (admin uniquement)
     */
    public function all() {
        // Vérifier si l'utilisateur est admin
        if ($_SESSION['user_role'] != 'admin') {
            setFlash('error', 'Vous n\'avez pas les droits nécessaires pour accéder à cette section');
            redirect('farm');
        }
        
        // Récupérer toutes les fermes avec leurs propriétaires
        $farms = $this->farmModel->findAllWithOwners();
        
        // Pour chaque ferme, récupérer le nombre de lots actifs
        foreach ($farms as &$farm) {
            $farm['active_batches'] = $this->farmModel->countActiveBatches($farm['id_ferme']);
        }
        
        $data = [
            'title' => 'Toutes les fermes',
            'farms' => $farms
        ];
        
        $this->render('farms/all', $data);
    }
}