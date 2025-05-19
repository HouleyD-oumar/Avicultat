<?php
/**
 * Modèle ForumPost
 * Gère les opérations liées aux sujets du forum
 */
class ForumPost extends Model {
    protected $table = 'forum_posts';
    protected $primaryKey = 'id_post';
    protected $fillable = ['titre', 'contenu', 'date_post', 'id_user'];
    
    /**
     * Récupère les derniers sujets du forum
     * 
     * @param int $limit Nombre de sujets à récupérer
     * @return array Liste des derniers sujets
     */
    public function getRecentPosts($limit = 5) {
        $sql = "SELECT p.*, u.nom, u.prenom, 
                (SELECT COUNT(*) FROM forum_replies WHERE id_post = p.id_post) as nombre_reponses
                FROM {$this->table} p 
                JOIN users u ON p.id_user = u.id_user 
                ORDER BY p.date_post DESC 
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Récupère tous les sujets du forum
     * 
     * @param int $limit Nombre de sujets à récupérer
     * @param int $offset Offset pour la pagination
     * @return array Liste des sujets
     */
    public function getAllTopics($limit = 10, $offset = 0) {
        $sql = "SELECT p.*, u.nom, u.prenom, 
                (SELECT COUNT(*) FROM forum_replies WHERE id_post = p.id_post) as nombre_reponses
                FROM {$this->table} p 
                JOIN users u ON p.id_user = u.id_user 
                ORDER BY p.date_post DESC 
                LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Récupère un sujet avec ses réponses
     * 
     * @param int $id ID du sujet
     * @return array Données du sujet avec ses réponses
     */
    public function getTopicWithReplies($id) {
        // Récupérer le sujet
        $sql = "SELECT p.*, u.nom, u.prenom 
                FROM {$this->table} p 
                JOIN users u ON p.id_user = u.id_user 
                WHERE p.id_post = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $topic = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$topic) {
            return null;
        }
        
        // Récupérer les réponses
        $sql = "SELECT r.*, u.nom, u.prenom 
                FROM forum_replies r 
                JOIN users u ON r.id_user = u.id_user 
                WHERE r.id_post = :post_id 
                ORDER BY r.date_reply ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':post_id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $topic['replies'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $topic;
    }
    
    /**
     * Crée un nouveau sujet
     * 
     * @param array $data Données du sujet
     * @return int|bool ID du nouveau sujet ou false en cas d'échec
     */
    public function createTopic($data) {
        $data['date_post'] = date('Y-m-d H:i:s');
        return $this->create($data);
    }
    
    /**
     * Met à jour un sujet
     * 
     * @param int $id ID du sujet
     * @param array $data Nouvelles données
     * @return bool Succès ou échec de la mise à jour
     */
    public function updateTopic($id, $data) {
        return $this->update($id, $data);
    }
    
    /**
     * Recherche des sujets
     * 
     * @param string $query Terme de recherche
     * @return array Liste des sujets correspondants
     */
    public function searchTopics($query) {
        $sql = "SELECT p.*, u.nom, u.prenom 
                FROM {$this->table} p 
                JOIN users u ON p.id_user = u.id_user 
                WHERE p.titre LIKE :query OR p.contenu LIKE :query
                ORDER BY p.date_post DESC";
        
        $stmt = $this->db->prepare($sql);
        $searchTerm = "%{$query}%";
        $stmt->bindParam(':query', $searchTerm, PDO::PARAM_STR);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Récupère les sujets paginés avec recherche et filtrage par catégorie
     */
    public function getPaginatedPosts($page, $perPage, $search = '', $category = '') {
        $offset = ($page - 1) * $perPage;
        $params = [];
        $where = [];
        
        if (!empty($search)) {
            $where[] = "(title LIKE ? OR content LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        if (!empty($category)) {
            $where[] = "category = ?";
            $params[] = $category;
        }
        
        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        
        $sql = "SELECT p.*, u.username as author_name, 
                (SELECT COUNT(*) FROM forum_comments WHERE post_id = p.id) as comment_count
                FROM {$this->table} p
                LEFT JOIN users u ON p.user_id = u.id
                $whereClause
                ORDER BY p.created_at DESC
                LIMIT ? OFFSET ?";
        
        $params[] = $perPage;
        $params[] = $offset;
        
        return $this->query($sql, $params);
    }
    
    /**
     * Compte le nombre total de sujets avec filtres
     */
    public function countPosts($search = '', $category = '') {
        $params = [];
        $where = [];
        
        if (!empty($search)) {
            $where[] = "(title LIKE ? OR content LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        if (!empty($category)) {
            $where[] = "category = ?";
            $params[] = $category;
        }
        
        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        
        $sql = "SELECT COUNT(*) as total FROM {$this->table} $whereClause";
        $result = $this->query($sql, $params);
        
        return $result[0]['total'] ?? 0;
    }
    
    /**
     * Récupère un sujet avec les informations de l'auteur
     */
    public function find($id) {
        $sql = "SELECT p.*, u.username as author_name
                FROM {$this->table} p
                LEFT JOIN users u ON p.user_id = u.id
                WHERE p.id = ?";
        
        $result = $this->query($sql, [$id]);
        return $result[0] ?? null;
    }
    
    /**
     * Récupère les commentaires d'un sujet
     */
    public function getComments($post_id) {
        $sql = "SELECT c.*, u.username as author_name
                FROM forum_comments c
                LEFT JOIN users u ON c.user_id = u.id
                WHERE c.post_id = ?
                ORDER BY c.created_at ASC";
        
        return $this->query($sql, [$post_id]);
    }
    
    /**
     * Récupère un commentaire spécifique
     */
    public function getComment($id) {
        $sql = "SELECT * FROM forum_comments WHERE id = ?";
        $result = $this->query($sql, [$id]);
        return $result[0] ?? null;
    }
    
    /**
     * Ajoute un commentaire
     */
    public function addComment($data) {
        $sql = "INSERT INTO forum_comments (post_id, user_id, content, created_at)
                VALUES (?, ?, ?, NOW())";
        
        return $this->query($sql, [
            $data['post_id'],
            $data['user_id'],
            $data['content']
        ]);
    }
    
    /**
     * Supprime un commentaire
     */
    public function deleteComment($id) {
        $sql = "DELETE FROM forum_comments WHERE id = ?";
        return $this->query($sql, [$id]);
    }
    
    /**
     * Crée un nouveau sujet
     */
    public function create($data) {
        $sql = "INSERT INTO {$this->table} (title, content, category, user_id, created_at)
                VALUES (?, ?, ?, ?, NOW())";
        
        return $this->query($sql, [
            $data['title'],
            $data['content'],
            $data['category'],
            $data['user_id']
        ]);
    }
    
    /**
     * Met à jour un sujet
     */
    public function update($id, $data) {
        $sql = "UPDATE {$this->table}
                SET title = ?, content = ?, category = ?
                WHERE id = ?";
        
        return $this->query($sql, [
            $data['title'],
            $data['content'],
            $data['category'],
            $id
        ]);
    }
    
    /**
     * Supprime un sujet et ses commentaires
     */
    public function delete($id) {
        // Supprimer d'abord les commentaires
        $sql = "DELETE FROM forum_comments WHERE post_id = ?";
        $this->query($sql, [$id]);
        
        // Puis supprimer le sujet
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        return $this->query($sql, [$id]);
    }
} 