<?php

namespace App\Views\ViewReinscr;

class RegisterView
{
    private $headerView;
    private $footerView;

    public function __construct(HeaderView $headerView, FooterView $footerView)
    {
        $this->headerView = $headerView;
        $this->footerView = $footerView;
    }

public function showDashboard($isUserConnected, $userData, $title) {
    $this->headerView->showHeader($isUserConnected, $title);
    ?>

    <div class="col-md-12">
        <h3>Vous avez déjà candidaté aux catégories suivantes:</h3>
    </div>

    <?php if (isset($_SESSION['success_message']) || isset($_SESSION['error_message'])) : ?>
        <div class="alert <?php echo isset($_SESSION['success_message']) ? 'alert-success' : 'alert-danger'; ?>" role="alert">
            <strong><?php echo isset($_SESSION['success_message']) ? 'Félicitations!' : 'Alert!'; ?></strong>
            <?php echo isset($_SESSION['success_message']) ? $_SESSION['success_message'] : $_SESSION['error_message']; ?>
        </div>
        <?php unset($_SESSION['success_message'], $_SESSION['error_message']); ?>
    <?php endif; ?>

    <p>Welcome, <?php echo htmlspecialchars($userData['prenom'] . ' ' . $userData['nom']); ?></p>

    <div class="card mt-3">
        <div class="card-body">
            <h5 class="card-title">CNE: <?php echo htmlspecialchars($userData['matricule']); ?></h5>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-body">
            <h5 class="card-title">Email: <?php echo htmlspecialchars($userData['email']); ?></h5>
        </div>
    </div>

    <!-- Additional information with improved HTML structure -->
    <div class="card mt-3">
        <div class="card-body">
            <h4 class="card-title">Licence:</h4>

            <div class="color-6">Choix 1 =&gt; <?php echo htmlspecialchars($userData['choix_filiere1']); ?></div>
            <div class="color-6">Choix 2 =&gt; <?php echo htmlspecialchars($userData['choix_filiere2']); ?></div>

            <div class="mt-2">
                <b><a href="#" data-toggle="modal" data-target="#confirmPrintModal4" class="" role="button">Télécharger le reçu</a></b>
            </div>

            <div><span>Du: 2023-08-01 au: 2023-09-30</span></div>
        </div>
    </div>

    <?php
    $this->footerView->showFooter();
}



    public function showRegisterForm($isUserConnected,$user,$currentPage, $title)
    {
        $this->headerView->showHeader($isUserConnected,$title);

        // Start of PHP block
        switch ($currentPage) {
            case 'page1':
                $this->showPage1Form($user);

                break;
            case 'new_user':
                $this->showNewUsersForm();
                break;
            default:
                echo 'Default content for unknown page.';
                break;
        }
        // End of PHP block

        $this->footerView->showFooter();
    }

