<?php
/**
 * Contrôleur PoultryBatchController
 * Gère les opérations CRUD pour les lots de volailles
 */
class PoultryBatchController extends Controller {
    private $poultryBatchModel;
    private $farmModel;
    
    public function __construct() {
        // Vérifier si l'utilisateur est connecté
        if (!$this->isLoggedIn()) {
            redirect(URLROOT . '/user/login');
        }
        
        // Vérifier si l'utilisateur a les droits nécessaires
        if (!in_array($_SESSION['user_role'], ['eleveur', 'admin'])) {
            setFlash('error', 'Vous n\'avez pas les droits nécessaires pour accéder à cette section');
            redirect(URLROOT . '/home');
        }
        
        // Charger les modèles nécessaires
        require_once dirname(__DIR__) . '/models/PoultryBatch.php';
        $this->poultryBatchModel = new PoultryBatch();
        
        require_once dirname(__DIR__) . '/models/Farm.php';
        $this->farmModel = new Farm();
    }
    
    // Liste des lots d'une ferme
    public function index($farmId = null) {
        if (!$farmId) {
            setFlash('error', 'ID de ferme non spécifié');
            redirect(URLROOT . '/farm');
        }
        
        $farm = $this->farmModel->findById($farmId);
        if (!$farm) {
            setFlash('error', 'Ferme non trouvée');
            redirect(URLROOT . '/farm');
        }
        
        // Vérifier si l'utilisateur est le propriétaire ou un admin
        if (!$this->farmModel->isOwner($farmId, $_SESSION['user_id']) && $_SESSION['user_role'] != 'admin') {
            setFlash('error', 'Vous n\'avez pas les droits nécessaires pour accéder à cette ferme');
            redirect(URLROOT . '/farm');
        }
        
        // Récupérer les paramètres de filtrage
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $status = isset($_GET['status']) ? trim($_GET['status']) : '';
        
        $batches = $this->poultryBatchModel->findByFarmId($farmId, $search, $status);
        $stats = $this->poultryBatchModel->getFarmStats($farmId);
        
        $data = [
            'title' => 'Lots de volailles - ' . $farm['nom_ferme'],
            'farm' => $farm,
            'batches' => $batches,
            'search' => $search,
            'status' => $status,
            'stats' => $stats
        ];
        
        $this->render('poultryBatch/index', $data);
    }
    
    // Formulaire de création
    public function add($farmId = null) {
        if (!$farmId) {
            setFlash('error', 'ID de ferme non spécifié');
            redirect(URLROOT . '/farm');
        }
        
        $farm = $this->farmModel->findById($farmId);
        if (!$farm) {
            setFlash('error', 'Ferme non trouvée');
            redirect(URLROOT . '/farm');
        }
        
        // Vérifier si l'utilisateur est le propriétaire ou un admin
        if (!$this->farmModel->isOwner($farmId, $_SESSION['user_id']) && $_SESSION['user_role'] != 'admin') {
            setFlash('error', 'Vous n\'avez pas les droits nécessaires pour accéder à cette ferme');
            redirect(URLROOT . '/farm');
        }
        
        $data = [
            'title' => 'Nouveau lot - ' . $farm['nom_ferme'],
            'farm' => $farm,
            'errors' => []
        ];
        
        $this->render('poultryBatch/form', $data);
    }
    
