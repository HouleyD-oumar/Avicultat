<?php

class ForumController extends BaseController {
    private $forumModel;
    private $postsPerPage = 10;
    
    public function __construct() {
        parent::__construct();
        $this->forumModel = new ForumPost();
    }
    
    public function index() {
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $category = isset($_GET['category']) ? trim($_GET['category']) : '';
        
        // Calculer le nombre total de sujets
        $totalPosts = $this->forumModel->countPosts($search, $category);
        $totalPages = ceil($totalPosts / $this->postsPerPage);
        
        // S'assurer que la page courante est valide
        $currentPage = max(1, min($currentPage, $totalPages));
        
        // Récupérer les sujets pour la page courante
        $posts = $this->forumModel->getPaginatedPosts(
            $currentPage,
            $this->postsPerPage,
            $search,
            $category
        );
        
        $this->view('forum/index', [
            'posts' => $posts,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'search' => $search,
            'category' => $category,
            'title' => 'Forum Communautaire'
        ]);
    }
    
    public function show($id) {
        $post = $this->forumModel->find($id);
        if (!$post) {
            $_SESSION['error'] = 'Sujet introuvable';
            $this->redirect('forum');
        }
        
        $comments = $this->forumModel->getComments($id);
        
        $this->view('forum/show', [
            'post' => $post,
            'comments' => $comments,
            'title' => $post['title']
        ]);
    }
    
    public function create() {
        $this->view('forum/create', [
            'title' => 'Nouveau Sujet'
        ]);
    }
    
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validation des données
            $errors = $this->validatePost($_POST);
            
            if (empty($errors)) {
                $data = [
                    'title' => trim($_POST['title']),
                    'content' => trim($_POST['content']),
                    'category' => $_POST['category'],
                    'user_id' => $_SESSION['user_id']
                ];
                
                if ($this->forumModel->create($data)) {
                    $_SESSION['success'] = 'Sujet créé avec succès';
                    $this->redirect('forum');
                } else {
                    $_SESSION['error'] = 'Erreur lors de la création du sujet';
                    $this->redirect('forum/create');
                }
            } else {
                $_SESSION['error'] = implode('<br>', $errors);
                $_SESSION['form_data'] = $_POST;
                $this->redirect('forum/create');
            }
        }
    }
    
    public function edit($id) {
        $post = $this->forumModel->find($id);
        if (!$post || $post['user_id'] !== $_SESSION['user_id']) {
            $_SESSION['error'] = 'Vous n\'êtes pas autorisé à modifier ce sujet';
            $this->redirect('forum');
        }
        
        $this->view('forum/edit', [
            'post' => $post,
            'title' => 'Modifier le Sujet'
        ]);
    }
    
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $post = $this->forumModel->find($id);
            if (!$post || $post['user_id'] !== $_SESSION['user_id']) {
                $_SESSION['error'] = 'Vous n\'êtes pas autorisé à modifier ce sujet';
                $this->redirect('forum');
            }
            
            // Validation des données
            $errors = $this->validatePost($_POST);
            
            if (empty($errors)) {
                $data = [
                    'title' => trim($_POST['title']),
                    'content' => trim($_POST['content']),
                    'category' => $_POST['category']
                ];
                
                if ($this->forumModel->update($id, $data)) {
                    $_SESSION['success'] = 'Sujet mis à jour avec succès';
                    $this->redirect('forum');
                } else {
                    $_SESSION['error'] = 'Erreur lors de la mise à jour du sujet';
                    $this->redirect('forum/edit/' . $id);
                }
            } else {
                $_SESSION['error'] = implode('<br>', $errors);
                $_SESSION['form_data'] = $_POST;
                $this->redirect('forum/edit/' . $id);
            }
        }
    }
    
    public function delete($id) {
        $post = $this->forumModel->find($id);
        if (!$post || $post['user_id'] !== $_SESSION['user_id']) {
            $_SESSION['error'] = 'Vous n\'êtes pas autorisé à supprimer ce sujet';
            $this->redirect('forum');
        }
        
        if ($this->forumModel->delete($id)) {
            $_SESSION['success'] = 'Sujet supprimé avec succès';
        } else {
            $_SESSION['error'] = 'Erreur lors de la suppression du sujet';
        }
        $this->redirect('forum');
    }
    
    public function comment($post_id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validation du commentaire
            $errors = $this->validateComment($_POST);
            
            if (empty($errors)) {
                $data = [
                    'post_id' => $post_id,
                    'content' => trim($_POST['content']),
                    'user_id' => $_SESSION['user_id']
                ];
                
                if ($this->forumModel->addComment($data)) {
                    $_SESSION['success'] = 'Commentaire ajouté avec succès';
                } else {
                    $_SESSION['error'] = 'Erreur lors de l\'ajout du commentaire';
                }
            } else {
                $_SESSION['error'] = implode('<br>', $errors);
            }
            $this->redirect('forum/show/' . $post_id);
        }
    }
    
    public function deleteComment($post_id, $comment_id) {
        $comment = $this->forumModel->getComment($comment_id);
        if (!$comment || $comment['user_id'] !== $_SESSION['user_id']) {
            $_SESSION['error'] = 'Vous n\'êtes pas autorisé à supprimer ce commentaire';
            $this->redirect('forum/show/' . $post_id);
        }
        
        if ($this->forumModel->deleteComment($comment_id)) {
            $_SESSION['success'] = 'Commentaire supprimé avec succès';
        } else {
            $_SESSION['error'] = 'Erreur lors de la suppression du commentaire';
        }
        $this->redirect('forum/show/' . $post_id);
    }
    
    /**
     * Valide les données d'un sujet
     */
    private function validatePost($data) {
        $errors = [];
        
        // Validation du titre
        if (empty($data['title'])) {
            $errors[] = 'Le titre est requis';
        } elseif (strlen($data['title']) < 5) {
            $errors[] = 'Le titre doit contenir au moins 5 caractères';
        } elseif (strlen($data['title']) > 100) {
            $errors[] = 'Le titre ne doit pas dépasser 100 caractères';
        }
        
        // Validation de la catégorie
        $validCategories = ['general', 'sante', 'alimentation', 'equipement', 'conseils'];
        if (empty($data['category']) || !in_array($data['category'], $validCategories)) {
            $errors[] = 'Veuillez sélectionner une catégorie valide';
        }
        
        // Validation du contenu
        if (empty($data['content'])) {
            $errors[] = 'Le contenu est requis';
        } elseif (strlen($data['content']) < 20) {
            $errors[] = 'Le contenu doit contenir au moins 20 caractères';
        }
        
        return $errors;
    }
    
    /**
     * Valide les données d'un commentaire
     */
    private function validateComment($data) {
        $errors = [];
        
        if (empty($data['content'])) {
            $errors[] = 'Le commentaire est requis';
        } elseif (strlen($data['content']) < 5) {
            $errors[] = 'Le commentaire doit contenir au moins 5 caractères';
        }
        
        return $errors;
    }
    
    /**
     * Retourne la couleur Bootstrap pour une catégorie
     */
    public function getCategoryColor($category) {
        $colors = [
            'general' => 'primary',
            'sante' => 'danger',
            'alimentation' => 'success',
            'equipement' => 'info',
            'conseils' => 'warning'
        ];
        
        return $colors[$category] ?? 'secondary';
    }
} 