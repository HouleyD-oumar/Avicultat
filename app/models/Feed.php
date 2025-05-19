<?php
/**
 * Modèle Feed
 * Gère les opérations liées à l'alimentation des volailles
 */
class Feed extends Model {
    protected $table = 'feeds';
    protected $primaryKey = 'id_feed';
    protected $fillable = ['type', 'quantite', 'date_distribution', 'id_lot'];
    
    /**
     * Récupère les distributions d'aliments d'un lot
     * 
     * @param int $lotId ID du lot
     * @return array Liste des distributions
     */
    public function getFeedsByLotId($lotId) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE id_lot = :lot_id 
                ORDER BY date_distribution DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':lot_id', $lotId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Ajoute une nouvelle distribution d'aliments
     * 
     * @param array $data Données de la distribution
     * @return int|bool ID de la nouvelle distribution ou false en cas d'échec
     */
    public function addFeed($data) {
        return $this->create($data);
    }
    
    /**
     * Met à jour une distribution d'aliments
     * 
     * @param int $id ID de la distribution
     * @param array $data Nouvelles données
     * @return bool Succès ou échec de la mise à jour
     */
    public function updateFeed($id, $data) {
        return $this->update($id, $data);
    }
    
    /**
     * Récupère les statistiques d'alimentation d'un lot
     * 
     * @param int $lotId ID du lot
     * @return array Statistiques d'alimentation
     */
    public function getFeedStats($lotId) {
        $sql = "SELECT 
                    COUNT(*) as total_distributions,
                    SUM(quantite) as total_quantite,
                    AVG(quantite) as moyenne_quantite,
                    MAX(date_distribution) as derniere_distribution
                FROM {$this->table}
                WHERE id_lot = :lot_id";
                
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':lot_id', $lotId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Vérifie le niveau de stock d'aliments
     * 
     * @param int $lotId ID du lot
     * @param float $seuil Seuil d'alerte en kg
     * @return bool True si le niveau est bas
     */
    public function checkLowStock($lotId, $seuil = 50) {
        $sql = "SELECT SUM(quantite) as stock_actuel 
                FROM {$this->table} 
                WHERE id_lot = :lot_id 
                AND date_distribution >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
                
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':lot_id', $lotId, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['stock_actuel'] < $seuil;
    }
    
    /**
     * Récupère l'historique des distributions d'un lot
     * 
     * @param int $lotId ID du lot
     * @param string $periode Période (jour, semaine, mois)
     * @return array Historique des distributions
     */
    public function getFeedHistory($lotId, $periode = 'mois') {
        $interval = 'INTERVAL 30 DAY'; // Valeur par défaut
        
        switch($periode) {
            case 'jour':
                $interval = 'INTERVAL 1 DAY';
                break;
            case 'semaine':
                $interval = 'INTERVAL 7 DAY';
                break;
        }
        
        $sql = "SELECT * FROM {$this->table} 
                WHERE id_lot = :lot_id 
                AND date_distribution >= DATE_SUB(CURDATE(), {$interval})
                ORDER BY date_distribution DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':lot_id', $lotId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Récupère les distributions d'aliments récentes d'un utilisateur
     * 
     * @param int $userId ID de l'utilisateur
     * @param int $limit Nombre maximum de résultats
     * @return array Liste des distributions récentes
     */
    public function getRecentFeedsByUserId($userId, $limit = 5) {
        $sql = "SELECT f.*, pb.race, fm.nom_ferme 
                FROM {$this->table} f
                INNER JOIN poultry_batches pb ON f.id_lot = pb.id_lot
                INNER JOIN farms fm ON pb.id_ferme = fm.id_ferme
                WHERE fm.id_user = :user_id
                ORDER BY f.date_distribution DESC
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} 