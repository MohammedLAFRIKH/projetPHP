<?php

namespace App\Controllers;

use App\Models\AvisModel;

use App\Models\AnnonceModel;
use App\Models\EmploiModel;

use App\Views\EspaceProf\IndexProfView;



class ProfController {
    private $AvisModel;
    private $IndexView;

    public function __construct(AvisModel $AvisModel, IndexProfView $IndexView) {
        $this->IndexView = $IndexView;
        $this->AvisModel = $AvisModel;

        session_start();

    }

    public function showAllAvis() {
        // Check if the Prof is logged in
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

    public function annonceChangementHoraire() {
        // Vérifiez si l'ID est présent dans la requête GET
        if (isset($_GET['id'])) {
            // Récupérez l'ID de l'emploi depuis le formulaire
            $emploiId = $_GET['id'];
    
            $emploiModel = new EmploiModel();
            $emploi = $emploiModel->getEmploiByID($emploiId);
    
            if ($emploi) {
                // Si l'emploi est trouvé, affichez-le
                $this->IndexView->Annonce($emploi, 'Annonce de changement d\'horaire');
            } else {
                // Si l'emploi n'est pas trouvé, affichez un message d'erreur
                throw new Exception("L'emploi avec l'ID $emploiId n'a pas été trouvé.");
            }
        } else {
            // Si l'ID n'est pas présent dans la requête GET, affichez un message d'erreur
            throw new Exception("ID d'emploi manquant dans la requête.");
        }
    }
    public function PublierAnnonce() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $annonceText = $_POST['annonceText'] ?? '';
            $filiere = $_POST['filiere'] ?? '';
    
            // Check if the required fields are not empty
            if (!$filiere || !$annonceText) {
                // Handle the validation error
                $this->IndexView->showError("Invalid data. Please fill in all required fields.");
                return;
            }
    
            $AnnonceModel = new AnnonceModel();
    
            // Check if a file is uploaded
            if (isset($_FILES['pieceJointe']) && $_FILES['pieceJointe']['error'] == UPLOAD_ERR_OK) {
                // Process the uploaded file
                $uploadDir = 'public/upload/avis/';
                $originalFileName = $_FILES['pieceJointe']['name'];
                $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);
    
                // Generate a unique filename to avoid overwriting existing files
                $uniqueFileName = uniqid('annonce_') . '.' . $fileExtension;
                $filePath = $uploadDir . $uniqueFileName;
    
                // Validate file type
                $allowedFileTypes = array('pdf', 'jpg', 'jpeg', 'png');
                if (!in_array(strtolower($fileExtension), $allowedFileTypes)) {
                    // Handle invalid file type error
                    $this->IndexView->showError("Invalid file type. Allowed types: pdf, jpg, jpeg, png");
                    return;
                }
    
                // Move the uploaded file to the specified directory
                if (move_uploaded_file($_FILES['pieceJointe']['tmp_name'], $filePath)) {
                    // File uploaded successfully, update the database
                    $editeur = $_SESSION['matricule'];
                    $success = $AnnonceModel->insertAnnouncement($editeur, $filiere, $annonceText, $filePath);
    
                    if ($success) {
                        // Redirect with success message
                        header('Location: /apogee_ens/espaceprof/emploi?success=annonce_published');
                        exit();
                    } else {
                        // Handle database update error
                        header('Location: /apogee_ens/espaceprof/avis?success=database_error');
                        exit();
                    }
                } else {
                    // Handle file upload error
                    $this->IndexView->showError("Failed to upload file.");
                    return;
                }
            } else {
                $AnnonceId = $_POST['id'] ?? '';

                // No file is uploaded, fetch existing file path from the database
                $existingFilePath = $AnnonceModel->getFilePathByAnnonceId($AnnonceId);
    
                // Update the database without changing the file
                $editeur = $_SESSION['matricule'];
                $success = $AnnonceModel->insertAnnouncement($editeur, $filiere, $annonceText, $existingFilePath);
    
                if ($success) {
                    // Redirect with success message
                    header('Location: /apogee_ens/espaceprof/emploi?success=annonce_published');
                    exit();
                } else {
                    // Handle database update error
                    header('Location: /apogee_ens/espaceprof/avis?error=database_error');
                    exit();
                }
            }
        } else {
            // Handle the case where the request method is not POST, perhaps redirect to an error page
            $this->IndexView->showError("Invalid request method.");
            return;
        }
    }
    
    public function MesAnnonces() {
            // Récupérez l'ID de l'emploi depuis le formulaire
            $matricule = $_SESSION['matricule'];           
            $AnnonceModel = new AnnonceModel();
            $Annonce = $AnnonceModel->getAnnonceByID($matricule);
    
            if ($Annonce) {
                // Si l'Annonce est trouvé, affichez-le
                
                $this->IndexView->MesAnnonces($Annonce, 'Liste des Annonces');
            } else {
                // Si l'Annonce n'est pas trouvé, affichez un message d'erreur
                throw new Exception("L'Annonce avec l'ID $matricule n'a pas été trouvé.");
            }
        
 
    }
    
    


}
