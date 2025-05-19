<?php
/**
 * Modèle Farm
 * Gère les opérations liées aux fermes
 */
class Farm extends Model {
    protected $table = 'farms';
    protected $primaryKey = 'id_ferme';
    protected $fillable = ['nom_ferme', 'localisation', 'date_creation', 'id_user'];
    
    /**
     * Récupère les fermes d'un utilisateur
     * 
     * @param int $userId ID de l'utilisateur
     * @return array Liste des fermes
     */
    public function getFarmsByUserId($userId) {
        $sql = "SELECT * FROM {$this->table} WHERE id_user = :user_id ORDER BY nom_ferme";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Vérifie si une ferme appartient à un utilisateur
     * 
     * @param int $farmId ID de la ferme
     * @param int $userId ID de l'utilisateur
     * @return bool True si la ferme appartient à l'utilisateur
     */
    public function belongsToUser($farmId, $userId) {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE id_ferme = :farm_id AND id_user = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':farm_id', $farmId, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Récupère les statistiques d'une ferme
     * 
     * @param int $farmId ID de la ferme
     * @return array Statistiques de la ferme
     */
    public function getStats($farmId) {
        $sql = "SELECT 
                    COUNT(DISTINCT pl.id_lot) as total_lots,
                    SUM(pl.effectif_initial) as total_volailles,
                    COUNT(DISTINCT CASE WHEN pl.statut = 'actif' THEN pl.id_lot END) as lots_actifs
                FROM {$this->table} f
                LEFT JOIN poultry_batches pl ON f.id_ferme = pl.id_ferme
                WHERE f.id_ferme = :farm_id";
                
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':farm_id', $farmId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Récupère toutes les fermes d'un utilisateur
     * 
     * @param int $userId ID de l'utilisateur
     * @return array Liste des fermes
     */
    public function findByUserId($userId) {
        return $this->findBy(['id_user' => $userId]);
    }
    
    /**
     * Récupère une ferme avec les informations de son propriétaire
     * 
     * @param int $farmId ID de la ferme
     * @return array|false Données de la ferme ou false si non trouvée
     */
    public function findWithOwner($farmId) {
        $sql = "SELECT f.*, u.nom, u.prenom, u.email 
                FROM {$this->table} f 
                JOIN users u ON f.id_user = u.id_user 
                WHERE f.id_ferme = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $farmId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Récupère toutes les fermes avec les informations de leurs propriétaires
     * 
     * @return array Liste des fermes avec leurs propriétaires
     */
    public function findAllWithOwners() {
        $sql = "SELECT f.*, u.nom, u.prenom 
                FROM {$this->table} f 
                JOIN users u ON f.id_user = u.id_user 
                ORDER BY f.nom_ferme ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Compte le nombre de lots actifs pour une ferme
     * 
     * @param int $farmId ID de la ferme
     * @return int Nombre de lots actifs
     */
    public function countActiveBatches($farmId) {
        $sql = "SELECT COUNT(*) as count 
                FROM poultry_batches 
                WHERE id_ferme = :farm_id AND statut = 'actif'";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':farm_id', $farmId, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }
    
    /**
     * Surcharge de la méthode delete pour utiliser id_ferme au lieu de id
     * 
     * @param int $id ID de la ferme à supprimer
     * @return bool Succès ou échec de la suppression
     */
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id_ferme = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    /**
     * Surcharge de la méthode update pour utiliser id_ferme au lieu de id
     * 
     * @param int $id ID de la ferme à mettre à jour
     * @param array $data Données à mettre à jour
     * @return bool Succès ou échec de la mise à jour
     */
    public function update($id, $data) {
        // Filtrer les clés qui ne correspondent pas aux colonnes de la table
        $filteredData = array_intersect_key($data, array_flip($this->fillable ?? []));
        
        $setClause = '';
        foreach (array_keys($filteredData) as $key) {
            $setClause .= "{$key} = :{$key}, ";
        }
        $setClause = rtrim($setClause, ', ');
        
        $sql = "UPDATE {$this->table} SET {$setClause} WHERE id_ferme = :id";
        $stmt = $this->db->prepare($sql);
        
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        foreach ($filteredData as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        
        return $stmt->execute();
    }
    
    /**
     * Vérifie si un utilisateur est propriétaire d'une ferme
     * 
     * @param int $farmId ID de la ferme
     * @param int $userId ID de l'utilisateur
     * @return bool True si l'utilisateur est propriétaire de la ferme
     */
    public function isOwner($farmId, $userId) {
        return $this->belongsToUser($farmId, $userId);
    }
}