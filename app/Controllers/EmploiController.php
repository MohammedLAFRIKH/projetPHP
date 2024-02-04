<?php

namespace App\Controllers;

use App\Models\EmploiModel;


use App\Views\EspaceAdmin\IndexView;
use App\Views\EspaceAdmin\EmploiView;
use App\Views\EspaceProf\EmploiProfView;

use App\Views\EspaceProf\IndexProfView;
use App\Views\EspaceProf\HeaderProfView;
use App\Views\EspaceProf\FooterProfView;

use App\Views\EspaceEtudiant\IndexEtudiantView;
use App\Views\EspaceEtudiant\HeaderEtudiantView;
use App\Views\EspaceEtudiant\FooterEtudiantView;

class EmploiController {
    private $EmploiModel;
    private $EmploiView;
    private $headerProfView;
    private $footerProfView;
    private $headerEtudiantView;
    private $footerEtudiantView;
    public function __construct(EmploiModel $EmploiModel, EmploiView $EmploiView) {
        $this->EmploiModel = $EmploiModel;
        $this->EmploiView = $EmploiView;
        $this->headerEtudiantView=new HeaderEtudiantView();
        $this->footerEtudiantView=new FooterEtudiantView();
        $this->headerProfView=new HeaderProfView();
        $this->footerProfView=new FooterProfView();


    }

  
    public function showAllemploiHtml() {
        $error = '';
        $success = '';
    
        // Check if there is an error message in the URL
        if (isset($_GET['error'])) {
            $errorCode = $_GET['error'];
    
            // Define error messages
            $errorMessages = [
                'file' => 'Erreur lors du téléchargement du fichier. Veuillez réessayer.',
                'deletion_error' => 'Erreur lors de la suppression des Emploi.',
                'no_emploi_selected' => 'Aucun Emploi sélectionné.',
            ];
    
            // Set the appropriate error message based on the error code
            $error = isset($errorMessages[$errorCode]) ? $errorMessages[$errorCode] : '';
        }
    
        // Check if there is a success message in the URL
        if (isset($_GET['success'])) {
            $successCode = $_GET['success'];
    
            // Define success messages
            $successMessages = [
                'success' => 'Emploi ajouté avec succès !',
                'emploi_deleted' => 'Emploi supprimés avec succès !',
                'emploi_modified' => 'Emploi modifié avec succès !',
                'annonce_published' => 'Emploi annonce avec succès !',
            ];
    
            // Set the appropriate success message based on the success code
            $success = isset($successMessages[$successCode]) ? $successMessages[$successCode] : '';
        }
    
        if ($_SESSION['role'] == 'prof') {
            // Additional logic or view for professor role
    
            // Fetch filiere names from the database (replace with your actual database query)
            $filiereNames = $this->EmploiModel->getRefFilfiliere(); // Implement this method in your model

            // Start output buffering
            ob_start();
            
            // Loop through each filiere
      // Loop through each filiere
            foreach ($filiereNames as $filiere) {
                // Ensure $filiere['ref_fil'] is a string
                $filiereName = isset($filiere['ref_fil']) ? (string) $filiere['ref_fil'] : '';

                // Check if $filiereName is not empty before processing
                if (!empty($filiereName)) {
                    $directoryPath = 'public/upload/emploi/';
                    $filePath = $directoryPath . strtolower($filiereName) . '.csv';

                    // Check if the file exists
                    if (file_exists($filePath)) {
                        // Read CSV data from the file
                        $csvData = file_get_contents($filePath);

                        // Parse CSV data into an associative array
                        $rows = array_map('str_getcsv', explode("\n", $csvData));
                        $header = array_shift($rows);
                        $schedule = [];

                        foreach ($rows as $row) {
                            $row = array_pad($row, count($header), ''); // Ensure each row has the same number of columns
                            $day = $row[0];
                            $time = $row[1];
                            $subject = $row[2];
                            $professor = $row[3];
                            $location = $row[4];

                            $schedule[$day][$time] = "{$subject}<br>{$professor}<br>{$location}";
                        }

                        // Display the schedule in HTML table
                        echo '<h2>' . $filiereName . '</h2>';
                        echo '<table id="timetable">';
                        echo '<tr><th>Jour/Horaire</th><th data-time="1">8h30-11h30</th><th>Pause</th><th data-time="2">15h-18h</th></tr>';

                        foreach ($schedule as $day => $daySchedule) {
                            echo "<tr><td>{$day}</td>";

                            $timeSlots = [
                                '8h30-11h30' => '8h30-11h30',
                                'Pause' => 'Pause',
                                '15h-18h' => '15h-18h',
                            ];

                            foreach ($timeSlots as $timeSlot) {
                                $cellContent = isset($daySchedule[$timeSlot]) ? $daySchedule[$timeSlot] : '';
                                echo "<td id='{$day}_{$timeSlot}'>{$cellContent}</td>";
                            }

                            echo '</tr>';
                        }

                        echo '</table>';
                    } 
                } else {
                    echo 'Invalid filiere name.';
                }
            }

    
            // Get the buffered content and clean the buffer
            $tableContent = ob_get_clean();
    
            // Now you can use $tableContent as needed
            // For example, you can pass it to a view or echo it where needed
            $EmploiProfView = new \App\Views\EspaceProf\EmploiProfView($this->headerProfView, $this->footerProfView); 
            $EmploiProfView->showAllemploiHtml($tableContent, $error, $success, "Les Emploi");
        } else {


// Ensure $_SESSION['filiere'] is set and not empty
if (isset($_SESSION['filiere']) && !empty($_SESSION['filiere'])) {
    // Get the filiere name from the session
    $filiereName = $_SESSION['filiere'];

    // Ensure $filiereName is a string
    if (is_string($filiereName)) {
        // Specify the directory for schedule files
        $directoryPath = 'public/upload/emploi/';
        $filePath = $directoryPath . strtolower($filiereName) . '.csv';

        // Check if the file exists
        if (file_exists($filePath)) {
            // Read CSV data from the file
            $csvData = file_get_contents($filePath);

            // Parse CSV data into an associative array
            $rows = array_map('str_getcsv', explode("\n", $csvData));
            $header = array_shift($rows);
            $schedule = [];

            foreach ($rows as $row) {
                $row = array_pad($row, count($header), ''); // Ensure each row has the same number of columns
                $day = $row[0];
                $time = $row[1];
                $subject = $row[2];
                $professor = $row[3];
                $location = $row[4];

                $schedule[$day][$time] = "{$subject}<br>{$professor}<br>{$location}";
            }

            // Display the schedule in HTML table
            echo '<h2>' . htmlspecialchars($filiereName) . '</h2>';
            echo '<table id="timetable">';
            echo '<tr><th>Jour/Horaire</th><th data-time="1">8h30-11h30</th><th>Pause</th><th data-time="2">15h-18h</th></tr>';

            foreach ($schedule as $day => $daySchedule) {
                echo "<tr><td>{$day}</td>";

                $timeSlots = [
                    '8h30-11h30' => '8h30-11h30',
                    'Pause' => 'Pause',
                    '15h-18h' => '15h-18h',
                ];

                foreach ($timeSlots as $timeSlot) {
                    $cellContent = isset($daySchedule[$timeSlot]) ? $daySchedule[$timeSlot] : '';
                    echo "<td id='{$day}_{$timeSlot}'>{$cellContent}</td>";
                }

                echo '</tr>';
            }

            echo '</table>';
        } else {
            echo 'File does not exist: ' . htmlspecialchars($filePath);
        }
    } else {
        echo 'Invalid filiere name.';
    }
} else {
    echo 'Filiere not set in session or empty.';
}

// Get the buffered content and clean the buffer
$tableContent = ob_get_clean();

// Now you can use $tableContent as needed
$emploi = $this->EmploiModel->getPieceJointeByFiliere($filiereName); // Implement this method in your model
$IndexView = new \App\Views\EspaceEtudiant\IndexEtudiantView($this->headerEtudiantView, $this->footerEtudiantView); // Instantiate LoginView
$IndexView->showAllemploiHtml($emploi,$tableContent, $error, $success, "Les Emploi");

        }
    }
    


