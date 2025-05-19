<?php
/**
 * Modèle Treatment
 * Gère les opérations liées aux traitements vétérinaires
 */
class Treatment extends Model {
    protected $table = 'treatments';
    protected $primaryKey = 'id_traitement';
    protected $fillable = ['produit', 'posologie', 'date_application', 'observations', 'id_lot'];
    
    /**
     * Récupère les traitements récents pour les vétérinaires
     * 
     * @param int $limit Nombre maximum de traitements à récupérer
     * @return array Liste des traitements récents
     */
    public function getRecentTreatments($limit = 10) {
        $sql = "SELECT t.*, pb.race, f.nom_ferme as farm_name, u.nom, u.prenom as eleveur_nom
                FROM {$this->table} t 
                JOIN poultry_batches pb ON t.id_lot = pb.id_lot 
                JOIN farms f ON pb.id_ferme = f.id_ferme 
                JOIN users u ON f.id_user = u.id_user
                ORDER BY t.date_application DESC 
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Récupère les traitements récents d'un utilisateur
     * 
     * @param int $userId ID de l'utilisateur
     * @param int $limit Nombre maximum de traitements à récupérer
     * @return array Liste des traitements récents
     */
    public function getRecentTreatmentsByUserId($userId, $limit = 5) {
        $sql = "SELECT t.*, pb.race, f.nom_ferme as farm_name 
                FROM {$this->table} t 
                JOIN poultry_batches pb ON t.id_lot = pb.id_lot 
                JOIN farms f ON pb.id_ferme = f.id_ferme 
                WHERE f.id_user = :user_id 
                ORDER BY t.date_application DESC 
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Récupère les traitements d'un lot
     * 
     * @param int $lotId ID du lot
     * @return array Liste des traitements
     */
    public function getTreatmentsByLotId($lotId) {
        $sql = "SELECT t.*, u.nom, u.prenom as veterinaire_nom 
                FROM {$this->table} t 
                LEFT JOIN users u ON t.id_veterinaire = u.id_user 
                WHERE t.id_lot = :lot_id 
                ORDER BY t.date_application DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':lot_id', $lotId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Ajoute un nouveau traitement
     * 
     * @param array $data Données du traitement
     * @return int|bool ID du nouveau traitement ou false en cas d'échec
     */
    public function addTreatment($data) {
        return $this->create($data);
    }
    
    /**
     * Met à jour un traitement
     * 
     * @param int $id ID du traitement
     * @param array $data Nouvelles données
     * @return bool Succès ou échec de la mise à jour
     */
    public function updateTreatment($id, $data) {
        return $this->update($id, $data);
    }
    
    /**
     * Récupère les traitements à venir pour un lot
     * 
     * @param int $lotId ID du lot
     * @return array Liste des traitements à venir
     */
    public function getUpcomingTreatments($lotId) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE id_lot = :lot_id 
                AND date_application >= CURDATE() 
                ORDER BY date_application ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':lot_id', $lotId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Récupère l'historique des traitements d'un lot
     * 
     * @param int $lotId ID du lot
     * @return array Historique des traitements
     */
    public function getTreatmentHistory($lotId) {
        $sql = "SELECT t.*, u.nom, u.prenom as veterinaire_nom 
                FROM {$this->table} t 
                LEFT JOIN users u ON t.id_veterinaire = u.id_user 
                WHERE t.id_lot = :lot_id 
                ORDER BY t.date_application DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':lot_id', $lotId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} 