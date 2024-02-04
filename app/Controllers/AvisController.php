<?php

namespace App\Controllers;

use App\Models\AvisModel;


use App\Views\EspaceAdmin\IndexView;
use App\Views\EspaceAdmin\AvisView;


class AvisController {
    private $AdminModel;
    private $avisView;

    public function __construct(AvisModel $AvisModel, AvisView $AvisView) {
        $this->AvisModel = $AvisModel;
        $this->avisView = $AvisView;
    }

    public function showAllAvis() {
        // Fetch all avis from the model
        $allAvis = $this->AvisModel->getAllAvis();
        $error = '';
        $success = '';
    
        // Check if there is an error message in the URL
        if (isset($_GET['error'])) {
            $errorCode = $_GET['error'];
    
            // Define error messages
            $errorMessages = [
                'file' => 'Erreur lors du téléchargement du fichier. Veuillez réessayer.',
                'deletion_error' => 'Erreur lors de la suppression des avis.',
                'no_reviews_selected' => 'Aucun avis sélectionné.',
            ];
    
            // Set the appropriate error message based on the error code
            $error = isset($errorMessages[$errorCode]) ? $errorMessages[$errorCode] : '';
        }
    
        // Check if there is a success message in the URL
        if (isset($_GET['success'])) {
            $successCode = $_GET['success'];
    
            // Define success messages
            $successMessages = [
                'success' => 'Avis ajouté avec succès !',
                'reviews_deleted' => 'Avis supprimés avec succès !',
                'avis_modified' => 'Avis modifié avec succès !', // Add this line for avis modification success

            ];
    
            // Set the appropriate success message based on the success code
            $success = isset($successMessages[$successCode]) ? $successMessages[$successCode] : '';
        }
    
        // Pass the data to a view
        $this->avisView->showAllAvis($allAvis, $error, $success, "Les avis");
    }
    


    public function showAvisForm() {
        $groupeData = $this->AvisModel->getAllgroupe();
        $error = '';
        $success = '';  
        $this->avisView->showAddAvisForm($groupeData,$error  ,$success ,"Ajouter un avis");
    }
    
    public function submitAvis() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Handle form submission
            session_start();
    
            $utilisateurEns = $_SESSION['matricule'];
            $selectedGroups = isset($_POST['groupe']) ? $_POST['groupe'] : array();
            $objet = $_POST['objet'] ?? '';
            $contenu = $_POST['contenu'] ?? '';
    
            // Convert spaces in $objet to underscores
            $objetWithoutSpaces = str_replace(' ', '_', $objet);
    
            // Check if file is uploaded
            if (isset($_FILES['pieceJointe'])) {
                // File handling
                $uploadDir = 'public/upload/avis/';
                $originalFileName = $_FILES['pieceJointe']['name'];
                
                $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);

                // Remove spaces from the original filename
                $fileNameWithoutSpaces = $objetWithoutSpaces . '.' . str_replace(' ', '', $fileExtension);
    
                $filePath = $uploadDir . $fileNameWithoutSpaces;
    
                // Validate file type
                $allowedFileTypes = array('pdf', 'jpg', 'jpeg', 'png');
                $fileType = pathinfo($originalFileName, PATHINFO_EXTENSION);
    
                if (!in_array(strtolower($fileType), $allowedFileTypes)) {
                    // Handle invalid file type error
                    header('Location: /apogee_ens/espaceadmin/ajouteravis?error=InvalidFileType');
                    return;
                }
    