    public function changementEploiForm(){
            $error = '';
            $success = '';
            $filiereNames = $this->EmploiModel->getAllfiliere(); // Implement this method in your model

            $EmploiProfView = new \App\Views\EspaceProf\EmploiProfView($this->headerProfView, $this->footerProfView); 
            $EmploiProfView->changementEploiForm($filiereNames, $error, $success, "Les Emploi");
    }
    
      
    

public function processAddSession()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Handle form submission
        $day = $_POST['day'];
        $time = $_POST['time'];
        $subject = $_POST['subject'];
        $selectedFiliere = $_POST['selectedFiliere'];
        $Professor = $_POST['Professor'];
        $Location = $_POST['Location'];
        // Assuming your CSV files are stored in a specific directory
        $directoryPath = 'public/upload/emploi/';
        $filePath = $directoryPath . strtolower($selectedFiliere) . '.csv';

        // Load existing schedule from CSV
        $schedule = $this->loadScheduleFromCSV($filePath);

        // Update the schedule
         $schedule[$day][$time] = [
        'subject' => $subject,
        'Professor' => $Professor,
        'Location' => $Location,
    ];


        // Save the updated schedule to CSV
        $this->saveScheduleToCSV($filePath, $schedule);

        // Prepare a response
        $response = [
            'status' => 'success',
            'message' => 'Session added successfully.',
            'data' => [
                'day' => $day,
                'time' => $time,
                'subject' => $subject,
            ],
        ];

