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
     * Récupère les lots d'une ferme avec filtres
     * 
     * @param int $farmId ID de la ferme
     * @param string $search Terme de recherche
     * @param string $status Statut du lot
     * @param string $orderBy Champ pour le tri
     * @param string $order Direction du tri (ASC ou DESC)
     * @return array Liste des lots
     */
    public function findByFarmId($farmId, $search = '', $status = '', $orderBy = 'date_arrivee', $order = 'DESC') {
        $sql = "SELECT pb.*, f.nom_ferme as farm_name 
                FROM {$this->table} pb 
                JOIN farms f ON pb.id_ferme = f.id_ferme 
                WHERE pb.id_ferme = :farm_id";
        
        $params = ['farm_id' => $farmId];
        
        if (!empty($search)) {
            $sql .= " AND pb.race LIKE :search";
            $params['search'] = "%{$search}%";
        }
        
        if (!empty($status)) {
            $sql .= " AND pb.statut = :status";
            $params['status'] = $status;
        }
        
        $sql .= " ORDER BY pb.{$orderBy} {$order}";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
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
    public function findById($id) {
        $sql = "SELECT pb.*, f.nom_ferme, f.localisation 
                FROM {$this->table} pb 
                JOIN farms f ON pb.id_ferme = f.id_ferme 
                WHERE pb.id_lot = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
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
                WHERE t.id_lot = :id1
                UNION ALL
                SELECT 
                    'alimentation' as type,
                    f.date_distribution as date,
                    f.type as description,
                    CONCAT(f.quantite, ' kg') as details
                FROM feeds f
                WHERE f.id_lot = :id2
                ORDER BY date DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id1', $id, PDO::PARAM_INT);
        $stmt->bindValue(':id2', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Validation des données
     * 
     * @param array $data Données du lot
     * @param bool $isUpdate Indique si c'est une mise à jour
     * @return array Liste des erreurs
     */
    public function validate($data, $isUpdate = false) {
        $errors = [];
        
        // Validation de la race
        if (empty($data['race'])) {
            $errors['race'] = 'La race est requise';
        } elseif (strlen($data['race']) > 100) {
            $errors['race'] = 'La race ne doit pas dépasser 100 caractères';
        }
        
        // Validation de l'effectif initial
        if (!isset($data['effectif_initial']) || !is_numeric($data['effectif_initial']) || $data['effectif_initial'] <= 0) {
            $errors['effectif_initial'] = 'L\'effectif initial doit être un nombre supérieur à 0';
        }
        
        // Validation de la date d'arrivée
        if (empty($data['date_arrivee'])) {
            $errors['date_arrivee'] = 'La date d\'arrivée est requise';
        } elseif (!strtotime($data['date_arrivee'])) {
            $errors['date_arrivee'] = 'La date d\'arrivée est invalide';
        } elseif (strtotime($data['date_arrivee']) > time()) {
            $errors['date_arrivee'] = 'La date d\'arrivée ne peut pas être dans le futur';
        }
        
        // Validation du statut
        if (isset($data['statut']) && !in_array($data['statut'], ['actif', 'vendu', 'perte totale'])) {
            $errors['statut'] = 'Le statut est invalide';
        }
        
        // Validation de l'ID de la ferme (uniquement pour la création)
        if (!$isUpdate && (!isset($data['id_ferme']) || empty($data['id_ferme']))) {
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
        $errors = $this->validate($data, false);
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
        try {
            // Vérifier si le lot existe
            $batch = $this->findById($id);
            if (!$batch) {
                error_log("Erreur : Lot non trouvé (ID: {$id})");
                return false;
            }

            // Validation des données
            $errors = $this->validate($data, true);
            if (!empty($errors)) {
                error_log("Erreur de validation : " . json_encode($errors));
                return false;
            }

            // Mise à jour dans la base de données
            $result = parent::update($id, $data);
            
            if (!$result) {
                error_log("Erreur lors de la mise à jour du lot (ID: {$id})");
            }
            
            return $result;
        } catch (Exception $e) {
            error_log("Exception lors de la mise à jour du lot : " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Compte le nombre de lots actifs d'une ferme
     * 
     * @param int $farmId ID de la ferme
     * @return int Nombre de lots actifs
     */
    public function countActiveBatches($farmId) {
        $sql = "SELECT COUNT(*) as count 
                FROM {$this->table} 
                WHERE id_ferme = :farm_id 
                AND statut = 'actif'";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['farm_id' => $farmId]);
        $result = $stmt->fetch();
        
        return isset($result['count']) ? (int)$result['count'] : 0;
    }
    
    /**
     * Récupère les statistiques des lots d'une ferme
     * 
     * @param int $farmId ID de la ferme
     * @return array Statistiques des lots
     */
    public function getFarmStats($farmId) {
        $sql = "SELECT 
                    COUNT(*) as total_batches,
                    SUM(CASE WHEN statut = 'actif' THEN 1 ELSE 0 END) as active_batches,
                    SUM(CASE WHEN statut = 'vendu' THEN 1 ELSE 0 END) as sold_batches,
                    SUM(CASE WHEN statut = 'perte totale' THEN 1 ELSE 0 END) as lost_batches,
                    SUM(effectif_initial) as total_poultry
                FROM {$this->table}
                WHERE id_ferme = :farm_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['farm_id' => $farmId]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
} 