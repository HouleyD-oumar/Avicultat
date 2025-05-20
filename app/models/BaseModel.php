<?php
/**
 * Classe de base pour tous les modèles
 */
class BaseModel {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    
    /**
     * Constructeur
     */
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Récupère tous les enregistrements
     * 
     * @param string $orderBy Champ pour le tri
     * @param string $order Direction du tri (ASC ou DESC)
     * @return array
     */
    public function findAll($orderBy = null, $order = 'ASC') {
        $sql = "SELECT * FROM {$this->table}";
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy} {$order}";
        }
        return $this->db->query($sql)->fetchAll();
    }
    
    /**
     * Récupère un enregistrement par son ID
     * 
     * @param int $id ID de l'enregistrement
     * @return array|false
     */
    public function findById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id";
        return $this->db->query($sql, ['id' => $id])->fetch();
    }
    
    /**
     * Crée un nouvel enregistrement
     * 
     * @param array $data Données à insérer
     * @return int|false ID du nouvel enregistrement ou false en cas d'échec
     */
    public function create($data) {
        // Filtrer les données selon les champs autorisés
        $data = array_intersect_key($data, array_flip($this->fillable));
        
        if (empty($data)) {
            error_log("Erreur : Aucune donnée valide à insérer");
            return false;
        }
        
        $fields = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_map(function($field) {
            return ":{$field}";
        }, array_keys($data)));
        
        $sql = "INSERT INTO {$this->table} ({$fields}) VALUES ({$placeholders})";
        
        try {
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute($data);
            
            if ($result) {
                return $this->db->lastInsertId();
            } else {
                error_log("Erreur lors de l'exécution de la requête : " . implode(", ", $stmt->errorInfo()));
                return false;
            }
        } catch (PDOException $e) {
            error_log("Erreur PDO lors de la création : " . $e->getMessage());
            return false;
        } catch (Exception $e) {
            error_log("Erreur lors de la création : " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Met à jour un enregistrement
     * 
     * @param int $id ID de l'enregistrement
     * @param array $data Données à mettre à jour
     * @return bool
     */
    public function update($id, $data) {
        // Filtrer les données selon les champs autorisés
        $data = array_intersect_key($data, array_flip($this->fillable));
        
        if (empty($data)) {
            error_log("Erreur : Aucune donnée valide à mettre à jour");
            return false;
        }
        
        $fields = implode(', ', array_map(function($field) {
            return "{$field} = :{$field}";
        }, array_keys($data)));
        
        $sql = "UPDATE {$this->table} SET {$fields} WHERE {$this->primaryKey} = :id";
        
        try {
            $stmt = $this->db->prepare($sql);
            $data['id'] = $id;
            $result = $stmt->execute($data);
            
            if ($result) {
                return true;
            } else {
                error_log("Erreur lors de l'exécution de la requête : " . implode(", ", $stmt->errorInfo()));
                return false;
            }
        } catch (PDOException $e) {
            error_log("Erreur PDO lors de la mise à jour : " . $e->getMessage());
            return false;
        } catch (Exception $e) {
            error_log("Erreur lors de la mise à jour : " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Supprime un enregistrement
     * 
     * @param int $id ID de l'enregistrement
     * @return bool
     */
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        
        try {
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute(['id' => $id]);
            
            if ($result) {
                return true;
            } else {
                error_log("Erreur lors de l'exécution de la requête : " . implode(", ", $stmt->errorInfo()));
                return false;
            }
        } catch (PDOException $e) {
            error_log("Erreur PDO lors de la suppression : " . $e->getMessage());
            return false;
        } catch (Exception $e) {
            error_log("Erreur lors de la suppression : " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Compte le nombre d'enregistrements
     * 
     * @param array $conditions Conditions de recherche
     * @return int
     */
    public function count($conditions = []) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        $params = [];
        
        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $field => $value) {
                $where[] = "{$field} = :{$field}";
                $params[$field] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        
        $result = $this->db->query($sql, $params)->fetch();
        return (int) $result['count'];
    }
    
    /**
     * Pagine les résultats
     * 
     * @param array $conditions Conditions de recherche
     * @param int $page Numéro de la page
     * @param int $perPage Nombre d'éléments par page
     * @return array
     */
    public function paginate($conditions = [], $page = 1, $perPage = 10) {
        $offset = ($page - 1) * $perPage;
        $total = $this->count($conditions);
        
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        
        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $field => $value) {
                $where[] = "{$field} = :{$field}";
                $params[$field] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        
        $sql .= " LIMIT {$perPage} OFFSET {$offset}";
        
        $data = $this->db->query($sql, $params)->fetchAll();
        
        return [
            'data' => $data,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => ceil($total / $perPage)
        ];
    }
} 