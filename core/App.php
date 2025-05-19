<?php
/**
 * Classe App - Routeur principal de l'application
 * Gère le routage des requêtes vers les contrôleurs appropriés
 */
class App {
    protected $controller = 'HomeController';
    protected $method = 'index';
    protected $params = [];
    protected $controllerName = 'HomeController';
    
    /**
     * Constructeur - Analyse l'URL et détermine le contrôleur, la méthode et les paramètres
     */
    public function __construct() {
        // Démarrer la session si elle n'est pas déjà active
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Récupérer l'URL
        $url = $this->parseUrl();
        
        // Gestion des routes imbriquées
        if (isset($url[0]) && $url[0] === 'farms' && isset($url[1]) && is_numeric($url[1])) {
            $farmId = $url[1];
            unset($url[0], $url[1]);
            
            if (isset($url[2]) && $url[2] === 'batches') {
                $this->controller = 'PoultryBatchController';
                $this->controllerName = 'PoultryBatchController';
                unset($url[2]);
                
                // Ajouter farmId comme premier paramètre
                $this->params[] = $farmId;
            }
        } else {
            // Logique de routage standard
            if (isset($url[0])) {
                $potentialController = ucfirst($url[0]) . 'Controller';
                if (file_exists(dirname(__DIR__) . '/app/controllers/' . $potentialController . '.php')) {
                    $this->controller = $potentialController;
                    $this->controllerName = $potentialController;
                    unset($url[0]);
                } else {
                    // Rediriger vers la page 404 si le contrôleur n'existe pas
                    header("HTTP/1.0 404 Not Found");
                    require_once dirname(__DIR__) . '/app/views/errors/404.php';
                    exit;
                }
            }
        }
        
        // Charger le contrôleur
        require_once dirname(__DIR__) . '/app/controllers/' . $this->controller . '.php';
        $this->controller = new $this->controller();
        
        // Déterminer la méthode
        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            } else {
                // Rediriger vers la page 404 si la méthode n'existe pas
                header("HTTP/1.0 404 Not Found");
                require_once dirname(__DIR__) . '/app/views/errors/404.php';
                exit;
            }
        }
        
        // Ajouter les paramètres restants de l'URL
        $this->params = array_merge($this->params, array_values($url));
        
        // Récupérer les paramètres de la méthode
        $methodReflection = new ReflectionMethod($this->controller, $this->method);
        $requiredParams = $methodReflection->getParameters();
        
        // Vérifier si nous avons assez de paramètres
        if (count($this->params) < count($requiredParams)) {
            // Si nous n'avons pas assez de paramètres, essayer de les récupérer de $_GET ou $_POST
            $this->params = $this->prepareMethodParameters($requiredParams);
        }
        
        // Appeler la méthode du contrôleur avec les paramètres
        call_user_func_array([$this->controller, $this->method], $this->params);
    }
    
    /**
     * Prépare les paramètres pour l'appel de la méthode
     * 
     * @param array $requiredParams Paramètres requis par la méthode
     * @return array Paramètres préparés
     */
    protected function prepareMethodParameters($requiredParams) {
        $params = [];
        $paramIndex = 0;
        
        foreach ($requiredParams as $param) {
            $paramName = $param->getName();
            
            // Utiliser les paramètres déjà préparés
            if (isset($this->params[$paramIndex])) {
                $params[] = $this->params[$paramIndex];
                $paramIndex++;
                continue;
            }
            
            // Chercher dans $_GET
            if (isset($_GET[$paramName])) {
                $params[] = $_GET[$paramName];
            }
            // Chercher dans $_POST
            elseif (isset($_POST[$paramName])) {
                $params[] = $_POST[$paramName];
            }
            // Si le paramètre est optionnel, utiliser la valeur par défaut
            elseif ($param->isOptional()) {
                $params[] = $param->getDefaultValue();
            }
            // Si le paramètre est requis mais non fourni, lancer une exception
            else {
                throw new ArgumentCountError(
                    "Le paramètre '{$paramName}' est requis pour la méthode {$this->method} " .
                    "du contrôleur {$this->controllerName}. " .
                    "URL attendue : /farms/{farmId}/batches"
                );
            }
        }
        
        return $params;
    }
    
    /**
     * Parse l'URL pour extraire les composants
     * 
     * @return array Composants de l'URL
     */
    protected function parseUrl() {
        if (isset($_GET['url'])) {
            // Nettoyer l'URL
            $url = filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL);
            // Diviser l'URL en segments
            return explode('/', $url);
        }
        
        return [];
    }
    
    /**
     * Méthode statique pour charger les classes automatiquement
     * 
     * @param string $className Nom de la classe à charger
     * @return void
     */
    public static function autoload($className) {
        // Chemins possibles pour les classes
        $paths = [
            dirname(__DIR__) . '/core/',
            dirname(__DIR__) . '/app/controllers/',
            dirname(__DIR__) . '/app/models/'
        ];
        
        foreach ($paths as $path) {
            $file = $path . $className . '.php';
            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
    }
    
    /**
     * Méthode statique pour démarrer l'application
     * 
     * @return void
     */
    public static function run() {
        // Enregistrer la fonction d'autoload
        spl_autoload_register(['App', 'autoload']);
        
        // Créer une instance de l'application
        new self();
    }
}