    private function showPage1Form($user)
    {
        ?>
<div class="container">
    <form action="/apogee_ens/register/processForm" method="post" class="container" enctype="multipart/form-data">
        <!-- Panel: Informations personnelles -->
        <div class="panel panel-default">
            <div class="panel-heading">Informations personnelles</div>
            <div class="panel-body">

                <div class="form-group row">
                    <div class="col-md-6">
                        <label for="civility">Civilité</label>
                        <select class="form-control" id="civility" name="civility">
                            <option value="">---Civilité---</option>
                            <option value="m" selected="selected">Monsieur</option>
                            <option value="mme">Madame</option>
                            <option value="mlle">Mademoiselle</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="familySituation">Situation familiale</label>
                        <select class="form-control js-family-situation" id="familySituation" name="familySituation">
                            <option value="">---Situation familiale---</option>
                            <option value="célibataire" selected="selected">Célibataire</option>
                            <option value="marié(e)">Marié(e)</option>
                            <option value="divorcé(e)">Divorcé(e)</option>
                            <option value="veuf">Veuf(veuve)</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-6">
                        <label for="firstName">Prénom</label>
                        <input type="text" class="form-control" placeholder="Prénom" id="firstName" name="firstName"
                            value="<?= $user['prenom'] ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="firstNameArabic">Prénom en arabe</label>
                        <input type="text" class="form-control direction-rtl keyboardInput" dir="rtl"
                            placeholder="Prénom en arabe" id="firstNameArabic" name="firstNameArabic"
                            value="<?= $user['prenom_ar'] ?>">
                    </div>
                </div>

                <!-- Continue adding your form fields and controls -->

                <div class="v-space pull-left"></div>

                <!-- ******************************************* -->

                <div class="form-group row">
                    <div class="col-md-6">
                        <label for="lastName">Nom</label>
                        <input type="text" class="form-control" placeholder="Nom" id="lastName" name="lastName"
                            value="<?= $user['nom'] ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="lastNameArabic">Nom en arabe</label>
                        <input type="text" class="form-control direction-rtl keyboardInput" dir="rtl"
                            placeholder="Nom en arabe" id="lastNameArabic" name="lastNameArabic"
                            value="<?= $user['nom_ar'] ?>">
                    </div>
                </div>

                <!-- Continue adding your form fields and controls -->
                <div class="v-space pull-left"></div>

                <!-- ----------------------------------------- -->

                <div class="form-group row">
                    <div class="col-md-6">
                        <label for="cin">CNIE</label>
                        <input type="text" class="form-control" placeholder="CNIE" id="cin" name="cin"
                            value="<?= $user['cin'] ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="phone">Numéro de téléphone</label>
                        <input type="text" class="form-control" placeholder="Numéro de téléphone" id="phone"
                            name="phone" value="<?= $user['phone'] ?>">
                    </div>
                </div>

                <!-- Continue adding your form fields and controls -->
                <div class="v-space pull-left"></div>

                <div class="form-group row">
                    <div class="col-md-6">
                        <label for="birthCity">Lieu de naissance, comme il est écrit sur votre carte nationale</label>
                        <input type="text" class="form-control"
                            placeholder="Lieu de naissance, comme il est écrit sur votre carte nationale" id="birthCity"
                            name="birthCity" value="<?= $user['lieuNaiss'] ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="birthCityArabic">Lieu de naissance, en arabe comme il est écrit sur votre carte
                            nationale</label>
                        <input type="text" class="form-control direction-rtl keyboardInput" dir="rtl"
                            placeholder="Lieu de naissance, en arabe comme il est écrit sur votre carte nationale"
                            id="birthCityArabic" name="birthCityArabic" value="<?= $user['birthCityArabic'] ?>">
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-6">
                        <label for="birthProvince">Province de naissance</label>
                        <select class="form-control" id="birthProvince" name="birthProvince">
                            <option value="">--- Province de naissance ---</option>
                            <option value="1">Agadir idda outanane</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="birthDate">Date de naissance</label>
                        <input class="form-control datepicker-js" placeholder="Date de naissance" data-language="fr"
                            id="birthDate" name="birthDate" value="<?= $user['dateNaiss'] ?>">
                    </div>
                </div>

                <!-- Continue adding your form fields and controls -->

                <div class="v-space pull-left"></div>

                <!-- ******************************************* -->

                <div class="form-group row">
                    <div class="col-md-6">
                        <label for="">Sexe</label>
                        <select class="form-control" id="sexe" name="sexe">
                            <option value="">---Sexe---</option>
                            <option value="M" selected="selected">Masculin</option>
                            <option value="F">Féminin</option>
                        </select>
                    </div>
                </div>

                <div class="v-space pull-left"></div>

                <!-- ******************************************* -->

                <div class="form-group row">
                    <div class="col-md-6">
                        <label for="address">Adresse</label>
                        <textarea class="form-control" placeholder="Adresse" id="address"
                            name="address"><?= $user['address'] ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label for="addressArabic">Adresse en arabe</label>
                        <textarea class="form-control direction-rtl keyboardInput" dir="rtl"
                            placeholder="Adresse en arabe" id="addressArabic" name="addressArabic"></textarea>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-6">
                        <label for="codePostal">Code Postal</label>
                        <input class="form-control datepicker-js" placeholder="Code Postal" data-language="fr"
                            id="codePostal" name="codePostal">
                    </div>
                    <div class="col-md-6">
                        <label for="addressProvince">Province de résidence</label>
                        <input class="form-control datepicker-js" placeholder="Province de résidence" data-language="fr"
                            id="addressProvince" name="addressProvince">
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-6">
                        <label for="currentCountry">Pays de résidence</label>
                        <input class="form-control datepicker-js" placeholder="Pays de résidence" data-language="fr"
                            id="currentCountry" name="currentCountry" value="<?= $user['currentCountry'] ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="nationality">Nationalité</label>
                        <input class="form-control datepicker-js" placeholder="Nationalité" data-language="fr"
                            id="nationality" name="nationality" value="<?= $user['nationality'] ?>">
                    </div>
                </div>

                <div class="v-space pull-left"></div>
                <!-- Panel: Diplômes -->
                <div class="panel panel-default">
                    <div class="panel-heading">Diplômes</div>
                    <div class="panel-body">

                        <div class="v-space"></div>

                        <div class="col-md-6">
                            <div class="group-control">
                                <div>Etablissements :</div>
                                <input type="test" class="form-control" placeholder="Etablissements" id="Etablissements"
                                    name="Etablissements" value="<?= $user['etablissement'] ?> ">
                            </div>
                            <div class="v-space"></div>
                        </div>

                        <div class="col-md-6">
                            <div class="group-control">
                                <div>Type de Bac</div>
                                <input type="test" class="form-control" placeholder="Type de Bac" id="TypedeBac"
                                    name="TypedeBac" value="<?= $user['type_de_bac'] ?>">
                            </div>
                            <div class="v-space"></div>
                        </div>

                        <div class="col-md-6">
                            <div class="group-control">
                                <div>Année du Bac</div>
                                <input type="text" class="form-control" placeholder="Année du Bac" id="AnneeduBac"
                                    name="AnneeduBac" value="<?= $user['annee_du_bac'] ?>">
                            </div>
                            <div class="v-space"></div>
                        </div>

                        <!-- Continue adding your form fields and controls -->

                        <div class="v-space pull-left"></div>

                        <!-- ******************************************* -->

                        <div class="col-md-6">
                            <div class="group-control">
                                <div>Diplôme Bac+2</div>
                                <input type="text" class="form-control" placeholder="Diplôme Bac+2" id="DiplomeBac"
                                    name="DiplomeBac" value="<?= $user['diplome_bac_plus_2'] ?>:">
                            </div>
                            <div class="v-space"></div>
                        </div>

                        <div class="col-md-6">
                            <div class="group-control">
                                <div>Spécialité</div>
                                <input type="text" class="form-control" placeholder="Spécialité" id="Specialité"
                                    name="Specialité" value="<?= $user['specialite'] ?>">
                            </div>
                            <div class="v-space"></div>
                        </div>
                        <div class="col-md-6">
                            <div class="group-control">
                                <div>Année du Diplôm</div>
                                <input type="text" class="form-control" placeholder="Année du Diplôm" id="AnneeduDiplom"
                                    name="AnneeduDiplom" value="<?= $user['annee_du_diplome'] ?>">
                            </div>
                            <div class="v-space"></div>
                        </div>

                        <!-- Continue adding your form fields and controls -->

                        <div class="v-space pull-left"></div>
                        <!-- ******************************************* -->

                        <div class="col-md-6">
                            <div class="group-control">
                                <div>Note S1</div>
                                <input type="text" class="form-control note-input" placeholder="Note S1" id="NoteS1" name="NoteS1"
                                    value="<?= $user['note_s1'] ?>">
                                    <div id="noteErrorMessage"></div>

                            </div>
                            <div class="v-space"></div>
                        </div>

                        <div class="col-md-6">
                            <div class="group-control">
                                <div>Note S3</div>
                                <input type="text" class="form-control note-input" placeholder="Note S3" id="NoteS3" name="NoteS3"
                                    value="<?= $user['note_s3'] ?>">
                                    <div id="noteErrorMessage"></div>

                            </div>
                            <div class="v-space"></div>
                        </div>
                        <div class="col-md-6">
                            <div class="group-control">
                                <div>Note S2</div>
                                <input type="text" class="form-control note-input" placeholder="Note S2" id="NoteS2" name="NoteS2"
                                    value="<?= $user['note_s2'] ?>">
                                    <div id="noteErrorMessage"></div>

                            </div>
                            <div class="v-space"></div>
                        </div>
                        <div class="col-md-6">
                            <div class="group-control">
                                <div>Note S4</div>
                                <input type="text" class="form-control note-input" placeholder="Note S4" id="NoteS4" name="NoteS4"
                                    <?=$user['note_s4'] ?>>
                                    <div id="noteErrorMessage"></div>

                            </div>
                            <div class="v-space"></div>
                        </div>
                        <div class="col-md-6">

                    <div class="form-group">
                        <?php if (!empty($user['piece_jointe'])) : ?>
                            <label for="currentPieceJointe">Fichier actuel :</label>
                            <a href="<?php echo BASE_URL ."/". $user['piece_jointe']; ?>" target="_blank">
                                Votre relevés de points
                            </a>
                        <?php endif; ?>
                    </div>


                        <div class="group-control">
                            <label for="pieceJointe">Les relevés de points:</label>
                            <input type="file" class="form-control-file" name="pieceJointe" id="pieceJointe" accept=".pdf" required>
                            <small class="form-text text-muted">Téléchargez uniquement des fichiers PDF ou image (.pdf).</small>
                            <small class="form-text text-muted">Tous les relevés de points dans un seul fichier pdf.</small>
                        </div>
                        <div class="v-space"></div>
                    </div>

                    

                        <!-- End of form fields -->

                    </div>
                </div>



                <div class="v-space pull-left"></div>
                <!-- Panel: Choix -->
                <div class="panel panel-default">
                    <div class="panel-heading">Choix</div>
                        <div class="panel-body">

                            <div class="v-space"></div>

                            <div class="col-md-6">
                                <div class="group-control">
                                    <div>Spécifiez votre premier choix: :</div>
                                        <select class="form-control"   name="choix_filiere1"  id="choix_filiere1">
                                            <option value="0">Choisissez</option>
                                            <option value="Mécatronique">Mécatronique</option>
                                            <option value="Métrologie, Qualité, Sécurité et environnement">Métrologie, Qualité, Sécurité et environnement</option>
                                            <option value="Ingénierie des systèmes d'information et réseaux">Ingénierie des systèmes d'information et réseaux</option>
                                            <option value="Gestion Comptable et Financièr">Gestion Comptable et Financière</option>
                                        </select>
                                    </div>
                            
                                <div class="v-space"></div>
                            </div>

                            <div class="col-md-6">
                                <div class="group-control">
                                    <div>Spécifiez votre deuxième choix: :</div>
                                    <select  class="form-control"  name="choix_filiere2" id="choix_filiere2">
                                        <option value="0">Choisissez</option>
                                        <option value="Mécatronique">Mécatronique</option>
                                        <option value="Métrologie, Qualité, Sécurité et environnement">Métrologie, Qualité, Sécurité et environnement</option>
                                        <option value="Ingénierie des systèmes d'information et réseaux">Ingénierie des systèmes d'information et réseaux</option>
                                        <option value="Gestion Comptable et Financièr">Gestion Comptable et Financière</option>
                                    </select>
                                </div>
                                <div class="v-space"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="v-space"></div>
                <button type="submit" class="btn btn-primary btn-block">Suivant</button>
                <div class="v-space"></div>
                <div class="v-space"></div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Fonction pour valider le format de la note
    function isValidNoteFormat(note) {
        var regex = /^\d{1,2},\d{3}$/;
        return regex.test(note);
    }

    // Écouteur d'événements pour les champs de saisie des notes
    document.querySelectorAll('.note-input').forEach(function (input) {
        input.addEventListener('input', function () {
            var note = this.value.trim();
            var errorMessage = document.getElementById('noteErrorMessage');

            // Vérifie si la note a un format valide
            if (!isValidNoteFormat(note)) {
                // Affiche un message d'erreur ou stylise le champ de saisie pour indiquer une erreur
                this.classList.add('invalid-note');
                errorMessage.textContent = 'Le format de la note doit être 12,123 par exemple.';
                errorMessage.style.color = 'red'; // Optionnel : Vous pouvez personnaliser le style du message d'erreur
            } else {
                // Supprime le style d'erreur si le format est valide
                this.classList.remove('invalid-note');
                errorMessage.textContent = ''; // Efface le message d'erreur
            }
        });
    });
});
</script>



