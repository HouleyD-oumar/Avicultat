<?php
/**
 * Contrôleur HomeController
 * Gère l'affichage de la page d'accueil et du tableau de bord
 */
class HomeController extends Controller {
    private $userModel;
    private $farmModel;
    private $poultryBatchModel;
    private $treatmentModel;
    private $feedModel;
    private $forumPostModel;
    
    /**
     * Constructeur - initialise les modèles nécessaires
     */
    public function __construct() {
        // Initialiser uniquement les modèles nécessaires selon le contexte
        $this->userModel = new User();
        
        // Les autres modèles seront initialisés à la demande
        if ($this->isLoggedIn()) {
            if (in_array($_SESSION['user_role'], ['eleveur', 'admin'])) {
                require_once dirname(__DIR__) . '/models/Farm.php';
                require_once dirname(__DIR__) . '/models/PoultryBatch.php';
                $this->farmModel = new Farm();
                $this->poultryBatchModel = new PoultryBatch();
            }
            
            if (in_array($_SESSION['user_role'], ['eleveur', 'veterinaire', 'admin'])) {
                require_once dirname(__DIR__) . '/models/Treatment.php';
                $this->treatmentModel = new Treatment();
            }
            
            if (in_array($_SESSION['user_role'], ['eleveur', 'admin'])) {
                require_once dirname(__DIR__) . '/models/Feed.php';
                $this->feedModel = new Feed();
            }
            
            require_once dirname(__DIR__) . '/models/ForumPost.php';
            $this->forumPostModel = new ForumPost();
        }
    }
    
    /**
     * Affiche la page d'accueil ou le tableau de bord selon le statut de connexion
     */
    public function index() {
        if ($this->isLoggedIn()) {
            // Afficher le tableau de bord adapté au rôle de l'utilisateur
            $this->dashboard();
        } else {
            // Afficher la page d'accueil pour les visiteurs
            $data = [
                'pageTitle' => 'Bienvenue sur ' . APP_NAME
            ];
            
            $this->render('home/index', $data);
        }
    }
    
    /**
     * Affiche le tableau de bord adapté au rôle de l'utilisateur
     */
    private function dashboard() {
        $data = [
            'pageTitle' => 'Tableau de bord'
        ];
        
        // Données communes à tous les utilisateurs
        $data['recent_posts'] = $this->forumPostModel->getRecentPosts(5);
        
        // Données spécifiques selon le rôle
        switch ($_SESSION['user_role']) {
            case 'eleveur':
                $data['farms'] = $this->farmModel->findByUserId($_SESSION['user_id']);
                $data['active_batches'] = $this->poultryBatchModel->getActiveBatchesByUserId($_SESSION['user_id']);
                $data['recent_treatments'] = $this->treatmentModel->getRecentTreatmentsByUserId($_SESSION['user_id'], 5);
                $data['recent_feeds'] = $this->feedModel->getRecentFeedsByUserId($_SESSION['user_id'], 5);
                break;
                
            case 'veterinaire':
                $data['recent_treatments'] = $this->treatmentModel->getRecentTreatments(10);
                break;
                
            case 'admin':
                $data['total_users'] = count($this->userModel->findAll());
                $data['total_farms'] = count($this->farmModel->findAll());
                $data['total_batches'] = count($this->poultryBatchModel->findAll());
                $data['total_treatments'] = count($this->treatmentModel->findAll());
                $data['total_feeds'] = count($this->feedModel->findAll());
                $data['total_posts'] = count($this->forumPostModel->findAll());
                break;
        }
        
        $this->render('home/dashboard', $data);
    }
}