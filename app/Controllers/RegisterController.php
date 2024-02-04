<?php
// RegisterController.php

namespace App\Controllers;

use App\Models\UtilisateurModel;
use App\Views\ViewReinscr\RegisterView;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class RegisterController {
    private $utilisateurModel;
    private $registerView;

    public function __construct(UtilisateurModel $utilisateurModel, RegisterView $registerView) {
        $this->utilisateurModel = $utilisateurModel;
        $this->registerView = $registerView;
        session_start();

    }
    public function showDashboard() {

        if (isset($_SESSION['matricule'])) {
            $isUserData = $this->utilisateurModel->getUserByCNE($_SESSION['matricule']);
            $this->registerView->showDashboard($isUserConnected = true, $isUserData, 'Modifier mes informations');

        }

    }

    public function showRegisterForm() {
        // Start the session if not started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    
        // Check if 'matricule' is set in the session
        if (isset($_SESSION['matricule'])) {
            // Check if user data is complete
            $isUserDataComplete = $this->utilisateurModel->isUserDataComplete($_SESSION['matricule']);
    
            // Check if it's a modification request
            $modification = isset($_GET['modification']) && $_GET['modification'] === 'true';
    
            // Check if it's a new user registration
    
            // If it's a modification, retrieve user data and show the registration form
            if ($modification) {
                $matricule = $_SESSION['matricule'];
                $model = new UtilisateurModel();
                $user = $model->getUserByCNE($matricule);
                $contenu = "page1";
    
                // Show the registration form for modification
                $this->registerView->showRegisterForm($isUserConnected = true, $user, $contenu, 'Modifier mes informations');
                exit;
            }

            // If user data is complete, redirect to the dashboard
            if ($isUserDataComplete) {
                header('Location: /apogee_ens/register/dashboard');
                exit;
            } else {
                $matricule = $_SESSION['matricule'];
                $model = new UtilisateurModel();
                $user = $model->getUserByCNE($matricule);
                $contenu = "page1";
                $this->registerView->showRegisterForm($isUserConnected = true, $user , $contenu, 'Plateforme de gestion des inscriptions des étudiants');
                exit;
            }
        } else {
            $newUser = isset($_GET['enregistrement']) && $_GET['enregistrement'] === 'new_user';
            // If it's a new user registration, show the registration form
            if ($newUser) {
                $contenu = "new_user";

                // Show the registration form for a new user
                $this->registerView->showRegisterForm($isUserConnected = false, $user = null, $contenu, 'Création de compte sur la plateforme de préinscription');
                exit;
            }
        }
    }
    
    public function processForm()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate and sanitize inputs
            $civility = filter_input(INPUT_POST, 'civility', FILTER_SANITIZE_STRING);
            $familySituation = filter_input(INPUT_POST, 'familySituation', FILTER_SANITIZE_STRING);
            $firstName = filter_input(INPUT_POST, 'firstName', FILTER_SANITIZE_STRING);
            $lastName = filter_input(INPUT_POST, 'lastName', FILTER_SANITIZE_STRING);
            $firstNameArabic = filter_input(INPUT_POST, 'firstNameArabic', FILTER_SANITIZE_STRING);
            $lastNameArabic = filter_input(INPUT_POST, 'lastNameArabic', FILTER_SANITIZE_STRING);
            $cin = filter_input(INPUT_POST, 'cin', FILTER_SANITIZE_STRING);
            $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
            $birthCity = filter_input(INPUT_POST, 'birthCity', FILTER_SANITIZE_STRING);
            $birthCityArabic = filter_input(INPUT_POST, 'birthCityArabic', FILTER_SANITIZE_STRING);
            $birthProvince = filter_input(INPUT_POST, 'birthProvince', FILTER_SANITIZE_STRING);
            $birthDate = filter_input(INPUT_POST, 'birthDate', FILTER_SANITIZE_STRING);
            $sexe = filter_input(INPUT_POST, 'sexe', FILTER_SANITIZE_STRING);
            $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
            $codePostal = filter_input(INPUT_POST, 'codePostal', FILTER_SANITIZE_STRING);
            $currentCountry = filter_input(INPUT_POST, 'addressProvince', FILTER_SANITIZE_STRING);
            $nationality = filter_input(INPUT_POST, 'nationality', FILTER_SANITIZE_STRING);
            $addressProvince = filter_input(INPUT_POST, 'addressProvince', FILTER_SANITIZE_STRING);
            $addressProvince = filter_input(INPUT_POST, 'addressProvince', FILTER_SANITIZE_STRING);

            // ... (continue for other form fields)
            $userData = [
                'matricule' => $_SESSION['matricule'],
                'civility' => $civility,
                'familySituation' => $familySituation,
                'firstName' => $firstName,
                'lastName' => $lastName,
                'firstNameArabic' => $firstNameArabic,
                'lastNameArabic' => $lastNameArabic,
                'cin' => $cin,
                'phone' => $phone,
                'birthCity' => $birthCity,
                'birthCityArabic' => $birthCityArabic,
                'birthProvince' => $birthProvince,
                'birthDate' => $birthDate,
                'sexe' => $sexe,
                'address' => $address,
                'codePostal' => $codePostal,
                'currentCountry' => $currentCountry,
                'nationality' => $nationality,
                'etablissement' => $_POST['Etablissements'],
                'type_de_bac' => $_POST['TypedeBac'],
                'annee_du_bac' => $_POST['AnneeduBac'],
                'diplome_bac_plus_2' => $_POST['DiplomeBac'],
                'specialite' => $_POST['Specialité'],
                'annee_du_diplome' => $_POST['AnneeduDiplom'],
                'note_s1' => $_POST['NoteS1'],
                'note_s3' => $_POST['NoteS3'],
                'note_s2' => $_POST['NoteS2'],
                'note_s4' => $_POST['NoteS4'],
                'choix_filiere1' => $_POST['choix_filiere1'],
                'choix_filiere2' => $_POST['choix_filiere2'],
                'isUserDataComplete' => true
            ];
            
    
            // Convert spaces in $objet to underscores
            $objetWithoutSpaces = str_replace(' ', '_', $_SESSION['matricule']);
    
            // Check if file is uploaded
            if (isset($_FILES['pieceJointe'])) {
                // File handling
                $uploadDir = 'public/upload/Les_elevés_de_points/';
                $originalFileName = $_FILES['pieceJointe']['name'];
                
                $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);

                // Remove spaces from the original filename
                $fileNameWithoutSpaces = $objetWithoutSpaces . '.' . str_replace(' ', '', $fileExtension);
    
                $filePath = $uploadDir . $fileNameWithoutSpaces;
    
                // Validate file type
                $allowedFileTypes = array('pdf');
                $fileType = pathinfo($originalFileName, PATHINFO_EXTENSION);
    
                if (!in_array(strtolower($fileType), $allowedFileTypes)) {
                    // Handle invalid file type error
                    header('Location: /apogee_ens/espaceadmin/details?error=InvalidFileType');
                    return;
                }
    
                // Move the uploaded file to the specified directory
                if (move_uploaded_file($_FILES['pieceJointe']['tmp_name'], $filePath)) {
                    // File uploaded successfully
                    // Now you can proceed to insert data into the database or perform other actions
                    $userData['filePath'] = $filePath;
                    $success = $this->utilisateurModel->enregistrement($userData);

                    if ($success) {
                        // Registration successful
                        $_SESSION['success_message'] = 'Inscription réussie! Vous pouvez maintenant telecharer votre recu.';
                        $this->redirectWithSuccess('/apogee_ens/register/dashboard');
                    } else {
                        // Registration failed
                        $_SESSION['error_message'] = 'Erreur lors de l\'inscription. Veuillez réessayer.';
                        $this->redirectWithError('/apogee_ens/register/details?enregistrement=page1');
                    }
                } else {
                    // Handle file upload error
                    header('Location: /apogee_ens/espaceadmin/avis?error=file');
                }
            } else {
                // Handle case when file is not uploaded
                header('Location: /apogee_ens/register/details?enregistrement=page1&error=NoFileUploaded');
            }

            
        }
    }


    private function redirectWithSuccess($url) {
        header('Location: ' . $url);
        exit;
    }

    private function redirectWithError($url) {
        header('Location: ' . $url);
        exit;
    }

    public function registerUser() {
        try {
            // Validate and sanitize inputs
            $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
            $CNE = filter_input(INPUT_POST, 'CNE', FILTER_SANITIZE_STRING);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    
            if (empty($password) || empty($CNE) || empty($email)) {
                $response = ['success' => false, 'message' => 'Invalid input data.'];
            } else {
                // Hash the user's password using bcrypt
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
                // Generate a confirmation token
                $confirmationToken = bin2hex(random_bytes(16));
    
                // Prepare the user data for insertion into the database
                $userData = [
                    'CNE' => $CNE,
                    'email' => $email,
                    'password' => $hashedPassword,
                    'confirmationToken' => $confirmationToken,
                ];
    
                // Attempt to register the user in the database
                $userId = $this->utilisateurModel->registerUser($userData);
    
                if ($userId) {
                    // Registration successful
                    // Generate a confirmation link using the token
                    $confirmationLink = $this->generateConfirmationLink($CNE, $confirmationToken);
    
                    // Send a confirmation email to the user
                    $emailSent = $this->sendConfirmationEmail($email, $password ,$CNE, $confirmationLink);
    
                    if ($emailSent) {
                        $response = ['success' => true, 'message' => 'Inscription réussie! Courriel de confirmation envoyé. Veuillez vérifier votre boîte de réception.'];
                    } else {
                        $response = ['success' => false, 'message' => 'Error sending confirmation email.'];
                    }
                } else {
                    // Registration failed
                    $response = ['success' => false, 'userId' => null, 'message' => 'Error registering user.'];
                }
            }
        } catch (\PDOException $e) {
            // Log or handle the database exception
            $response = ['success' => false, 'message' => 'An error occurred during registration.'];
        } catch (\Exception $e) {
            // Log or handle the general exception
            $response = ['success' => false, 'message' => 'An unexpected error occurred.'];
        }
    
        // Move these lines outside of the try block
        header('Content-Type: application/json');
        echo json_encode($response);
    }
    
    private function generateConfirmationLink($userId, $confirmationToken) {
        // Generate a confirmation link
        return "http://localhost/apogee_ens/confirm?user=$userId&token=$confirmationToken";
    }

    private function sendConfirmationEmail($email, $password ,$CNE, $confirmationLink) {
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->SMTPDebug = SMTP::DEBUG_OFF;   // You can set DEBUG_SERVER for more detailed logs
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';     // Your SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'mohamedlafrikh26@gmail.com';   // Your SMTP username
            $mail->Password = 'wrtkwesqtlfjhkxb';   // Your SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  // Enable TLS encryption
            $mail->Port = 587;  // TCP port to connect to

            // Recipients
            $mail->setFrom('mohamedlafrikh26@gmail.com', 'app');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Confirmation de Candidature';
            
            $mail->Body ='
            <html>
            <head>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        background-color: #f4f4f4;
                        color: #333;
                        margin: 0;
                        padding: 0;
                    }
                    .container {
                        max-width: 600px;
                        margin: 20px auto;
                        background-color: #fff;
                        border-radius: 5px;
                        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                        padding: 20px;
                    }
                    .confirmation-text {
                        font-size: 16px;
                        margin-bottom: 20px;
                    }
                    .confirmation-link {
                        display: inline-block;
                        padding: 10px;
                        background-color: #4caf50;
                        color: #fff;
                        text-decoration: none;
                        border-radius: 3px;
                    }
                    .credentials {
                        margin-top: 20px;
                        font-size: 14px;
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <p class="confirmation-text">Votre candidature a été bien enregistrée </p>
                    <p class="confirmation-text">Pour activer votre compte, cliquez sur le lien suivant :</p>
                    <a class="confirmation-link" href="' . $confirmationLink . '">Activer le compte</a>
                    <p class="confirmation-text">Ensuite, pour accéder à votre compte, vous aurez besoin de :</p>
                    <ul class="credentials">
                        <li>Votre numéro de candidature ci-dessus</li>
                        <li>Votre CNE:   ' . $CNE . '></li>
                        <li>Votre mot de passe:' . $password . '></li>
                    </ul>
                </div>
            </body>
            </html>
            ';
            
            $mail->send();
            return true;
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }


}
