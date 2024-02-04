<?php
// ConfirmController.php

namespace App\Controllers;

use App\Models\UtilisateurModel; // Import the correct namespace for the model

class ConfirmController {
    private $utilisateurModel;

    public function __construct(UtilisateurModel $utilisateurModel) {
        $this->utilisateurModel = $utilisateurModel;
    }

    public function confirmUser() {
        // Récupérer le CNE et le jeton de la chaîne de requête
        $CNE = isset($_GET['user']) ? $_GET['user'] : null;
        $token = isset($_GET['token']) ? $_GET['token'] : null;
 
        if ($CNE && $token) {
            // Vérifier si l'utilisateur est déjà confirmé
            $isUserConfirmed = $this->utilisateurModel->isUserConfirmed($CNE);
    
            if ($isUserConfirmed) {
                // L'utilisateur est déjà confirmé, afficher un message ou rediriger
                echo "<p style='color: green;'>L'utilisateur avec le CNE <strong>$CNE</strong> est déjà confirmé.</p>";
            } else {
                // Vérifier le jeton et mettre à jour l'enregistrement de l'utilisateur dans la base de données
                try {
                    $isValidToken = $this->utilisateurModel->verifyToken($CNE, $token);
    

    
                    if ($isValidToken) {
                        $this->utilisateurModel->confirmUser($CNE);
                        include 'app/views/ViewReinscr/ConfirmView.php';
                    } else {
                        // Afficher un message d'erreur ou rediriger vers une page d'erreur
                        echo "Erreur : jeton ou CNE invalide.";
                    }
                } catch (\Exception $e) {
                    // Gérer les exceptions, enregistrer l'erreur ou rediriger vers une page d'erreur
                    echo "Erreur : une exception s'est produite - " . $e->getMessage();
                }
            }
        } else {
            // Gérer les paramètres manquants, par exemple, rediriger vers une page d'erreur
            echo "Erreur : CNE ou jeton manquant.";
        }
    }
    
    


}
