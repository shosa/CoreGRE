<?php
/**
 * Router Class
 * Gestisce il routing dell'applicazione con risoluzione corretta dei percorsi
 * Importante: gestisce percorsi quando l'app è 2 cartelle dentro la webroot
 */

class Router
{
    private $routes = [];
    private $basePath;
    private $requestUri;
    private $requestMethod;
    
    public function __construct()
    {
        $this->setupBasePath();
        $this->setupRequestUri();
        $this->requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }
    
    /**
     * Configura il basePath utilizzando la configurazione dell'applicazione
     */
    private function setupBasePath()
    {
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $scriptDir = dirname($scriptName);
        
        // Se è definita una subdirectory specifica nella configurazione
        if (defined('APP_SUBDIRECTORY') && APP_SUBDIRECTORY !== '') {
            // Verifica se siamo già nella subdirectory configurata
            if (basename($scriptDir) === APP_SUBDIRECTORY) {
                $this->basePath = $scriptDir;
            } else {
                // Cerca la subdirectory nel path
                $pathParts = explode('/', trim($scriptDir, '/'));
                $subIndex = array_search(APP_SUBDIRECTORY, $pathParts);
                
                if ($subIndex !== false) {
                    $this->basePath = '/' . implode('/', array_slice($pathParts, 0, $subIndex + 1));
                } else {
                    // Fallback: aggiungi la subdirectory al path corrente
                    $this->basePath = $scriptDir . '/' . APP_SUBDIRECTORY;
                }
            }
        } else {
            // Nessuna subdirectory configurata, usa il path dello script
            $this->basePath = $scriptDir;
        }
        
        // Normalizza il path
        $this->basePath = rtrim($this->basePath, '/');
        if (empty($this->basePath)) {
            $this->basePath = '';
        }
    }
    
    /**
     * Configura l'URI della richiesta rimuovendo il basePath
     */
    private function setupRequestUri()
    {
        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
        
        // Rimuovi query string se presente
        if (($pos = strpos($requestUri, '?')) !== false) {
            $requestUri = substr($requestUri, 0, $pos);
        }
        
        // Rimuovi il basePath dall'URI per ottenere il path relativo
        if ($this->basePath !== '/' && strpos($requestUri, $this->basePath) === 0) {
            $requestUri = substr($requestUri, strlen($this->basePath));
        }
        
        // Assicura che inizi con /
        if (empty($requestUri) || $requestUri[0] !== '/') {
            $requestUri = '/' . $requestUri;
        }
        
        $this->requestUri = $requestUri;
    }
    
    /**
     * Aggiunge una route GET
     */
    public function get($path, $handler)
    {
        $this->addRoute('GET', $path, $handler);
        return $this;
    }
    
    /**
     * Aggiunge una route POST
     */
    public function post($path, $handler)
    {
        $this->addRoute('POST', $path, $handler);
        return $this;
    }
    
    /**
     * Aggiunge una route PUT
     */
    public function put($path, $handler)
    {
        $this->addRoute('PUT', $path, $handler);
        return $this;
    }
    
    /**
     * Aggiunge una route DELETE
     */
    public function delete($path, $handler)
    {
        $this->addRoute('DELETE', $path, $handler);
        return $this;
    }
    
    /**
     * Aggiunge una route per qualsiasi metodo HTTP
     */
    public function any($path, $handler)
    {
        $methods = ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS'];
        foreach ($methods as $method) {
            $this->addRoute($method, $path, $handler);
        }
        return $this;
    }
    
    /**
     * Aggiunge una route al sistema
     */
    private function addRoute($method, $path, $handler)
    {
        // Normalizza il path
        $path = '/' . trim($path, '/');
        
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'pattern' => $this->convertToRegex($path)
        ];
    }
    
    /**
     * Converte un path in regex per il matching con parametri
     */
    private function convertToRegex($path)
    {
        // Converte {param} in regex groups
        $pattern = preg_replace('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', '([^/]+)', $path);
        return '#^' . $pattern . '$#';
    }
    
    /**
     * Risolve la route corrente
     */
    public function resolve()
    {
        foreach ($this->routes as $route) {
            if ($route['method'] === $this->requestMethod) {
                // Normalizza gli slash per il confronto
                $routePath = rtrim($route['path'], '/') ?: '/';
                $requestPath = rtrim($this->requestUri, '/') ?: '/';
                
                if ($routePath === $requestPath) {
                    // Match esatto
                    return $this->handleRoute($route, []);
                } elseif (preg_match($route['pattern'], $this->requestUri, $matches)) {
                    // Match con parametri
                    array_shift($matches); // Rimuovi il match completo
                    return $this->handleRoute($route, $matches);
                }
            }
        }
        
        // Nessuna route trovata, prova la route di default
        return $this->handleDefaultRoute();
    }
    
    /**
     * Gestisce l'esecuzione di una route
     */
    private function handleRoute($route, $params)
    {
        $handler = $route['handler'];
        
        if (is_string($handler)) {
            // Handler nel formato "Controller@method"
            if (strpos($handler, '@') !== false) {
                list($controllerName, $method) = explode('@', $handler, 2);
                return $this->callControllerMethod($controllerName, $method, $params);
            } else {
                // Solo nome del controller, usa il metodo di default
                return $this->callControllerMethod($handler, DEFAULT_ACTION, $params);
            }
        } elseif (is_callable($handler)) {
            // Handler è una closure
            return call_user_func_array($handler, $params);
        } else {
            throw new Exception("Invalid route handler");
        }
    }
    
    /**
     * Chiama un metodo di un controller
     */
    private function callControllerMethod($controllerName, $method, $params)
    {
        $controllerClass = $controllerName . 'Controller';
        
        if (!class_exists($controllerClass)) {
            throw new Exception("Controller {$controllerClass} not found");
        }
        
        $controller = new $controllerClass();
        
        if (!method_exists($controller, $method)) {
            throw new Exception("Method {$method} not found in controller {$controllerClass}");
        }
        
        return call_user_func_array([$controller, $method], $params);
    }
    
    /**
     * Gestisce la route di default quando nessuna route matcha
     */
    private function handleDefaultRoute()
    {
        // Prova a parsare l'URI come Controller/Action/Params
        $segments = explode('/', trim($this->requestUri, '/'));
        
        $controllerName = !empty($segments[0]) ? ucfirst($segments[0]) : DEFAULT_CONTROLLER;
        $method = !empty($segments[1]) ? $segments[1] : DEFAULT_ACTION;
        $params = array_slice($segments, 2);
        
        try {
            return $this->callControllerMethod($controllerName, $method, $params);
        } catch (Exception $e) {
            // Se anche la route di default fallisce, mostra 404
            $this->show404();
        }
    }
    
    /**
     * Mostra pagina 404
     */
    private function show404()
    {
        http_response_code(404);
        if (class_exists('ErrorController')) {
            $controller = new ErrorController();
            if (method_exists($controller, 'notFound')) {
                return $controller->notFound();
            }
        }
        
        // Fallback 404
        echo "404 - Page not found";
        exit;
    }
    
    /**
     * Ottiene l'URL base dell'applicazione
     */
    public function getBaseUrl()
    {
        // Se BASE_URL è già definita dalla configurazione, usala
        if (defined('BASE_URL')) {
            return BASE_URL;
        }
        
        // Fallback per compatibilità
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return $protocol . '://' . $host . $this->basePath;
    }
    
    /**
     * Genera un URL per una route specifica
     */
    public function url($path = '', $params = [])
    {
        $path = '/' . ltrim($path, '/');
        $url = $this->getBaseUrl() . $path;
        
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        
        return $url;
    }
}