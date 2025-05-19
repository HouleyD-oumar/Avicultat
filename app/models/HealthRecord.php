<?php
/**
 * Modèle HealthRecord
 * Gère les opérations liées aux dossiers de santé des volailles
 */
class HealthRecord extends Model {
    protected $table = 'health_records';
    protected $primaryKey = 'id';
    protected $fillable = ['lot_id', 'date_examen', 'type_examen', 'resultat', 'observations', 'id_veterinaire'];
    
    /**
     * Récupère les dossiers de santé d'un lot
     * 
     * @param int $lotId ID du lot
     * @return array Liste des dossiers de santé
     */
    public function getRecordsByLotId($lotId) {
        $sql = "SELECT hr.*, u.username as veterinaire_nom 
                FROM {$this->table} hr 
                LEFT JOIN users u ON hr.id_veterinaire = u.id 
                WHERE hr.lot_id = :lot_id 
                ORDER BY hr.date_examen DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':lot_id', $lotId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Ajoute un nouveau dossier de santé
     * 
     * @param array $data Données du dossier
     * @return int|bool ID du nouveau dossier ou false en cas d'échec
     */
    public function addRecord($data) {
        return $this->create($data);
    }
    
    /**
     * Met à jour un dossier de santé
     * 
     * @param int $id ID du dossier
     * @param array $data Nouvelles données
     * @return bool Succès ou échec de la mise à jour
     */
    public function updateRecord($id, $data) {
        return $this->update($id, $data);
    }
    
    /**
     * Récupère les statistiques de santé d'un lot
     * 
     * @param int $lotId ID du lot
     * @return array Statistiques de santé
     */
    public function getHealthStats($lotId) {
        $sql = "SELECT 
                    COUNT(*) as total_examens,
                    COUNT(CASE WHEN resultat = 'normal' THEN 1 END) as examens_normaux,
                    COUNT(CASE WHEN resultat = 'anormal' THEN 1 END) as examens_anormaux,
                    MAX(date_examen) as dernier_examen
                FROM {$this->table}
                WHERE lot_id = :lot_id";
                
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':lot_id', $lotId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
} 