    // Traitement de la création
    public function create($farmId = null) {
        if (!$farmId) {
            setFlash('error', 'ID de ferme non spécifié');
            redirect(URLROOT . '/farm');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(URLROOT . '/farm');
        }
        
        // Vérifier le token CSRF
        $csrf_token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
        if (!validateCSRFToken($csrf_token)) {
            setFlash('error', 'Erreur de sécurité. Veuillez réessayer.');
            redirect(URLROOT . '/batch/add/' . $farmId);
        }
        
        $farm = $this->farmModel->findById($farmId);
        if (!$farm) {
            setFlash('error', 'Ferme non trouvée');
            redirect(URLROOT . '/farm');
        }
        
        // Récupérer et nettoyer les données du formulaire
        $data = [
            'id_ferme' => $farmId,
            'race' => isset($_POST['race']) ? trim($_POST['race']) : '',
            'effectif_initial' => isset($_POST['effectif_initial']) ? (int)$_POST['effectif_initial'] : 0,
            'date_arrivee' => isset($_POST['date_arrivee']) ? trim($_POST['date_arrivee']) : date('Y-m-d'),
            'statut' => 'actif'
        ];
        
        // Validation des données
        $errors = $this->poultryBatchModel->validate($data);
        
        if (!empty($errors)) {
            $viewData = [
                'title' => 'Nouveau lot - ' . $farm['nom_ferme'],
                'farm' => $farm,
                'batch' => $data,
                'errors' => $errors
            ];
            $this->render('poultryBatch/form', $viewData);
            return;
        }
        
        try {
            // Créer le lot
            $result = $this->poultryBatchModel->create($data);
            
            if ($result) {
                setFlash('success', 'Le lot a été créé avec succès');
                redirect(URLROOT . '/batch/index/' . $farmId);
            } else {
                throw new Exception('Erreur lors de la création du lot');
            }
        } catch (Exception $e) {
            error_log("Erreur lors de la création du lot : " . $e->getMessage());
            setFlash('error', 'Une erreur est survenue lors de la création du lot');
            $viewData = [
                'title' => 'Nouveau lot - ' . $farm['nom_ferme'],
                'farm' => $farm,
                'batch' => $data,
                'errors' => ['general' => 'Une erreur est survenue lors de la création du lot']
            ];
            $this->render('poultryBatch/form', $viewData);
        }
    }
    
    // Détails d'un lot
    public function show($farmId = null, $batchId = null) {
        if (!$farmId || !$batchId) {
            setFlash('error', 'Paramètres manquants');
            redirect(URLROOT . '/farm');
        }
        
        $farm = $this->farmModel->findById($farmId);
        $batch = $this->poultryBatchModel->findById($batchId);
        
        if (!$farm || !$batch || $batch['id_ferme'] != $farmId) {
            setFlash('error', 'Lot non trouvé');
            redirect(URLROOT . '/farm');
        }
        
        // Vérifier si l'utilisateur est le propriétaire ou un admin
        if (!$this->farmModel->isOwner($farmId, $_SESSION['user_id']) && $_SESSION['user_role'] != 'admin') {
            setFlash('error', 'Vous n\'avez pas les droits nécessaires pour accéder à ce lot');
            redirect(URLROOT . '/farm');
        }
        
        // Récupérer l'historique du lot
        $history = $this->poultryBatchModel->getHistory($batchId);
        
        $data = [
            'title' => 'Détails du lot - ' . $farm['nom_ferme'],
            'farm' => $farm,
            'batch' => $batch,
            'history' => $history
        ];
        
        $this->render('poultryBatch/show', $data);
    }
    
    // Formulaire de modification
    public function edit($farmId = null, $batchId = null) {
        if (!$farmId || !$batchId) {
            setFlash('error', 'Paramètres manquants');
            redirect(URLROOT . '/farm');
        }
        
        $farm = $this->farmModel->findById($farmId);
        $batch = $this->poultryBatchModel->findById($batchId);
        
        if (!$farm || !$batch || $batch['id_ferme'] != $farmId) {
            setFlash('error', 'Lot non trouvé');
            redirect(URLROOT . '/farm');
        }
        
        // Vérifier si l'utilisateur est le propriétaire ou un admin
        if (!$this->farmModel->isOwner($farmId, $_SESSION['user_id']) && $_SESSION['user_role'] != 'admin') {
            setFlash('error', 'Vous n\'avez pas les droits nécessaires pour modifier ce lot');
            redirect(URLROOT . '/farm');
        }
        
        $data = [
            'title' => 'Modifier le lot - ' . $farm['nom_ferme'],
            'farm' => $farm,
            'batch' => $batch,
            'errors' => []
        ];
        
        $this->render('poultryBatch/form', $data);
    }
    
