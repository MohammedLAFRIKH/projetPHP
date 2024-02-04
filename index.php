<?php

// Define HTTP method constants
const GET = 'GET';
const POST = 'POST';

// Autoload composer dependencies
require_once 'vendor/autoload.php';
require_once 'config/config.php';
session_start();

use App\Router;

// Define authMiddleware function
function authMiddleware($url, $requestMethod, $allowedRoles) {
    

    // Check if the user's role is allowed for the current route
    $userRole = $_SESSION['role'];
    
    if (!in_array($userRole, $allowedRoles)) {
        // Redirect to an error page or access denied page
        echo 'Accès refusé';
        exit;
    }
}

// Create a router instance
$router = new App\Router();

$router->addRoute('/apogee_ens/user/login', 'LoginController', 'showLoginForm', GET, ['']);
$router->addRoute('/apogee_ens/user/login/process', 'LoginController', 'loginUser', POST, ['']);
$router->addRoute('/apogee_ens/user/passwords/forgotten', 'LoginController', 'showforgottenForm', GET ,['']);
$router->addRoute('/apogee_ens/user/passwords/forgotten/process', 'LoginController', 'forgotten', POST ,['']);
$router->addRoute('/apogee_ens/user/passwords/resetPassword', 'LoginController', 'resetPassword', GET ,['']);
$router->addRoute('/apogee_ens/user/passwords/reset', 'LoginController', 'updatePassword', POST ,['']);


$router->addRoute('/apogee_ens/register/details', 'RegisterController', 'showRegisterForm', GET ,['']);
$router->addRoute('/apogee_ens/register/processForm', 'RegisterController', 'processForm', POST ,['']);
$router->addRoute('/apogee_ens/register/dashboard', 'RegisterController', 'showDashboard', POST ,['authMiddleware']);

$router->addRoute('/apogee_ens/register/formdownloed', 'RegisterController', 'showFormdownload', POST ,['authMiddleware']);
$router->addRoute('/apogee_ens/register/process', 'RegisterController', 'registerUser', POST ,['authMiddleware']);
$router->addRoute('/apogee_ens/confirm', 'ConfirmController', 'confirmUser', GET ,['authMiddleware']);
$router->addRoute('/apogee_ens/check-email', 'UtilisateurController', 'checkEmailExists', POST ,['authMiddleware']);


$router->addRoute('/apogee_ens/sessions/remove', 'UtilisateurController', 'logout', GET ,['']);
$router->addRoute('/apogee_ens/dashboard', 'UtilisateurController', 'dashboard', POST ,['authMiddleware']);

// Espace Etudiant ________ route ______

$router->addRoute('/apogee_ens/espaceetudiant/details', 'EtudiantController', 'showAllAvis', POST ,[
    'authMiddleware' => [
        'function' => 'authMiddleware',
        'roles' => ['etudiant']
    ]
]);
$router->addRoute('/apogee_ens/espaceetudiant/avis', 'EtudiantController', 'showAllAvis', POST ,[
    'authMiddleware' => [
        'function' => 'authMiddleware',
        'roles' => ['etudiant']
    ]
]);
$router->addRoute('/apogee_ens/espaceetudiant/annonce', 'EtudiantController', 'showAllAnnonce', POST ,[
    'authMiddleware' => [
        'function' => 'authMiddleware',
        'roles' => ['etudiant']
    ]
]);
$router->addRoute('/apogee_ens/espaceetudiant/showEmploichangment', 'EmploiController', 'showAllemploiHtml', GET ,[
    'authMiddleware' => [
        'function' => 'authMiddleware',
        'roles' => ['etudiant']
    ]
]);
$router->addRoute('/apogee_ens/infoetudiant', 'UtilisateurEnsController', 'modifyInfoform', GET ,[
    'authMiddleware' => [
        'function' => 'authMiddleware',
        'roles' => ['etudiant']
    ]
]);


// Espace Prof ________ route ______

