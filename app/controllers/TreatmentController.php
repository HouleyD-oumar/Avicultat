<?php

class TreatmentController extends BaseController {
    private $treatmentModel;
    
    public function __construct() {
        parent::__construct();
        $this->treatmentModel = new Treatment();
    }
    
    public function index() {
        $treatments = $this->treatmentModel->all();
        $this->view('treatments/index', [
            'treatments' => $treatments,
            'title' => 'Gestion des Traitements'
        ]);
    }
    
    public function show($id) {
        $treatment = $this->treatmentModel->find($id);
        if (!$treatment) {
            $this->redirect('treatments');
        }
        
        $this->view('treatments/show', [
            'treatment' => $treatment,
            'title' => 'Détails du Traitement'
        ]);
    }
    
    public function create() {
        $this->view('treatments/create', [
            'title' => 'Nouveau Traitement'
        ]);
    }
    
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'batch_id' => $_POST['batch_id'],
                'treatment_type' => $_POST['treatment_type'],
                'treatment_date' => $_POST['treatment_date'],
                'dosage' => $_POST['dosage'],
                'notes' => $_POST['notes'],
                'administered_by' => $_SESSION['user_id']
            ];
            
            if ($this->treatmentModel->create($data)) {
                $_SESSION['success'] = 'Traitement enregistré avec succès';
                $this->redirect('treatments');
            } else {
                $_SESSION['error'] = 'Erreur lors de l\'enregistrement du traitement';
                $this->redirect('treatments/create');
            }
        }
    }
    
    public function edit($id) {
        $treatment = $this->treatmentModel->find($id);
        if (!$treatment) {
            $this->redirect('treatments');
        }
        
        $this->view('treatments/edit', [
            'treatment' => $treatment,
            'title' => 'Modifier le Traitement'
        ]);
    }
    
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'batch_id' => $_POST['batch_id'],
                'treatment_type' => $_POST['treatment_type'],
                'treatment_date' => $_POST['treatment_date'],
                'dosage' => $_POST['dosage'],
                'notes' => $_POST['notes']
            ];
            
            if ($this->treatmentModel->update($id, $data)) {
                $_SESSION['success'] = 'Traitement mis à jour avec succès';
                $this->redirect('treatments');
            } else {
                $_SESSION['error'] = 'Erreur lors de la mise à jour du traitement';
                $this->redirect('treatments/edit/' . $id);
            }
        }
    }
    
    public function delete($id) {
        if ($this->treatmentModel->delete($id)) {
            $_SESSION['success'] = 'Traitement supprimé avec succès';
        } else {
            $_SESSION['error'] = 'Erreur lors de la suppression du traitement';
        }
        $this->redirect('treatments');
    }
} 