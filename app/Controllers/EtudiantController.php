<?php

namespace App\Controllers;

use App\Models\AvisModel;
use App\Models\AnnonceModel;

use App\Views\EspaceEtudiant\IndexEtudiantView;



class EtudiantController {
    private $AvisModel;
    private $AnnonceModel;

    private $IndexView;

    public function __construct(AvisModel $AvisModel, IndexEtudiantView $IndexView) {
        $this->IndexView = $IndexView;
        $this->AvisModel = $AvisModel;
        $this->AnnonceModel =new AnnonceModel();


    }

    public function showDashboard() {

    }

    public function showAllAvis() {
        // Check if the etudiant is logged in
        if (!isset($_SESSION['matricule'])) {
            header('Location: /apogee_ens/login'); // Redirect to the login page if not logged in
            exit();
        }
    
        $etudiantgroupe = $_SESSION['groupe'];
    
        // Fetch additional etudiant details from the model if needed
        // Fetch avis for the etudiant by groupe
        $avisForEtudiant = $this->AvisModel->getAvisByGroupes($etudiantgroupe);
    
        // Check if avis were retrieved successfully
        if ($avisForEtudiant) {
            // Paginate the results
            $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $perPage = 5; // Number of items per page
            $totalAvis = count($avisForEtudiant);
            $totalPages = ceil($totalAvis / $perPage);
    
            // Limit the array to the current page
            $offset = ($currentPage - 1) * $perPage;
            $currentAvis = array_slice($avisForEtudiant, $offset, $perPage);
    
            // Pass data to the view for rendering
            $this->IndexView->showAllAvis($currentAvis, $totalPages, $currentPage, 'ENS RABAT | ECOLE NORMALE SUPERIEURE DE RABAT');
        } else {
            // Handle the case when no avis are found
            $this->IndexView->showAllAvis($currentAvis=null, $totalPages=null, $currentPage=null, 'ENS RABAT | ECOLE NORMALE SUPERIEURE DE RABAT');
        }
    }

    public function showAllAnnonce() {
        // Check if the etudiant is logged in
        if (!isset($_SESSION['matricule'])) {
            header('Location: /apogee_ens/login'); // Redirect to the login page if not logged in
            exit();
        }
    
        $etudiantfiliere = $_SESSION['filiere'];
    
        // Fetch additional etudiant details from the model if needed
        // Fetch avis for the etudiant by filiere
        $AnnoncForEtudiant = $this->AnnonceModel->getAnnonceByfilieres($etudiantfiliere);
    
        // Check if Annonc were retrieved successfully
        if ($AnnoncForEtudiant) {
            // Paginate the results
            $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $perPage = 5; // Number of items per page
            $totalAnnonc = count($AnnoncForEtudiant);
            $totalPages = ceil($totalAnnonc / $perPage);
    
            // Limit the array to the current page
            $offset = ($currentPage - 1) * $perPage;
            $currentAnnonce = array_slice($AnnoncForEtudiant, $offset, $perPage);
    
            // Pass data to the view for rendering
            $this->IndexView->showAllAnnonce($currentAnnonce, $totalPages, $currentPage, 'ENS RABAT | ECOLE NORMALE SUPERIEURE DE RABAT');
        } else {
            // Handle the case when no Annonc are found
            $this->IndexView->showAllAnnonce($currentAnnonce=null, $totalPages=null, $currentPage=null, 'ENS RABAT | ECOLE NORMALE SUPERIEURE DE RABAT');
        }
    }
    



}