        // Send the JSON response back to the client
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}

// Function to load the schedule from CSV
function loadScheduleFromCSV($filePath)
{
    $schedule = [];

    if (($handle = fopen($filePath, 'r')) !== false) {
        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            $day = $data[0];
            $time = $data[1];
            $subject = $data[2];
            $Professor = $data[3];
            $Location = $data[4];

            $schedule[$day][$time] = [
                'subject' => $subject,
                'Professor' => $Professor,
                'Location' => $Location,
            ];
        }
        fclose($handle);
    }

    return $schedule;
}

// Function to save the schedule to CSV
function saveScheduleToCSV($filePath, $schedule)
{
    $file = fopen($filePath, 'w');

    if ($file) {
        foreach ($schedule as $day => $timeSlots) {
            foreach ($timeSlots as $time => $data) {
                fputcsv($file, [$day, $time, $data['subject'], $data['Professor'], $data['Location']]);
            }
        }

        fclose($file);
    }
}
    





    public function showAllemploi() {
        // Fetch all Emploi from the model

        $allEmploi = $this->EmploiModel->getAllEmploi();

        $error = '';
        $success = '';
    
        // Check if there is an error message in the URL
        if (isset($_GET['error'])) {
            $errorCode = $_GET['error'];
    
            // Define error messages
            $errorMessages = [
                'file' => 'Erreur lors du téléchargement du fichier. Veuillez réessayer.',
                'deletion_error' => 'Erreur lors de la suppression des Emploi.',
                'no_emploi_selected' => 'Aucun Emploi sélectionné.',
            ];
    
            // Set the appropriate error message based on the error code
            $error = isset($errorMessages[$errorCode]) ? $errorMessages[$errorCode] : '';
        }
    
        // Check if there is a success message in the URL
        if (isset($_GET['success'])) {
            $successCode = $_GET['success'];
    
            // Define success messages
            $successMessages = [
                'success' => 'Emploi ajouté avec succès !',
                'emploi_deleted' => 'Emploi supprimés avec succès !',
                'emploi_modified' => 'Emploi modifié avec succès !', // Add this line for Emploi modification success
                'annonce_published' => 'Emploi annonce avec succès !', // Add this line for Emploi a\publiier success

            ];
    
            // Set the appropriate success message based on the success code
            $success = isset($successMessages[$successCode]) ? $successMessages[$successCode] : '';
        }

        if ($_SESSION['role'] == 'prof') {
            // Additional logic or view for professor role
            $groupeData = $this->EmploiModel->getAllfiliere();

            $EmploiProfView = new \App\Views\EspaceProf\EmploiProfView($this->headerProfView, $this->footerProfView); 
            $EmploiProfView->showAllEmploi($groupeData,$allEmploi, $error, $success, "Les Emploi");

        } else {
            $this->EmploiView->showAllEmploi($allEmploi, $error, $success, "Les Emploi");
        }
        
    }
    
    public function searchemploi()
    {
        // Check if the form is submitted using POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get the selected filieres from the form
            $selectedFilieres = isset($_POST['selectedFiliere']) ? $_POST['selectedFiliere'] : [];
    
            // Validate and sanitize the selected filieres if needed
    
            // Perform the emploi search based on selected filieres
            $emploiModel = new EmploiModel();
            $searchResults = $emploiModel->searchEmploiByFiliere($selectedFilieres);
    
            // Check if search results are found
            if ($searchResults !== null) {
                // Handle the case when emploi records are found
                $error = null;
                $success = 'Search results found!';
            } else {
                // Handle the case when no emploi records are found
                $error = 'No emploi records found for the selected filieres.';
                $success = null;
            }
    
            $EmploiProfView = new \App\Views\EspaceProf\EmploiProfView($this->headerProfView, $this->footerProfView); 
            $EmploiProfView->showSearchResults($searchResults, $error, $success, "Search Results");
        } else {
            // Handle the case when the form is not submitted via POST
            // Redirect or display an error message as needed
            header('Location: /apogee_ens/espaceprof/emploi?error=InvalidRequestMethod');
        }
    }
    
    

    public function showemploiForm() {
        $groupeData = $this->EmploiModel->getAllfiliere();
        $error = '';
        $success = '';  
        $this->EmploiView->showemploiForm($groupeData,$error  ,$success ,"Ajouter un emplio");
    }
    
    public function submitEmploi() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    
            $selectedGroups = isset($_POST['filiere']) ? $_POST['filiere'] : array();
            $objet = $_POST['objet'] ?? '';
    
            // Convert spaces in $objet to underscores
            $objetWithoutSpaces = str_replace(' ', '_', $objet);
    
            // Check if file is uploaded
            if (isset($_FILES['pieceJointe'])) {
                // File handling
                $uploadDir = 'public/upload/emploi/';
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
                    header('Location: /apogee_ens/espaceadmin/ajouteremploi?error=InvalidFileType');
                    return;
                }
    
                // Move the uploaded file to the specified directory
                if (move_uploaded_file($_FILES['pieceJointe']['tmp_name'], $filePath)) {
                    // File uploaded successfully
                    // Now you can proceed to insert data into the database or perform other actions

                    $success = $this->EmploiModel->insertEmploi($selectedGroups, $objet, $filePath);
    
                    if ($success) {
                        header('Location: /apogee_ens/espaceadmin/emploi?success=success');
    
                    } else {
                        header('Location: /apogee_ens/espaceadmin/emploi?error=error');
                    }
                } else {
                    // Handle file upload error
                    header('Location: /apogee_ens/espaceadmin/emploi?error=file');
                }
            } else {
                // Handle case when file is not uploaded
                header('Location: /apogee_ens/espaceadmin/emploi?error=NoFileUploaded');
            }
        } else {
            // Handle non-POST requests
            header('Location: /apogee_ens/espaceadmin/emploi?error=InvalidRequestMethod');
        }
    }
    public function deleteSelectedemploi() {
        // Check if the form was submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Check if the deleteSelected button was clicked
            if (isset($_POST['deleteSelected'])) {
                // Check if selectedemploi is set and not empty
                if (isset($_POST['selectedemploi']) && !empty($_POST['selectedemploi'])) {
                    // Get the array of selected review IDs
                    $selectedemploi = $_POST['selectedemploi'];
    
                    foreach ($selectedemploi as $reviewId) {

                        // Fetch the file path associated with the review ID
                        $fileToDelete = $this->EmploiModel->getFilePathByEmploiwId($reviewId);
    
                        // Delete the file if it exists
                        if ($fileToDelete && file_exists($fileToDelete)) {
                            unlink($fileToDelete);
                        }
                    }
    
                    // Perform the deletion of reviews
                    $result = $this->EmploiModel->deleteSelectedemploi($selectedemploi);
    
                    // Check the result of the deletion
                    if ($result) {
                        // Success: Redirect with success message
                        header('Location: /apogee_ens/espaceadmin/emploi?success=emploi_deleted');
                        exit();
                    } else {
                        // Error: Redirect with error message
                        header('Location: /apogee_ens/espaceadmin/emploi?error=deletion_error');
                        exit();
                    }
                } else {
                    // No reviews selected for deletion
                    header('Location: /apogee_ens/espaceadmin/emploi?error=no_emploi_selected');
                    exit();
                }
            }
        }
    }

    // In AvisController.php

    public function showModifyEmploiForm() {
        // Retrieve the Emploi details based on the EmploiId
        $EmploiId = isset($_GET['id']) ? $_GET['id'] : null;
    
        // Check if EmploiId is provided
        if ($EmploiId) {
            $EmploiDetails = $this->EmploiModel->getEmploiDetails($EmploiId);
    
            // Check if the Emploi details are retrieved successfully
            if ($EmploiDetails) {
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
    
                // Pass the data to the view
                $this->EmploiView->showModifyEmploiForm($EmploiDetails, $error, $success);
    
            } else {
                // Handle the case when Emploi details are not available
                // Redirect or display an error message
                header('Location: /apogee_ens/espaceadmin/emploi?error=emploi_not_found');
                exit();
            }
        } else {
            // Handle the case when emploiId is not provided
            // Redirect or display an error message
            header('Location: /apogee_ens/espaceadmin/emploi?error=missing_Emploi_id');
            exit();
        }
    }
    
    public function modifyEmploi() {
        // Check if the form was submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Retrieve form data
            $emploiId = $_POST['emploi_id'] ?? '';
            $modifiedObjet = $_POST['modified_objet'] ?? '';
    
            // Fetch the current file path from the database
            $currentFilePath = $this->EmploiModel->getFilePathByEmploiwId($emploiId);
    
            // Validate file upload
            if (isset($_FILES['modified_pieceJointe']) && $_FILES['modified_pieceJointe']['error'] == UPLOAD_ERR_OK) {
                // Process the uploaded file
                $uploadDir = 'public/upload/emploi/';
                $originalFileName = $_FILES['modified_pieceJointe']['name'];
                $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);
                
                // Generate a unique filename to avoid overwriting existing files
                $uniqueFileName =$modifiedObjet . '.' . $fileExtension;
                $filePath = $uploadDir . $uniqueFileName;
    
                // Validate file type
                $allowedFileTypes = array('pdf', 'jpg', 'jpeg', 'png');
                if (!in_array(strtolower($fileExtension), $allowedFileTypes)) {
                    // Handle invalid file type error
                    header('Location: /apogee_ens/espaceadmin/modifyemploi?id=' . $emploiId . '&error=InvalidFileType');
                    exit();
                }
    
                // Move the uploaded file to the specified directory
                if (move_uploaded_file($_FILES['modified_pieceJointe']['tmp_name'], $filePath)) {
                    // File uploaded successfully, update the database
                    $success = $this->EmploiModel->updateemploi($emploiId, $modifiedObjet, $filePath);
    
                    if ($success) {
                        // Delete the old file
                        unlink($currentFilePath);
    
                        // Redirect with success message
                        header('Location: /apogee_ens/espaceadmin/emploi?success=emploi_modified');
                        exit();
                    } else {
                        // Handle database update error
                        header('Location: /apogee_ens/espaceadmin/showModifyemploiForm?id=' . $emploiId . '&error=database_error');
                        exit();
                    }
                } else {
                    // Handle file upload error
                    header('Location: /apogee_ens/espaceadmin/showModifyemploiForm?id=' . $emploiId . '&error=file_upload_error');
                    exit();
                }
            } else {
                // Handle case when no file is uploaded
                header('Location: /apogee_ens/espaceadmin/showModifyemploiForm?id=' . $emploiId . '&error=no_file_uploaded');
                exit();
            }
        } else {
            // Handle non-POST requests
            header('Location: /apogee_ens/espaceadmin/showModifyemploiForm?id=' . $emploiId . '&error=invalid_request_method');
            exit();
        }
    }
    



}