$router->addRoute('/apogee_ens/espaceprof/details', 'ProfController', 'showAllAvis', POST ,[
    'authMiddleware' => [
        'function' => 'authMiddleware',
        'roles' => ['prof']
    ]
]);
$router->addRoute('/apogee_ens/espaceprof/avis', 'ProfController', 'showAllAvis', POST ,[
    'authMiddleware' => [
        'function' => 'authMiddleware',
        'roles' => ['prof']
    ]
]);
$router->addRoute('/apogee_ens/espaceprof/annonce', 'ProfController', 'MesAnnonces', POST ,[
    'authMiddleware' => [
        'function' => 'authMiddleware',
        'roles' => ['prof']
    ]
]);
$router->addRoute('/apogee_ens/espaceprof/formannonce', 'ProfController', 'annonceChangementHoraire', POST ,[
    'authMiddleware' => [
        'function' => 'authMiddleware',
        'roles' => ['prof']
    ]
]);
$router->addRoute('/apogee_ens/espaceprof/publierannonce', 'ProfController', 'PublierAnnonce', POST ,[
    'authMiddleware' => [
        'function' => 'authMiddleware',
        'roles' => ['prof']
    ]
]);

$router->addRoute('/apogee_ens/espaceprof/emploi', 'EmploiController', 'showAllemploi', GET ,[
    'authMiddleware' => [
        'function' => 'authMiddleware',
        'roles' => ['prof']
    ]
]);
$router->addRoute('/apogee_ens/espaceprof/searchemploi', 'EmploiController', 'searchemploi', POST ,[
    'authMiddleware' => [
        'function' => 'authMiddleware',
        'roles' => ['prof']
    ]
]);
$router->addRoute('/apogee_ens/espaceprof/showAllemploiHtml', 'EmploiController', 'showAllemploiHtml', GET ,[
    'authMiddleware' => [
        'function' => 'authMiddleware',
        'roles' => ['prof']
    ]
]);
$router->addRoute('/apogee_ens/espaceprof/changementenemploiFoem', 'EmploiController', 'changementEploiForm', GET ,[
    'authMiddleware' => [
        'function' => 'authMiddleware',
        'roles' => ['prof']
    ]
]);
$router->addRoute('/apogee_ens/espaceprof/showAllemploiHtml', 'EmploiController', 'showAllemploiHtml', GET ,[
    'authMiddleware' => [
        'function' => 'authMiddleware',
        'roles' => ['prof']
    ]
]);
$router->addRoute('/apogee_ens/infoprof', 'UtilisateurEnsController', 'modifyInfoform', GET ,[
    'authMiddleware' => [
        'function' => 'authMiddleware',
        'roles' => ['prof']
    ]
]);

$router->addRoute('/apogee_ens/espaceprof/processAddSession', 'EmploiController', 'processAddSession', POST ,[
    'authMiddleware' => [
        'function' => 'authMiddleware',
        'roles' => ['prof']
    ]
]);

// Espace UtilisateurEnsController  ________ route ______

$router->addRoute('/apogee_ens/removesessions', 'UtilisateurEnsController', 'logout', GET ,['']);
$router->addRoute('/apogee_ens/login', 'UtilisateurEnsController', 'showLoginForm', GET ,['']);
$router->addRoute('/apogee_ens/login/process', 'UtilisateurEnsController', 'loginUser', POST ,['']);
$router->addRoute('/apogee_ens/passwords/forgotten', 'UtilisateurEnsController', 'showforgottenForm', GET ,['']);
$router->addRoute('/apogee_ens/passwords/forgotten/process', 'UtilisateurEnsController', 'forgotten', POST ,['']);
$router->addRoute('/apogee_ens/passwords/resetPassword', 'UtilisateurEnsController', 'resetPassword', GET ,['']);
$router->addRoute('/apogee_ens/passwords/reset', 'UtilisateurEnsController', 'updatePassword', POST ,['']);


