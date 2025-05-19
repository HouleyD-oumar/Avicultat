
<?php
/**
 * Modèle User
 * Gère les opérations liées aux utilisateurs (inscription, connexion, etc.)
 */
class User extends Model {
    protected $table = 'users';
    protected $primaryKey = 'id_user';
    protected $fillable = ['nom', 'prenom', 'email', 'mot_de_passe', 'role', 'photo_profil'];
    
    /**
     * Crée un nouvel utilisateur
     * 
     * @param array $data Données de l'utilisateur
     * @return int|false ID de l'utilisateur créé ou false en cas d'échec
     */
    public function register($data) {
        // Vérifier si l'email existe déjà
        if ($this->findByEmail($data['email'])) {
            return false;
        }
        
        // Hasher le mot de passe
        $data['mot_de_passe'] = password_hash($data['mot_de_passe'], PASSWORD_BCRYPT, ['cost' => HASH_COST]);
        
        // Définir un rôle par défaut si non spécifié
        if (!isset($data['role'])) {
            $data['role'] = 'eleveur';
        }
        
        // Créer l'utilisateur
        return $this->create($data);
    }
    
    /**
     * Authentifie un utilisateur
     * 
     * @param string $email Email de l'utilisateur
     * @param string $password Mot de passe en clair
     * @return array|false Données de l'utilisateur ou false si échec
     */
    public function login($email, $password) {
        $user = $this->findByEmail($email);
        
        if (!$user) {
            return false;
        }
        
        // Vérifier le mot de passe
        if (!password_verify($password, $user['mot_de_passe'])) {
            return false;
        }
        
        // Ne pas renvoyer le mot de passe
        unset($user['mot_de_passe']);
        
        return $user;
    }
    
    /**
     * Trouve un utilisateur par son email
     * 
     * @param string $email Email de l'utilisateur
     * @return array|false Données de l'utilisateur ou false si non trouvé
     */
    public function findByEmail($email) {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Met à jour le profil d'un utilisateur
     * 
     * @param int $id ID de l'utilisateur
     * @param array $data Données à mettre à jour
     * @return bool Succès ou échec de la mise à jour
     */
    public function updateProfile($id, $data) {
        // Si le mot de passe est fourni, le hasher
        if (isset($data['mot_de_passe']) && !empty($data['mot_de_passe'])) {
            $data['mot_de_passe'] = password_hash($data['mot_de_passe'], PASSWORD_BCRYPT, ['cost' => HASH_COST]);
        } else {
            // Ne pas mettre à jour le mot de passe s'il est vide
            unset($data['mot_de_passe']);
        }
        
        return $this->update($id, $data);
    }
    
    /**
     * Récupère tous les utilisateurs avec un rôle spécifique
     * 
     * @param string $role Rôle recherché
     * @return array Liste des utilisateurs
     */
    public function findByRole($role) {
        return $this->findBy(['role' => $role]);
    }
}