<?php
/**
 * Classe de base Model
 * Sert de classe mère pour tous les modèles de l'application
 * Gère la connexion à la base de données et les opérations CRUD de base
 */
class Model {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    
    /**
     * Constructeur - établit la connexion à la base de données
     */
    public function __construct() {
        try {
            require_once dirname(__DIR__) . '/config/config.php';
            $this->db = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
                DB_USER,
                DB_PASS,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch (PDOException $e) {
            die('Erreur de connexion à la base de données : ' . $e->getMessage());
        }
    }
    
    /**
     * Récupère tous les enregistrements de la table
     * 
     * @param string $orderBy Champ pour le tri
     * @param string $order Direction du tri (ASC ou DESC)
     * @return array Tableau d'objets
     */
    public function findAll($orderBy = 'id', $order = 'ASC') {
        $sql = "SELECT * FROM {$this->table} ORDER BY {$orderBy} {$order}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Récupère un enregistrement par son ID
     * 
     * @param int $id Identifiant de l'enregistrement
     * @return array|false Données de l'enregistrement ou false si non trouvé
     */
    public function findById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Crée un nouvel enregistrement
     * 
     * @param array $data Données à insérer
     * @return int|false ID de l'enregistrement créé ou false en cas d'échec
     */
    public function create($data) {
        // Filtrer les clés qui ne correspondent pas aux colonnes de la table
        $filteredData = array_intersect_key($data, array_flip($this->fillable ?? []));
        
        $columns = implode(', ', array_keys($filteredData));
        $placeholders = ':' . implode(', :', array_keys($filteredData));
        
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);
        
        foreach ($filteredData as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Met à jour un enregistrement existant
     * 
     * @param int $id ID de l'enregistrement à mettre à jour
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
        
        $sql = "UPDATE {$this->table} SET {$setClause} WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        foreach ($filteredData as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        
        return $stmt->execute();
    }
    
    /**
     * Supprime un enregistrement
     * 
     * @param int $id ID de l'enregistrement à supprimer
     * @return bool Succès ou échec de la suppression
     */
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    /**
     * Recherche des enregistrements selon des critères
     * 
     * @param array $criteria Critères de recherche [champ => valeur]
     * @return array Résultats de la recherche
     */
    public function findBy($criteria) {
        $whereClause = '';
        foreach (array_keys($criteria) as $key) {
            $whereClause .= "{$key} = :{$key} AND ";
        }
        $whereClause = rtrim($whereClause, ' AND ');
        
        $sql = "SELECT * FROM {$this->table} WHERE {$whereClause}";
        $stmt = $this->db->prepare($sql);
        
        foreach ($criteria as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}