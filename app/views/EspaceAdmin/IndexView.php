<?php

namespace App\Views\EspaceAdmin;

class IndexView
{
    private $headerView;
    private $footerView;

    public function __construct(HeaderAdminView $headerView, FooterAdminView $footerView)
    {
        $this->headerView = $headerView;
        $this->footerView = $footerView;
    }

    public function renderLoginForm($isUserConnected ,$error = null ,$success = null, $title)
    {
        $this->headerView->showHeader($isUserConnected ,$title);
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>   
            <style>
                /* Reset some default styles */
                body,
                h1,
                p,
                label,
                input,
                button,
                a {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }

                body {
                    font-family: 'Arial', sans-serif;
                    background-color: #f4f4f4;
                    color: #333;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    min-height: 100vh;
                    margin: 0;
                }

                .header {
                    background: #3678af;
                    color: #fff;
                    padding: 10px;
                    width: 100%;
                }

                .header-content {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 10px 20px; /* Added padding for space around content */
                }

                .logo-link {
                    display: flex;
                    align-items: center;
                }

                .logo {
                    float: left;
                    height: 60px;
                }

                .menu-list {
                    display: none;
                    width: 42px;
                    margin-right: 14px;
                }

                .float-right {
                    display: flex;
                    align-items: center;
                }

                .menu-links {
                    display: flex;
                    justify-content: flex-end; /* Align menu links to the right */
                }

                .menu-header {
                    margin-left: 20px; /* Add margin between menu items */
                    text-decoration: none;
                    color: #fff;
                    font-weight: bold;
                    transition: color 0.3s ease;
                }

                .menu-header:hover {
                    color: #eee; /* Change the hover color */
                }

                /* ... (remaining styles) */

                .container {
                    max-width: 600px;
                    width: 100%;
                    margin: auto;
                }

                .login-container {
                    background-color: #fff;
                    border-radius: 5px;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                    padding: 20px;
                    margin-top: 20px;
                    width: 100%;
                    box-sizing: border-box;
                }

                h1 {
                    color: #333;
                    text-align: center;
                    margin-bottom: 20px;
                }

                label {
                    display: block;
                    margin: 10px 0;
                    color: #333;
                }

                input {
                    width: 100%;
                    padding: 8px;
                    margin-bottom: 10px;
                    box-sizing: border-box;
                }

                button,
                .btn-login {
                    background-color: #4caf50;
                    color: #fff;
                    padding: 10px;
                    border: none;
                    border-radius: 3px;
                    cursor: pointer;
                    width: 100%;
                    transition: background-color 0.3s ease;
                }

                button:hover,
                .btn-login:hover {
                    background-color: #45a049;
                }

                .forgot-password {
                    display: block;
                    text-align: center;
                    text-decoration: none;
                    color: #333;
                    margin-top: 10px;
                }
            </style>
        </head>
            <div class="container">
                <div class="login-container">
                    <form method="post" action="/apogee_ens/login/process">
                        <h1>Page de Connexion</h1>
                        <?php if ($error): ?>
                            <div class="alert alert-danger center-text">
                                <p class="error-message"><?php echo $error; ?></p>
                            </div>
                        <?php endif; ?>
                        <div>
                        <?php if ($success): ?>
                            <div class="alert alert-success center-text">
                                <p class="success-message"><?php echo $success; ?></p>
                            </div>
                        <?php endif; ?>
                        <label for="cne">Matricule :</label>
                        <input type="text" id="matricule" name="matricule" required="">
                        <label for="password">Mot de passe :</label>
                        <input type="password" id="password" name="password" required="">
                        <button type="submit" class="btn-login">Connexion</button>
                        <a href="/apogee_ens/passwords/forgotten" class="forgot-password">Mot de passe oublié</a>
                    </form>
                </div>
            </div>
            <div class="v-space"></div>
        </body>
        </html>
     <?php
    }

