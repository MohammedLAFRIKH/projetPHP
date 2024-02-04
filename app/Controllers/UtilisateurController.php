<?php

// UtilisateurController.php

namespace App\Controllers;

use App\Models\UtilisateurModel;
use App\Views\ViewReinscr\UtilisateurView;
use App\Views\ViewReinscr\HeaderView; // Add the appropriate namespace for HeaderView
use App\Views\ViewReinscr\FooterView; // Add the appropriate namespace for FooterView

class UtilisateurController {
    private $utilisateurModel;
    private $utilisateurView;

    public function __construct(UtilisateurModel $utilisateurModel, UtilisateurView $utilisateurView) {
        $this->utilisateurModel = $utilisateurModel;
        $this->utilisateurView = $utilisateurView;
    }


    public function logout() {
        // Start the session
    
        // Unset or destroy the session variables
        session_unset();
        session_destroy();
    
        // Redirect to the login or home page
        header('Location: /apogee_ens/user/login'); // Change the URL to your login page
        exit;
    }
    
    
    
    // In UtilisateurController.php
    public function checkEmailExists()
    {
        try {
            // Récupération des données JSON du corps de la requête
            $requestData = json_decode(file_get_contents('php://input'), true);
    
            // Vérifiez si la clé 'email' est présente dans les données
            if (!isset($requestData['email'])) {
                throw new \Exception('Email parameter is missing.');
            }
    
            $email = $requestData['email'];
    
            // Perform the logic to check if the email exists in the database
            $model = new UtilisateurModel(); // Ajustez cette instanciation en fonction de votre code
            $exists = $model->checkEmailExists($email);
    
            // Retourne la réponse en JSON
            header('Content-Type: application/json');
            echo json_encode(['exists' => $exists]);
        } catch (\Exception $e) {
            // Handle exceptions, log the error, or return an error response
            header('Content-Type: application/json');
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
    

  
}
