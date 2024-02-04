<?php

namespace App;

use App\Controllers\UtilisateurController;
use App\Controllers\RegisterController;
use App\Controllers\ConfirmController;
use App\Controllers\LoginController;
use App\Controllers\AdminController;
use App\Controllers\AcceuilController;

use App\Views\ViewReinscr\HeaderView;
use App\Views\ViewReinscr\FooterView;
use App\Views\ViewReinscr\LoginView;


use App\Views\EspaceAdmin\IndexView;
use App\Views\EspaceAdmin\HeaderAdminView;
use App\Views\EspaceAdmin\FooterAdminView;

use App\Views\EspaceEtudiant\IndexEtudiantView;
use App\Views\EspaceEtudiant\HeaderEtudiantView;
use App\Views\EspaceEtudiant\FooterEtudiantView;

use App\Views\EspaceProf\IndexProfView;
use App\Views\EspaceProf\HeaderProfView;
use App\Views\EspaceProf\FooterProfView;

use App\Models\UtilisateurModel; // Import the correct namespace
use App\Models\AdminModel; // Import the correct namespace
use App\Models\AvisModel; // Import the correct namespace
use App\Models\EmploiModel; // Import the correct namespace
use App\Models\UtilisateurEnsModel;

class Router {
    private $routes = [];
    private $headerView;
    private $footerView;
    private $headerProfView;
    private $footerProfView;
    private $headerEtudiantView;
    private $footerEtudiantView;
    private $utilisateurModel;
    private $adminModel;
    private $avismodel;
    private $emploiModel;
    private $UtilisateurEnsModel;
    private $middlewareStack = [];


    public function __construct() {
        $this->headerView = new HeaderView();
        $this->footerView = new FooterView();
        
        $this->headerAdminView=new HeaderAdminView();
        $this->footerAdminView=new FooterAdminView();
        $this->headerEtudiantView=new HeaderEtudiantView();
        $this->footerEtudiantView=new FooterEtudiantView();
        $this->headerProfView=new HeaderProfView();
        $this->footerProfView=new FooterProfView();
        $this->utilisateurModel = new UtilisateurModel(); // Use the correct model here
        $this->adminModel = new AdminModel(); // Use the correct model here
        $this->avismodel = new AvisModel(); // Use the correct model here
        $this->emploiModel = new EmploiModel(); // Use the correct model here
        $this->UtilisateurEnsModel = new UtilisateurEnsModel(); // Use the correct model here
        $this->middlewareStack = [];

    }

public function addRoute($url, $controller, $action, $requestMethod ,$middleware) {
    $this->routes[$url] = [
        'controller' => $controller,
        'action' => $action,
        'requestMethod' => $requestMethod,
        'middleware' => $middleware,

    ];
}


public function dispatch($url, $requestMethod) {
    try {
        $routeConfig = $this->findRoute($url, $requestMethod);

        if ($routeConfig) {
            $this->applyMiddlewareStack($url, $requestMethod);

            $this->executeRoute($routeConfig['controller'], $routeConfig['action'], $url);
        } else {
            $this->handleNotFound();
        }

    } catch (\Exception $e) {
        $this->handleException($e);
    }
}

private function applyMiddlewareStack($url, $requestMethod) {
    foreach ($this->routes as $route => $config) {
        if ($this->urlMatchesRoute($url, $route) && $config['requestMethod'] === $requestMethod) {
            foreach ($config['middleware'] as $middleware) {
                if (is_array($middleware) && isset($middleware['function']) && is_callable($middleware['function'])) {
                    // Check if roles are defined for the middleware
                    $allowedRoles = isset($middleware['roles']) ? $middleware['roles'] : [];
                    
                    // Pass the allowed roles to the middleware
                    call_user_func($middleware['function'], $url, $requestMethod, $allowedRoles);
                } elseif (is_callable($middleware)) {
                    // Legacy middleware without roles support
                    call_user_func($middleware, $url, $requestMethod);
                }
            }
            break; // Stop after applying middleware for the matched route
        }
    }
}


private function urlMatchesRoute($url, $route) {
    $pattern = '/^' . str_replace('/', '\/', $route) . '$/';
    return (bool) preg_match($pattern, $url);
}