<script>

$(document).ready(function() {
    // Store the original options of the second select box
    var originalOptions = $('#choix_filiere2 option').clone();

    // Event listener for the first select box
    $('#choix_filiere1').on('change', function() {
        // Get the selected option in the first select box
        var selectedOption = $(this).val();

        // Clone the original options
        var optionsClone = originalOptions.clone();

        // Remove the selected option from the optionsClone
        optionsClone = optionsClone.filter(function() {
            return $(this).val() !== selectedOption;
        });

        // Update the options of the second select box
        $('#choix_filiere2').html(optionsClone);
    });
});
</script>

<?php
    }

    private function showNewUsersForm()
    {
        ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/styles.css">
</head>

<body>
    <div style="color: red; margin-bottom: 10px; padding: 10px; border: 1px solid #4CAF50; background-color: #DFF0D8; border-radius: 5px;"
        id="validation">
        <!-- Your validation message will be displayed here -->
    </div>



    <!-- user_details_template.php -->
    <div class="container">
        <form id="registerForm" method="post" action="/apogee_ens/register/process">
            <!-- Panel: Informations personnelles -->
            <div class="panel panel-default">
                <div class="panel-heading">Création de compte sur la plateforme de préinscription</div>
                <div class="panel-body">

                    <div class="v-space"></div>

                    <div class="col-md-6">
                        <div class="group-control">
                            <label for="CNE">CNE:</label>
                            <input type="text" class="form-control" placeholder="CNE" id="CNE" name="CNE">
                        </div>
                        <div class="v-space"></div>
                    </div>

                    <div class="col-md-6">
                        <div class="group-control">
                            <label for="email">E-mail :</label>
                            <input type="email" class="form-control" placeholder="Email" id="email" name="email">
                            <span id="email_error" style="color: red;"></span>

                        </div>
                        <div class="v-space"></div>
                    </div>

                    <div class="col-md-6">
                        <div class="group-control">
                            <label for="password">Mot de passe :</label>
                            <input type="password" class="form-control" placeholder="Mot de passe" id="password"
                                name="password">
                        </div>
                        <div class="v-space"></div>
                    </div>

                    <div class="col-md-6">
                        <div class="group-control">
                            <label for="confirm_password">Confirmer le mot de passe :</label>
                            <input type="password" class="form-control" placeholder="Confirmation du mot de passe"
                                id="confirm_password" name="confirm_password">
                            <span id="password_match_error" style="color: red;"></span>
                        </div>
                        <div class="v-space"></div>
                    </div>


                    <div class="v-space pull-left"></div>

                    <!-- End of form fields -->

                </div>
            </div>

            <div class="v-space"></div>
            <button type="submit" class="btn btn-primary btn-block">Enregistrer</button>
            <div class="v-space"></div>
            <div class="v-space"></div>
        </form>
    </div>

    <script src="<?= BASE_URL ?>/public/js/registerValidation.js"></script>

</body>

</html>
<?php
    }



    public function showConfirmationMessage()
    {
        echo "Courriel de confirmation envoyé. Veuillez vérifier votre boîte de réception.";
    }
}