<?php

namespace App\Controllers;

use App\Models\UtilisateurModel;
use App\Views\ViewReinscr\LoginView;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class LoginController {
    private $utilisateurModel;
    private $loginView;

    public function __construct(UtilisateurModel $utilisateurModel, LoginView $loginView) {
        $this->utilisateurModel = $utilisateurModel;
        $this->loginView = $loginView;
    }

    public function showLoginForm() {
        $error = '';
        $success = '';
    
        if (isset($_GET['error'])) {
            $enregistrementErreur = 'Il n\'y a pas de compte avec l\'information que vous avez saisi';
            if ($_GET['error'] == 'error') {
                $error .= $enregistrementErreur;
            }
        }
    
        if (isset($_GET['success'])) {
            $enregistrementsuccess = 'Mot de passe mis à jour avec succès !';
            if ($_GET['success'] == 'success') {
                $success .= $enregistrementsuccess;
            }
        }
    
        // Render the login form with error and success messages
        $this->loginView->renderLoginForm($error, $success, 'Page de Connexion');
    }
    

    public function showForgottenForm() {
        $error = '';
        $success = '';
    
        // Messages constants    
        if (isset($_GET['error'])) {
            $error = match ($_GET['error']) {
                'error' => 'Il n\'y a pas de compte avec les informations que vous avez saisies.',
                default => '',
            };
        }
    
        if (isset($_GET['success'])) {
            $success = match ($_GET['success']) {
                'success' => 'Un email de réinitialisation du mot de passe a été envoyé avec succès!',
                default => '',
            };
        }
    
        // Afficher le formulaire la première fois sans message d'erreur ou de succès
        $this->loginView->renderForgottenForm($error, $success, 'Réinitialisation du mot de passe');
    }
    

    public function loginUser() {
        $cne = filter_input(INPUT_POST, 'cne', FILTER_SANITIZE_STRING);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

        // Validate input
        if (empty($cne) || empty($password)) {
            $this->showLoginFormWithError('Entrée invalide');
        }

        $user = $this->utilisateurModel->loginUser($cne, $password);

        if ($user) {
            session_start();
            $_SESSION['matricule'] = $user['matricule'];
            header('Location: /apogee_ens/register/details');
            exit;
        } else {
            header('Location: /apogee_ens/user/login?error=error');
        }
    }

    public function showLoginFormWithError($errorMessage) {
        $this->loginView->renderLoginFormWithError($errorMessage);
        exit;
    }
    

// In your LoginController or PasswordResetController

public function forgotten() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email']; // Assuming your form has an input named 'email'

        // Validate the email (you can use a validation library or simple checks)

        // Check if the email exists in the database
        $user = $this->utilisateurModel->checkEmailExists($email);

        if ($user) {
            $resetToken = $this->utilisateurModel->generatePasswordResetToken($email);

            // Send an email to the user with a link to the resetPassword action and include the $resetToken
            $resetLink = "http://localhost/apogee_ens/user/passwords/resetPassword?token=$resetToken"; // Adjust the URL
            $emailSubject = "Password Reset Request";
            $emailBody = "Click the following link to reset your password: $resetLink";

            // Use a mail library or PHP's mail function to send the email
             $this->sendPasswordResetEmail($email,  $resetLink);

            // You can also show a success message to the user
            header('Location: /apogee_ens/user/passwords/forgotten?success=success');

        } else {
            // The email doesn't exist in the database
            header('Location: /apogee_ens/user/passwords/forgotten?error=error');
        }
    } else {
        // Display the forgotten password form (you can create a separate view for this)
        $this->utilisateurView->renderforgottenForm(null, "Forgotten Password");
    }
}

public function resetPassword() {
    // Check if the token is valid
    $token = isset($_GET['token']) ? $_GET['token'] : null;

    $user = $this->utilisateurModel->getUserByResetToken($token);

    if ($user) {
        // Display the password reset form
        $this->loginView->renderPasswordResetForm($token, "Réinitialisation du mot de passe");
    } else {
        echo $token;
        // Invalid or expired token, handle accordingly (e.g., show an error message)
        echo "Invalid or expired token!";
    }
}

        public function updatePassword() {
            $token = isset($_POST['token']) ? $_POST['token'] : null;
            $password = isset($_POST['password']) ? $_POST['password'] : null;
            $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : null;

            // Check if the token is valid
            $user = $this->utilisateurModel->getUserByResetToken($token);

            if ($user) {
                // Update the user's password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                $this->utilisateurModel->updatePasswordWithToken($token, $hashedPassword);

                // Clear the reset token after updating the password
                $this->utilisateurModel->clearResetToken($user['email']);

                // Show a success message or redirect the user to the login page
                header('Location: /apogee_ens/user/login?success=success');
            } else {

                header('Location: /apogee_ens/user/login?error=error');

            }
        }

    private function sendPasswordResetEmail($email,  $confirmationLink) {
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
            $mail->Subject = 'Reinitialiser le mot de passe';
            
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
                    
                    Bonjour,

    Prière de cliquer sur le lien ci-dessous afin de réinitialiser votre mot de passe.

' . $confirmationLink . '                </div>
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
