<?php
/**
 * Modèle PoultryBatch
 * Gère les opérations liées aux lots de volailles
 */
class PoultryBatch extends BaseModel {
    protected $table = 'poultry_batches';
    protected $primaryKey = 'id_lot';
    protected $fillable = ['race', 'effectif_initial', 'date_arrivee', 'statut', 'id_ferme'];
    
    /**
     * Récupère les lots actifs d'un utilisateur
     * 
     * @param int $userId ID de l'utilisateur
     * @return array Liste des lots actifs
     */
    public function getActiveBatchesByUserId($userId) {
        $sql = "SELECT pb.*, f.nom_ferme as farm_name 
                FROM {$this->table} pb 
                JOIN farms f ON pb.id_ferme = f.id_ferme 
                WHERE f.id_user = :user_id 
                AND pb.statut = 'actif' 
                ORDER BY pb.date_arrivee DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Récupère tous les lots
     * 
     * @param string $orderBy Champ pour le tri
     * @param string $order Direction du tri (ASC ou DESC)
     * @return array Liste de tous les lots
     */
    public function findAll($orderBy = 'id_lot', $order = 'ASC') {
        $sql = "SELECT pb.*, f.nom_ferme as farm_name, u.nom, u.prenom as eleveur_nom
                FROM {$this->table} pb 
                JOIN farms f ON pb.id_ferme = f.id_ferme 
                JOIN users u ON f.id_user = u.id_user
                ORDER BY pb.{$orderBy} {$order}";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Récupère un lot avec ses détails
     * 
     * @param int $id ID du lot
     * @return array|bool Détails du lot ou false si non trouvé
     */
    public function findWithDetails($id) {
        $sql = "SELECT pb.*, f.nom_ferme, f.localisation 
                FROM {$this->table} pb 
                JOIN farms f ON pb.id_ferme = f.id_ferme 
                WHERE pb.id_lot = :id";
        return $this->db->query($sql, ['id' => $id])->fetch();
    }
    
    /**
     * Met à jour le statut d'un lot
     * 
     * @param int $id ID du lot
     * @param string $status Nouveau statut
     * @return bool Succès ou échec de la mise à jour
     */
    public function updateStatus($id, $status) {
        $allowedStatuses = ['actif', 'vendu', 'perte totale'];
        if (!in_array($status, $allowedStatuses)) {
            return false;
        }
        
        return $this->update($id, ['statut' => $status]);
    }
    
    /**
     * Récupère l'historique d'un lot
     * 
     * @param int $id ID du lot
     * @return array Historique du lot
     */
    public function getHistory($id) {
        $sql = "SELECT 
                    'traitement' as type,
                    t.date_application as date,
                    t.produit as description,
                    t.observations as details
                FROM treatments t
                WHERE t.id_lot = :id
                UNION ALL
                SELECT 
                    'alimentation' as type,
                    f.date_distribution as date,
                    f.type as description,
                    CONCAT(f.quantite, ' kg') as details
                FROM feeds f
                WHERE f.id_lot = :id
                ORDER BY date DESC";
        
        return $this->db->query($sql, ['id' => $id])->fetchAll();
    }
    
    /**
     * Ajoute un nouveau lot
     * 
     * @param array $data Données du lot
     * @return int|bool ID du nouveau lot ou false en cas d'échec
     */
    public function addBatch($data) {
        return $this->create($data);
    }
    
    /**
     * Met à jour un lot
     * 
     * @param int $id ID du lot
     * @param array $data Nouvelles données
     * @return bool Succès ou échec de la mise à jour
     */
    public function updateBatch($id, $data) {
        return $this->update($id, $data);
    }
    
    /**
     * Récupère les lots d'une ferme spécifique
     * 
     * @param int $farmId ID de la ferme
     * @param string $orderBy Champ pour le tri
     * @param string $order Direction du tri (ASC ou DESC)
     * @return array Liste des lots de la ferme
     */
    public function findByFarmId($farmId, $orderBy = 'date_arrivee', $order = 'DESC') {
        $sql = "SELECT pb.*, f.nom_ferme as farm_name 
                FROM {$this->table} pb 
                JOIN farms f ON pb.id_ferme = f.id_ferme 
                WHERE pb.id_ferme = :farm_id 
                ORDER BY pb.{$orderBy} {$order}";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':farm_id', $farmId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Récupère les lots actifs d'une ferme
     * 
     * @param int $farmId ID de la ferme
     * @return array Liste des lots actifs
     */
    public function getActiveBatchesByFarmId($farmId) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE id_ferme = :farm_id AND statut = 'actif' 
                ORDER BY date_arrivee DESC";
        return $this->db->query($sql, ['farm_id' => $farmId])->fetchAll();
    }
    
    /**
     * Récupère les traitements d'un lot
     * 
     * @param int $batchId ID du lot
     * @return array Liste des traitements
     */
    public function getTreatments($batchId) {
        $sql = "SELECT * FROM treatments 
                WHERE id_lot = :batch_id 
                ORDER BY date_application DESC";
        return $this->db->query($sql, ['batch_id' => $batchId])->fetchAll();
    }
    
    /**
     * Récupère les alimentations d'un lot
     * 
     * @param int $batchId ID du lot
     * @return array Liste des alimentations
     */
    public function getFeeds($batchId) {
        $sql = "SELECT * FROM feeds 
                WHERE id_lot = :batch_id 
                ORDER BY date_distribution DESC";
        return $this->db->query($sql, ['batch_id' => $batchId])->fetchAll();
    }
    
    /**
     * Validation des données
     * 
     * @param array $data Données du lot
     * @return array Liste des erreurs
     */
    public function validate($data) {
        $errors = [];
        
        if (empty($data['race'])) {
            $errors['race'] = 'La race est requise';
        } elseif (strlen($data['race']) > 100) {
            $errors['race'] = 'La race ne doit pas dépasser 100 caractères';
        }
        
        if (!isset($data['effectif_initial']) || $data['effectif_initial'] <= 0) {
            $errors['effectif_initial'] = 'L\'effectif initial doit être supérieur à 0';
        }
        
        if (empty($data['date_arrivee'])) {
            $errors['date_arrivee'] = 'La date d\'arrivée est requise';
        } elseif (strtotime($data['date_arrivee']) > time()) {
            $errors['date_arrivee'] = 'La date d\'arrivée ne peut pas être dans le futur';
        }
        
        if (empty($data['id_ferme'])) {
            $errors['id_ferme'] = 'La ferme est requise';
        }
        
        return $errors;
    }
    
    /**
     * Surcharge de la méthode create pour ajouter la validation
     * 
     * @param array $data Données du lot
     * @return int|bool ID du nouveau lot ou false en cas d'échec
     */
    public function create($data) {
        $errors = $this->validate($data);
        if (!empty($errors)) {
            return false;
        }
        
        return parent::create($data);
    }
    
    /**
     * Surcharge de la méthode update pour ajouter la validation
     * 
     * @param int $id ID du lot
     * @param array $data Nouvelles données
     * @return bool Succès ou échec de la mise à jour
     */
    public function update($id, $data) {
        $errors = $this->validate($data);
        if (!empty($errors)) {
            return false;
        }
        
        return parent::update($id, $data);
    }
} 