$router->addRoute('/apogee_ens/espaceadmin/details', 'AdminController', 'dashboard', GET ,[
    'authMiddleware' => [
        'function' => 'authMiddleware',
        'roles' => ['admin']
    ]
]);
$router->addRoute('/apogee_ens/espaceadmin/ajouteravis', 'AvisController', 'showAvisForm', GET ,[
    'authMiddleware' => [
        'function' => 'authMiddleware',
        'roles' => ['admin']
    ]
]);
$router->addRoute('/apogee_ens/espaceadmin/avis', 'AvisController', 'showAllAvis', GET ,[
    'authMiddleware' => [
        'function' => 'authMiddleware',
        'roles' => ['admin']
    ]
]);
$router->addRoute('/apogee_ens/espaceadmin/submit_avis', 'AvisController', 'submitAvis',POST ,[
    'authMiddleware' => [
        'function' => 'authMiddleware',
        'roles' => ['admin']
    ]
]);
$router->addRoute('/apogee_ens/espaceadmin/deleteavis', 'AvisController', 'deleteSelectedAvis',POST ,[
    'authMiddleware' => [
        'function' => 'authMiddleware',
        'roles' => ['admin']
    ]
]);
$router->addRoute('/apogee_ens/espaceadmin/showModifyAvisForm', 'AvisController', 'showModifyAvisForm',GET ,[
    'authMiddleware' => [
        'function' => 'authMiddleware',
        'roles' => ['admin']
    ]
]);
$router->addRoute('/apogee_ens/espaceadmin/modifyavis', 'AvisController', 'modifyAvis',POST ,[
    'authMiddleware' => [
        'function' => 'authMiddleware',
        'roles' => ['admin']
    ]
]);
$router->addRoute('/apogee_ens/espaceadmin/emploi', 'EmploiController', 'showAllemploi', GET ,[
    'authMiddleware' => [
        'function' => 'authMiddleware',
        'roles' => ['admin']
    ]
]);
$router->addRoute('/apogee_ens/espaceadmin/ajouteremploi', 'EmploiController', 'showemploiForm', GET ,[
    'authMiddleware' => [
        'function' => 'authMiddleware',
        'roles' => ['admin']
    ]
]);
$router->addRoute('/apogee_ens/espaceadmin/submit_emploi', 'EmploiController', 'submitEmploi', POST ,[
    'authMiddleware' => [
        'function' => 'authMiddleware',
        'roles' => ['admin']
    ]
]);
$router->addRoute('/apogee_ens/espaceadmin/showModifyemploiForm', 'EmploiController', 'showModifyemploiForm', GET ,[
    'authMiddleware' => [
        'function' => 'authMiddleware',
        'roles' => ['admin']
    ]
]);
$router->addRoute('/apogee_ens/espaceadmin/modifyemploi', 'EmploiController', 'modifyEmploi', POST ,[
    'authMiddleware' => [
        'function' => 'authMiddleware',
        'roles' => ['admin']
    ]
]);
$router->addRoute('/apogee_ens/espaceadmin/deleteemploi', 'EmploiController', 'deleteSelectedemploi', POST ,[
    'authMiddleware' => [
        'function' => 'authMiddleware',
        'roles' => ['admin']
    ]
]);
$router->addRoute('/apogee_ens/infoadmin', 'UtilisateurEnsController', 'modifyInfoform', GET ,[
    'authMiddleware' => [
        'function' => 'authMiddleware',
        'roles' => ['admin']
    ]
]);

$router->addRoute('/apogee_ens/', 'AcceuilController', 'showHomePage', GET ,['']);

// Rest of your routes...

try {
    // Get the requested URL and request method
    $url = $_SERVER['REQUEST_URI'];
    $requestMethod = $_SERVER['REQUEST_METHOD'];


    // Use the router for authenticated pages
    $router->dispatch($url, $requestMethod);



} catch (\Exception $e) {
    // Handle exceptions, e.g., log the error or display a user-friendly error page
    echo "An error occurred: " . $e->getMessage();
}
?>
