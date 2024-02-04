<?php

namespace App\Views\EspaceAdmin;

class EmploiView
{
    private $headerView;
    private $footerView;

    public function __construct(HeaderAdminView $headerView, FooterAdminView $footerView)
    {
        $this->headerView = $headerView;
        $this->footerView = $footerView;

         include_once('functions.php');

    }

    public function showemploiForm($filiereData, $error, $success, $title) {
        {
        $isUserConnected=true;
        $this->headerView->showHeader($isUserConnected,$title);
        ?>
    <div class="container mt-5">
        <h1>Ajouter un emploi</h1>

        <form action="/apogee_ens/espaceadmin/submit_emploi" method="post" enctype="multipart/form-data">


            <div class="form-group" id="filiere">
                <label for="filiere">Choisir une ou plusieurs filiere :</label>
                <?php foreach ($filiereData as $row): ?>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="filiere[]" value="<?php echo $row['ref_fil']; ?>" id="filiere_<?php echo $row['ref_fil']; ?>">
                        
                            <?php echo $row['intitule_fil']; ?>
                        
                    </div>
                <?php endforeach; ?>
            </div>


            <div class="form-group">
                <label for="objet">Objet :</label>
                <input type="text" class="form-control" name="objet" required>
            </div>

            <div class="form-group">
                <label for="pieceJointe">Pièce jointe :</label>
                <input type="file" class="form-control-file" name="pieceJointe" id="pieceJointe" accept=".pdf, .jpg, .jpeg, .png" required>
                <small class="form-text text-muted">Téléchargez uniquement des fichiers PDF ou image (.pdf, .jpg, .jpeg, .png).</small>
            </div>

            <button type="submit" class="btn btn-primary">Ajouter emploi</button>
        </form>
    </div>



        <?php
            }
        }



        public function showAllEmploi($allEmploi, $error, $success,  $title) {
            $isUserConnected = true;
            $this->headerView->showHeader($isUserConnected, $title);
            ?>
        
            <div class="container mt-5">
                <h1>Tous les emploi</h1>
        
                <?php if ($error): ?>
                    <div class="alert alert-danger mt-3">
                        <p class="error-message"><?php echo $error; ?></p>
                    </div>
                <?php endif; ?>
        
                <?php if ($success): ?>
                    <div class="alert alert-success mt-3">
                        <p class="success-message"><?php echo $success; ?></p>
                    </div>
                <?php endif; ?>
        
                <div class="mt-3 mb-3">
                    <a href="/apogee_ens/espaceadmin/ajouteremploi" class="btn btn-primary">Ajouter un emploi</a>
        
                    <form action="/apogee_ens/espaceadmin/deleteemploi" method="post" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer les emploi sélectionnés ?')">
                        <div><button type="submit" class="btn btn-danger" name="deleteSelected">Supprimer les emploi cochés</button></div>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="selectAll"></th> <!-- Checkbox to select all reviews -->
                                    <th>ID</th>
                                    <th>Objet</th>
                                    <th>Filiere</th>
                                    <th>Piece Jointe</th>
                                    <th>Au</th>
                                    <th>Action</th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($allEmploi as $emploi): ?>
                                    <tr>
                                        <td><input type="checkbox" name="selectedemploi[]" value="<?php echo $emploi['id']; ?>"></td>
                                        <td><?php echo $emploi['id']; ?></td>
                                        <td><?php echo $emploi['objet']; ?></td>
                                        <td><?php echo $emploi['filiere']; ?></td>

                                        <td>
                                            <a href="<?php echo baseUrl() . $emploi['piece_jointe']; ?>" target="_blank">
                                                <?php echo $emploi['piece_jointe']; ?>
                                            </a>
                                        </td>
                                        <td><?php echo $emploi['au']; ?></td>

                                        <td>
                                            <a href="/apogee_ens/espaceadmin/showModifyemploiForm?id=<?php echo $emploi['id']; ?>" class="btn btn-warning btn-sm">Modifier</a>
                                        </td>
                                        <!-- Add more columns if needed -->
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </form>
                </div>
        
                <?php if (empty($allEmploi)): ?>
                    <p>Aucun emploi trouvé.</p>
                <?php endif; ?>
            </div>
        
            <script>
                // JavaScript code to handle "Select All" checkbox functionality
                document.getElementById('selectAll').addEventListener('change', function () {
                    var checkboxes = document.getElementsByName('selectedemploi[]');
                    checkboxes.forEach(function (checkbox) {
                        checkbox.checked = !checkbox.checked;
                    });
                });
            </script>
        
            <?php
        }
        
    // In emploiView.php
    public function showModifyEmploiForm($emploi,$error, $success,) {
        $isUserConnected = true;
        $this->headerView->showHeader($isUserConnected, "Modifier l'emploi");

        ?>
    <div class="container mt-5">
            <?php if ($error): ?>
                <div class="alert alert-danger mt-3">
                    <p class="error-message"><?php echo $error; ?></p>
                </div>
            <?php endif; ?>
    
            <?php if ($success): ?>
                <div class="alert alert-success mt-3">
                    <p class="success-message"><?php echo $success; ?></p>
                </div>
            <?php endif; ?>
            
        <h1>Modifier l'emploi</h1>

        <form action="/apogee_ens/espaceadmin/modifyemploi" method="post" enctype="multipart/form-data">
            <input type="hidden" name="emploi_id" value="<?php echo $emploi['id']; ?>">

            <div class="form-group">
                <label for="modified_objet">Nouvel Objet :</label>
                <input type="text" class="form-control" id="modified_objet" name="modified_objet" value="<?php echo $emploi['objet']; ?>">
            </div>


            <div class="form-group">
                <label for="modified_pieceJointe">Pièce jointe :</label>
                <input type="file" class="form-control-file" name="modified_pieceJointe" id="modified_pieceJointe" accept=".pdf, .jpg, .jpeg, .png" required>
                <small class="form-text text-muted">Téléchargez uniquement des fichiers PDF ou image (.pdf, .jpg, .jpeg, .png).</small>
            </div>

            <button type="submit" class="btn btn-primary">Modifier l'emploi</button>
        </form>
    </div>

        <?php
    }

}