    public function renderforgottenForm($error = null ,$success = null, $title)
    {
        $isespaceadminConnected = null;
        $this->headerView->showHeader($isespaceadminConnected, $title);
        ?>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="v-space"></div>
                    <div class="center-text text-4">Reinitialiser le mot de passe</div>
                    <div class="v-space"></div>
                    <form action="/apogee_ens/passwords/forgotten/process" method="post" class="container2">
                    <?php if ($error): ?>
                            <div class="alert alert-danger center-text">
                                <p class="error-message"><?php echo $error; ?></p>
                            </div>
                        <?php endif; ?>
                        <div>
                        <?php if ($success): ?>
                            <div class="alert alert-success center-text">
                                <p class="success-message"><?php echo $success; ?></p>
                            </div>
                        <?php endif; ?>
                        <div>
                            <div class="group-control">
                                <span>Entrez votre email</span>
                                <input type="text" class="form-control" placeholder="Ex: karam.elfetouaki@gmail.com" id="email" name="email" value="">
                            </div>
                            <div class="v-space"></div>,<BR>
                            <button type="submit" class="btn btn-primary btn-block">Envoyer le mail à mon adresse électronique</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php
    }
   
    public function renderPasswordResetForm($token, $title)
    {
        $isespaceadminConnected = null;
        $this->headerView->showHeader($isespaceadminConnected, $title);
        ?>
        <!-- formulaire_reinitialisation_mot_de_passe.html -->

        <div class="container">
            <h2>Réinitialiser le mot de passe</h2>
            <form action="/apogee_ens/passwords/reset" method="post">
                <!-- Ajoutez vos champs de formulaire, par exemple, mot de passe, confirmation du mot de passe, champ de jeton caché -->
                <label for="password">Nouveau mot de passe :</label>
                <input type="password" id="password" name="password" required>

                <label for="confirm_password">Confirmer le mot de passe :</label>
                <input type="password" id="confirm_password" name="confirm_password" required>

                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

                <button type="submit">Réinitialiser le mot de passe</button>
            </form>
        </div>
        <?php
    }
    public function modifyInfoformadmin($userData)
    {
        $this->headerView->showHeader(true, "Modifier les informations de l'utilisateur");
        ?>
            <div class="container mt-2">
                <h1>Modifier les informations de l'utilisateur</h1>

                <form action="/apogee_ens/user/update" method="post">
                    <div class="form-group">
                        <label for="userId">Matricule :</label>
                        <input class="form-control" name="userId" id="userId" value="<?php echo $userData['matricule']; ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label for="firstName">Prénom :</label>
                        <input type="text" class="form-control" id="firstName" name="firstName" value="<?php echo $userData['prenom']; ?>" readonly required>
                    </div>

                    <div class="form-group">
                        <label for="lastName">Nom :</label>
                        <input type="text" class="form-control" id="lastName" name="lastName" value="<?php echo $userData['nom']; ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label for="Email">Email :</label>
                        <input type="text" class="form-control" id="Email" name="Email" value="<?php echo $userData['email']; ?>">
                    </div>

                    <div class="form-group">
                        <label for="password">Nouveau mot de passe :</label>
                        <input class="form-control" type="password" id="password" name="password" required>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirmer le mot de passe :</label>
                        <input class="form-control" type="password" id="confirm_password" name="confirm_password" required>
                    </div>


                    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                </form>
            </div>
        <?php
    }


    public function showDashboard($isUserConnected, $userData, $title) {
        $this->headerView->showHeader($isUserConnected, $title);
        ?>
    
        <?php $this->displayMessages(); ?>
            <!-- Main content -->
            <div id="content">
                <h1>Ajouter un avis</h1>
                
            </div>
        </div>
        <?php
        $this->footerView->showFooter();
    }
    
    private function displayMessages() {
        // Display success message if set in the session
        if (isset($_SESSION['success_message'])) {
            echo '<div class="alert alert-success" role="alert">';
            echo '<strong>Félicitations!</strong> ' . $_SESSION['success_message'];
            echo '</div>';
            unset($_SESSION['success_message']); // Clear the success message after displaying
        }
    
        // Display error message if set in the session
        if (isset($_SESSION['error_message'])) {
            echo '<div class="alert alert-danger" role="alert">';
            echo '<strong>Alert!</strong> ' . $_SESSION['error_message'];
            echo '</div>';
            unset($_SESSION['error_message']); // Clear the error message after displaying
        }
    }

    

}