    private function findRoute($url, $requestMethod) {
        foreach ($this->routes as $route => $config) {
            // Parse the URL to get the path and query parameters
            $parsedUrl = parse_url($url);
            $path = $parsedUrl['path'];
            $queryParams = [];

            // If query parameters are present, parse them
            if (isset($parsedUrl['query'])) {
                parse_str($parsedUrl['query'], $queryParams);
            }

            // Escape the route pattern for regex
            $pattern = '/^' . str_replace('/', '\/', $route) . '$/';

            // Check if the URL path matches the pattern and the request method is correct
            if (preg_match($pattern, $path) && $_SERVER['REQUEST_METHOD'] === $requestMethod) {
                // Ensure $config['params'] is an array
                $config['params'] = isset($config['params']) && is_array($config['params']) ? $config['params'] : [];

                // Merge route parameters with query parameters
                $urlParams = array_merge($config['params'], $queryParams);

                // Add the merged parameters to the config
                $config['params'] = $urlParams;

                return $config;
            }
        }

        return null;
    }

    private function executeRoute($controllerName, $actionName, $urlParams) {
        $controllerNamespace = "App\\Controllers\\$controllerName";

        if (!class_exists($controllerNamespace)) {
            throw new \Exception("Controller class not found: $controllerNamespace");
        }

        $controller = $this->instantiateController($controllerNamespace);

        if (!method_exists($controller, $actionName)) {
            throw new \Exception("Action method not found in controller: $actionName");
        }

        // Ensure that $urlParams is an array
        $urlParams = is_array($urlParams) ? $urlParams : [$urlParams];

        // Call the specified action on the controller and pass route parameters
        call_user_func_array([$controller, $actionName], $urlParams);
    }

    private function instantiateController($controllerNamespace) {
        // Check the controller name to determine the appropriate view and model
        if ($controllerNamespace === 'App\\Controllers\\RegisterController') {
            $registerView = new \App\Views\ViewReinscr\RegisterView($this->headerView, $this->footerView);
            return new $controllerNamespace($this->utilisateurModel, $registerView);
        } elseif ($controllerNamespace === 'App\\Controllers\\UtilisateurController') {
            $utilisateurView = new \App\Views\ViewReinscr\UtilisateurView($this->headerView, $this->footerView);
            return new $controllerNamespace($this->utilisateurModel, $utilisateurView);
        } elseif ($controllerNamespace === 'App\\Controllers\\ConfirmController') {
            // Instantiate ConfirmController with the correct model and view
            $utilisateurModel = new UtilisateurModel(); // Use the correct model here
            return new $controllerNamespace($utilisateurModel);
        } elseif ($controllerNamespace === 'App\\Controllers\\LoginController') {
            $loginView = new \App\Views\ViewReinscr\LoginView($this->headerView, $this->footerView); // Instantiate LoginView
            return new $controllerNamespace($this->utilisateurModel, $loginView);
        }elseif ($controllerNamespace === 'App\\Controllers\\AdminController') {            
            $IndexView = new \App\Views\EspaceAdmin\IndexView($this->headerAdminView, $this->footerAdminView); // Instantiate LoginView
            return new $controllerNamespace($this->adminModel, $IndexView);
        }elseif ($controllerNamespace === 'App\\Controllers\\AvisController') {            
            $IndexView = new \App\Views\EspaceAdmin\AvisView($this->headerAdminView, $this->footerAdminView); // Instantiate LoginView
            return new $controllerNamespace($this->avismodel, $IndexView);
        }elseif ($controllerNamespace === 'App\\Controllers\\EmploiController') {            
            $IndexView = new \App\Views\EspaceAdmin\EmploiView($this->headerAdminView, $this->footerAdminView); // Instantiate LoginView
            return new $controllerNamespace($this->emploiModel, $IndexView);
        }elseif ($controllerNamespace === 'App\\Controllers\\UtilisateurEnsController') {            
            $IndexView = new \App\Views\EspaceAdmin\IndexView($this->headerAdminView, $this->footerAdminView); // Instantiate LoginView
            return new $controllerNamespace($this->UtilisateurEnsModel, $IndexView);
        }elseif ($controllerNamespace === 'App\\Controllers\\EtudiantController') {            
            $IndexView = new \App\Views\EspaceEtudiant\IndexEtudiantView($this->headerEtudiantView, $this->footerEtudiantView); // Instantiate LoginView
            return new $controllerNamespace($this->avismodel, $IndexView);
        }
        elseif ($controllerNamespace === 'App\\Controllers\\ProfController') {            
            $IndexView = new \App\Views\EspaceProf\IndexProfView($this->headerProfView, $this->footerProfView); // Instantiate LoginView
            return new $controllerNamespace($this->avismodel, $IndexView);
        }
        
        elseif ($controllerNamespace === 'App\\Controllers\\AcceuilController') {
            return new $controllerNamespace();
        }


        else {
            throw new \Exception("Unsupported controller: $controllerNamespace");
        }
    }
    
    private function handleNotFound() {
        http_response_code(404);
        echo "404 Not Found";
    }

    private function handleException(\Exception $e) {
        http_response_code(500);
        echo "An error occurred: " . $e->getMessage();
    }
}
