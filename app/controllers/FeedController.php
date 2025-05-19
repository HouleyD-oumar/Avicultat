<?php

class FeedController extends BaseController {
    private $feedModel;
    
    public function __construct() {
        parent::__construct();
        $this->feedModel = new Feed();
    }
    
    public function index() {
        $feeds = $this->feedModel->all();
        $this->view('feeds/index', [
            'feeds' => $feeds,
            'title' => 'Gestion de l\'Alimentation'
        ]);
    }
    
    public function show($id) {
        $feed = $this->feedModel->find($id);
        if (!$feed) {
            $this->redirect('feeds');
        }
        
        $this->view('feeds/show', [
            'feed' => $feed,
            'title' => 'Détails de l\'Alimentation'
        ]);
    }
    
    public function create() {
        $this->view('feeds/create', [
            'title' => 'Nouvelle Entrée d\'Alimentation'
        ]);
    }
    
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'batch_id' => $_POST['batch_id'],
                'feed_type' => $_POST['feed_type'],
                'quantity' => $_POST['quantity'],
                'feeding_date' => $_POST['feeding_date'],
                'notes' => $_POST['notes'],
                'recorded_by' => $_SESSION['user_id']
            ];
            
            if ($this->feedModel->create($data)) {
                $_SESSION['success'] = 'Entrée d\'alimentation enregistrée avec succès';
                $this->redirect('feeds');
            } else {
                $_SESSION['error'] = 'Erreur lors de l\'enregistrement de l\'alimentation';
                $this->redirect('feeds/create');
            }
        }
    }
    
    public function edit($id) {
        $feed = $this->feedModel->find($id);
        if (!$feed) {
            $this->redirect('feeds');
        }
        
        $this->view('feeds/edit', [
            'feed' => $feed,
            'title' => 'Modifier l\'Entrée d\'Alimentation'
        ]);
    }
    
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'batch_id' => $_POST['batch_id'],
                'feed_type' => $_POST['feed_type'],
                'quantity' => $_POST['quantity'],
                'feeding_date' => $_POST['feeding_date'],
                'notes' => $_POST['notes']
            ];
            
            if ($this->feedModel->update($id, $data)) {
                $_SESSION['success'] = 'Entrée d\'alimentation mise à jour avec succès';
                $this->redirect('feeds');
            } else {
                $_SESSION['error'] = 'Erreur lors de la mise à jour de l\'alimentation';
                $this->redirect('feeds/edit/' . $id);
            }
        }
    }
    
    public function delete($id) {
        if ($this->feedModel->delete($id)) {
            $_SESSION['success'] = 'Entrée d\'alimentation supprimée avec succès';
        } else {
            $_SESSION['error'] = 'Erreur lors de la suppression de l\'alimentation';
        }
        $this->redirect('feeds');
    }
} 