    // Traitement de la modification
    public function update($farmId = null, $batchId = null) {
        if (!$farmId || !$batchId) {
            setFlash('error', 'Paramètres manquants');
            redirect(URLROOT . '/farm');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(URLROOT . '/farm');
        }
        
        try {
            // Vérifier le token CSRF
            $csrf_token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
            if (!validateCSRFToken($csrf_token)) {
                throw new Exception('Erreur de sécurité. Veuillez réessayer.');
            }
            
            $farm = $this->farmModel->findById($farmId);
            $batch = $this->poultryBatchModel->findById($batchId);
            
            if (!$farm || !$batch || $batch['id_ferme'] != $farmId) {
                throw new Exception('Lot non trouvé');
            }
            
            // Vérifier si l'utilisateur est le propriétaire ou un admin
            if (!$this->farmModel->isOwner($farmId, $_SESSION['user_id']) && $_SESSION['user_role'] != 'admin') {
                throw new Exception('Vous n\'avez pas les droits nécessaires pour modifier ce lot');
            }
            
            $data = [
                'id_ferme' => $farmId,
                'race' => isset($_POST['race']) ? trim($_POST['race']) : '',
                'effectif_initial' => isset($_POST['effectif_initial']) ? (int)$_POST['effectif_initial'] : 0,
                'date_arrivee' => isset($_POST['date_arrivee']) ? trim($_POST['date_arrivee']) : '',
                'statut' => isset($_POST['statut']) ? trim($_POST['statut']) : ''
            ];
            
            // Validation des données
            $errors = $this->poultryBatchModel->validate($data, true);
            
            if (!empty($errors)) {
                $viewData = [
                    'title' => 'Modifier le lot - ' . $farm['nom_ferme'],
                    'farm' => $farm,
                    'batch' => array_merge($batch, $data),
                    'errors' => $errors
                ];
                $this->render('poultryBatch/form', $viewData);
                return;
            }
            
            if ($this->poultryBatchModel->update($batchId, $data)) {
                setFlash('success', 'Le lot a été modifié avec succès');
                redirect(URLROOT . '/batch/index/' . $farmId);
            } else {
                throw new Exception('Erreur lors de la mise à jour du lot');
            }
        } catch (Exception $e) {
            error_log("Erreur lors de la mise à jour du lot : " . $e->getMessage());
            setFlash('error', $e->getMessage());
            $viewData = [
                'title' => 'Modifier le lot - ' . $farm['nom_ferme'],
                'farm' => $farm,
                'batch' => array_merge($batch, $data),
                'errors' => ['general' => $e->getMessage()]
            ];
            $this->render('poultryBatch/form', $viewData);
        }
    }
    
    // Suppression d'un lot
    public function delete($farmId = null, $batchId = null) {
        if (!$farmId || !$batchId) {
            setFlash('error', 'Paramètres manquants');
            redirect(URLROOT . '/farm');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(URLROOT . '/farm');
        }
        
        try {
            // Vérifier le token CSRF
            $csrf_token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
            if (!validateCSRFToken($csrf_token)) {
                throw new Exception('Erreur de sécurité. Veuillez réessayer.');
            }
            
            $farm = $this->farmModel->findById($farmId);
            $batch = $this->poultryBatchModel->findById($batchId);
            
            if (!$farm || !$batch || $batch['id_ferme'] != $farmId) {
                throw new Exception('Lot non trouvé');
            }
            
            // Vérifier si l'utilisateur est le propriétaire ou un admin
            if (!$this->farmModel->isOwner($farmId, $_SESSION['user_id']) && $_SESSION['user_role'] != 'admin') {
                throw new Exception('Vous n\'avez pas les droits nécessaires pour supprimer ce lot');
            }
            
            // Supprimer le lot
            if ($this->poultryBatchModel->delete($batchId)) {
                setFlash('success', 'Le lot a été supprimé avec succès');
                redirect(URLROOT . '/batch/index/' . $farmId);
            } else {
                throw new Exception('Erreur lors de la suppression du lot');
            }
        } catch (Exception $e) {
            error_log("Erreur lors de la suppression du lot : " . $e->getMessage());
            setFlash('error', $e->getMessage());
            redirect(URLROOT . '/batch/show/' . $farmId . '/' . $batchId);
        }
    }
} 