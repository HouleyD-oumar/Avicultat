<?php
class PoultryBatchController extends BaseController {
    private $poultryBatchModel;
    private $farmModel;
    
    public function __construct() {
        parent::__construct();
        $this->poultryBatchModel = new PoultryBatch();
        $this->farmModel = new Farm();
    }
    
    // Liste des lots d'une ferme
    public function index($farmId) {
        $this->requireAuth();
        
        $farm = $this->farmModel->findById($farmId);
        if (!$farm) {
            $this->redirect('/farms');
        }
        
        $page = $_GET['page'] ?? 1;
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        
        $pagination = $this->poultryBatchModel->paginate([
            'id_ferme' => $farmId,
            'search' => $search,
            'status' => $status
        ], $page);
        
        $this->view('poultryBatch/index', [
            'title' => 'Lots de volailles - ' . $farm['nom_ferme'],
            'farm' => $farm,
            'batches' => $pagination['data'],
            'pagination' => $pagination,
            'search' => $search,
            'status' => $status
        ]);
    }
    
    // Formulaire de création
    public function create($farmId) {
        $this->requireAuth();
        
        $farm = $this->farmModel->findById($farmId);
        if (!$farm) {
            $this->redirect('/farms');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'id_ferme' => $farmId,
                'race' => $_POST['race'],
                'effectif_initial' => $_POST['effectif_initial'],
                'date_arrivee' => $_POST['date_arrivee'],
                'statut' => 'actif'
            ];
            
            if ($this->poultryBatchModel->create($data)) {
                $this->setFlash('success', 'Le lot a été créé avec succès');
                $this->redirect("/farms/{$farmId}/batches");
            } else {
                $this->setFlash('error', 'Erreur lors de la création du lot');
            }
        }
        
        $this->view('poultryBatch/form', [
            'title' => 'Nouveau lot - ' . $farm['nom_ferme'],
            'farm' => $farm,
            'batch' => null
        ]);
    }
    
    // Formulaire de modification
    public function edit($farmId, $batchId) {
        $this->requireAuth();
        
        $farm = $this->farmModel->findById($farmId);
        $batch = $this->poultryBatchModel->findById($batchId);
        
        if (!$farm || !$batch || $batch['id_ferme'] != $farmId) {
            $this->redirect('/farms');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'race' => $_POST['race'],
                'effectif_initial' => $_POST['effectif_initial'],
                'date_arrivee' => $_POST['date_arrivee'],
                'statut' => $_POST['statut']
            ];
            
            if ($this->poultryBatchModel->update($batchId, $data)) {
                $this->setFlash('success', 'Le lot a été modifié avec succès');
                $this->redirect("/farms/{$farmId}/batches");
            } else {
                $this->setFlash('error', 'Erreur lors de la modification du lot');
            }
        }
        
        $this->view('poultryBatch/form', [
            'title' => 'Modifier le lot - ' . $farm['nom_ferme'],
            'farm' => $farm,
            'batch' => $batch
        ]);
    }
    
    // Détails d'un lot
    public function show($farmId, $batchId) {
        $this->requireAuth();
        
        $farm = $this->farmModel->findById($farmId);
        $batch = $this->poultryBatchModel->findWithDetails($batchId);
        
        if (!$farm || !$batch || $batch['id_ferme'] != $farmId) {
            $this->redirect('/farms');
        }
        
        $treatments = $this->poultryBatchModel->getTreatments($batchId);
        $feeds = $this->poultryBatchModel->getFeeds($batchId);
        $history = $this->poultryBatchModel->getHistory($batchId);
        
        $this->view('poultryBatch/show', [
            'title' => 'Détails du lot - ' . $farm['nom_ferme'],
            'farm' => $farm,
            'batch' => $batch,
            'treatments' => $treatments,
            'feeds' => $feeds,
            'history' => $history
        ]);
    }
    
    // Suppression d'un lot
    public function delete($farmId, $batchId) {
        $this->requireAuth();
        
        $farm = $this->farmModel->findById($farmId);
        $batch = $this->poultryBatchModel->findById($batchId);
        
        if (!$farm || !$batch || $batch['id_ferme'] != $farmId) {
            $this->redirect('/farms');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->poultryBatchModel->delete($batchId)) {
                $this->setFlash('success', 'Le lot a été supprimé avec succès');
            } else {
                $this->setFlash('error', 'Erreur lors de la suppression du lot');
            }
            $this->redirect("/farms/{$farmId}/batches");
        }
        
        $this->view('poultryBatch/delete', [
            'title' => 'Supprimer le lot - ' . $farm['nom_ferme'],
            'farm' => $farm,
            'batch' => $batch
        ]);
    }
} 