<?php
/**
 * Classe Database
 * Gère la connexion à la base de données via PDO
 */
class Database {
    private static $instance = null;
    private $connection;
    
    /**
     * Constructeur privé pour empêcher l'instanciation directe
     */
    private function __construct() {
        try {
            // Création de la connexion PDO
            $this->connection = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            // Log de l'erreur
            error_log("Erreur de connexion à la base de données : " . $e->getMessage());
            throw new Exception("Impossible de se connecter à la base de données");
        }
    }
    
    /**
     * Empêche le clonage de l'instance
     */
    private function __clone() {}
    
    /**
     * Retourne l'instance unique de la classe
     * 
     * @return Database
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Retourne la connexion PDO
     * 
     * @return PDO
     */
    public function getConnection() {
        return $this->connection;
    }
    
    /**
     * Exécute une requête SQL avec des paramètres
     * 
     * @param string $sql Requête SQL
     * @param array $params Paramètres de la requête
     * @return PDOStatement
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Erreur SQL : " . $e->getMessage());
            throw new Exception("Erreur lors de l'exécution de la requête");
        }
    }
    
    /**
     * Récupère le dernier ID inséré
     * 
     * @return string
     */
    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }
    
    /**
     * Démarre une transaction
     */
    public function beginTransaction() {
        $this->connection->beginTransaction();
    }
    
    /**
     * Valide une transaction
     */
    public function commit() {
        $this->connection->commit();
    }
    
    /**
     * Annule une transaction
     */
    public function rollback() {
        $this->connection->rollBack();
    }
} 