                // Move the uploaded file to the specified directory
                if (move_uploaded_file($_FILES['pieceJointe']['tmp_name'], $filePath)) {
                    // File uploaded successfully
                    // Now you can proceed to insert data into the database or perform other actions
                    $avisModel = new AvisModel();
                    $success = $avisModel->insertAvis($utilisateurEns, $selectedGroups, $objet, $contenu, $filePath);
    
                    if ($success) {
                        header('Location: /apogee_ens/espaceadmin/avis?success=success');
    
                    } else {
                        header('Location: /apogee_ens/espaceadmin/avis?error=error');
                    }
                } else {
                    // Handle file upload error
                    header('Location: /apogee_ens/espaceadmin/avis?error=file');
                }
            } else {
                // Handle case when file is not uploaded
                header('Location: /apogee_ens/espaceadmin/avis?error=NoFileUploaded');
            }
        } else {
            // Handle non-POST requests
            header('Location: /apogee_ens/espaceadmin/avis?error=InvalidRequestMethod');
        }
    }
    public function deleteSelectedAvis() {
        // Check if the form was submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Check if the deleteSelected button was clicked
            if (isset($_POST['deleteSelected'])) {
                // Check if selectedAvis is set and not empty
                if (isset($_POST['selectedAvis']) && !empty($_POST['selectedAvis'])) {
                    // Get the array of selected review IDs
                    $selectedAvis = $_POST['selectedAvis'];
    
                    foreach ($selectedAvis as $reviewId) {

                        // Fetch the file path associated with the review ID
                        $fileToDelete = $this->AvisModel->getFilePathByReviewId($reviewId);
    
                        // Delete the file if it exists
                        if ($fileToDelete && file_exists($fileToDelete)) {
                            unlink($fileToDelete);
                        }
                    }
    
                    // Perform the deletion of reviews
                    $result = $this->AvisModel->deleteSelectedAvis($selectedAvis);
    
                    // Check the result of the deletion
                    if ($result) {
                        // Success: Redirect with success message
                        header('Location: /apogee_ens/espaceadmin/avis?success=reviews_deleted');
                        exit();
                    } else {
                        // Error: Redirect with error message
                        header('Location: /apogee_ens/espaceadmin/avis?error=deletion_error');
                        exit();
                    }
                } else {
                    // No reviews selected for deletion
                    header('Location: /apogee_ens/espaceadmin/avis?error=no_reviews_selected');
                    exit();
                }
            }
        }
    }

    // In AvisController.php

    public function showModifyAvisForm() {
        // Retrieve the avis details based on the avisId
        $avisId = isset($_GET['id']) ? $_GET['id'] : null;
    
        // Check if avisId is provided
        if ($avisId) {
            $avisDetails = $this->AvisModel->getAvisDetails($avisId);
    
            // Check if the avis details are retrieved successfully
            if ($avisDetails) {
                $error = '';
                $success = '';
    
                // Check if there is an error message in the URL
                if (isset($_GET['error'])) {
                    $errorCode = $_GET['error'];
    
                    // Define error messages
                    $errorMessages = [
                        'InvalidFileType' => 'Type de fichier non valide. Veuillez télécharger un fichier PDF, JPG, JPEG ou PNG.',
                        'database_error' => 'Erreur lors de la mise à jour de la base de données.',
                        'file_upload_error' => 'Erreur lors du téléchargement du fichier. Veuillez réessayer.',
                        'no_file_uploaded' => 'Aucun fichier téléchargé. Veuillez sélectionner un fichier.',
                        'invalid_request_method' => 'Méthode de requête invalide. Veuillez soumettre le formulaire correctement.',
                        // Add more error messages as needed
                    ];
    
                    // Set the appropriate error message based on the error code
                    $error = isset($errorMessages[$errorCode]) ? $errorMessages[$errorCode] : '';
                }
    
                $groupeData = $this->AvisModel->getAllgroupe();
                $this->avisView->showModifyAvisForm($groupeData,$avisDetails, $error, $success);
    
            } else {
                // Handle the case when avis details are not available
                // Redirect or display an error message
                header('Location: /apogee_ens/espaceadmin/avis?error=avis_not_found');
                exit();
            }
        } else {
            // Handle the case when avisId is not provided
            // Redirect or display an error message
            header('Location: /apogee_ens/espaceadmin/avis?error=missing_avis_id');
            exit();
        }
    }
    




    public function modifyAvis() {
        // Check if the form was submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Retrieve form data
            $avisId = $_POST['avis_id'] ?? '';
            $modifiedObjet = $_POST['modified_objet'] ?? '';
            $modifiedContenu = $_POST['modified_contenu'] ?? '';
            $selectedGroups = isset($_POST['groupe']) ? $_POST['groupe'] : array();

            // Fetch the current file path from the database
            $currentFilePath = $this->AvisModel->getFilePathByReviewId($avisId);
    
            // Validate file upload
            if (isset($_FILES['modified_pieceJointe']) && $_FILES['modified_pieceJointe']['error'] == UPLOAD_ERR_OK) {
                // Process the uploaded file
                $uploadDir = 'public/upload/avis/';
                $originalFileName = $_FILES['modified_pieceJointe']['name'];
                $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);
                
                // Generate a unique filename to avoid overwriting existing files
                $uniqueFileName = uniqid('modified_') . '.' . $fileExtension;
                $filePath = $uploadDir . $uniqueFileName;
    
                // Validate file type
                $allowedFileTypes = array('pdf', 'jpg', 'jpeg', 'png');
                if (!in_array(strtolower($fileExtension), $allowedFileTypes)) {
                    // Handle invalid file type error
                    header('Location: /apogee_ens/espaceadmin/modifyavis?id=' . $avisId . '&error=InvalidFileType');
                    exit();
                }
    
                // Move the uploaded file to the specified directory
                if (move_uploaded_file($_FILES['modified_pieceJointe']['tmp_name'], $filePath)) {
                    // File uploaded successfully, update the database
                    $success = $this->AvisModel->updateAvis($avisId, $modifiedObjet,$selectedGroups , $modifiedContenu, $filePath);
    
                    if ($success) {
                        // Delete the old file
                        unlink($currentFilePath);
    
                        // Redirect with success message
                        header('Location: /apogee_ens/espaceadmin/avis?success=avis_modified');
                        exit();
                    } else {
                        // Handle database update error
                        header('Location: /apogee_ens/espaceadmin/modifyavis?id=' . $avisId . '&error=database_error');
                        exit();
                    }
                } else {
                    // Handle file upload error
                    header('Location: /apogee_ens/espaceadmin/modifyavis?id=' . $avisId . '&error=file_upload_error');
                    exit();
                }
            } else {
                // Handle case when no file is uploaded
                header('Location: /apogee_ens/espaceadmin/modifyavis?id=' . $avisId . '&error=no_file_uploaded');
                exit();
            }
        } else {
            // Handle non-POST requests
            header('Location: /apogee_ens/espaceadmin/modifyavis?id=' . $avisId . '&error=invalid_request_method');
            exit();
        }
    }
    



}