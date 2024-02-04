<?php

namespace App\Views\EspaceAdmin;

class AvisView
{
    private $headerView;
    private $footerView;

    public function __construct(HeaderAdminView $headerView, FooterAdminView $footerView)
    {
        $this->headerView = $headerView;
        $this->footerView = $footerView;

include_once('functions.php');

    }

    public function showAddAvisForm($groupeData, $error, $success, $title) {
        {
        $isUserConnected=true;
        $this->headerView->showHeader($isUserConnected,$title);
        ?>
    <div class="container mt-5">
        <h1>Ajouter un avis</h1>

        <form action="/apogee_ens/espaceadmin/submit_avis" method="post" enctype="multipart/form-data">




            <div class="form-group" id="groupe">
                <label for="filiere">Choisir une ou plusieurs groupe :</label>
                <?php foreach ($groupeData as $row): ?>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="groupe[]" value="<?php echo $row['id_grp']; ?>" id="groupe_<?php echo $row['id_grp']; ?>">
                            <?php echo $row['nom_grp']; ?>
                    
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="form-group">
                <label for="objet">Objet :</label>
                <input type="text" class="form-control" name="objet" required>
            </div>

            <div class="form-group">
                <label for="contenu">Contenu :</label>
                <textarea class="form-control" name="contenu" required></textarea>
            </div>

            <div class="form-group">
                <label for="pieceJointe">Pièce jointe :</label>
                <input type="file" class="form-control-file" name="pieceJointe" id="pieceJointe" accept=".pdf, .jpg, .jpeg, .png" required>
                <small class="form-text text-muted">Téléchargez uniquement des fichiers PDF ou image (.pdf, .jpg, .jpeg, .png).</small>
            </div>

            <button type="submit" class="btn btn-primary">Ajouter l'avis</button>
        </form>
    </div>

    <script>
        document.getElementById('typeAvis').addEventListener('change', function() {
            var filiereGroup = document.getElementById('filiere');
            filiereGroup.style.display = this.value === 'filiere' ? 'block' : 'none';
            var filiereGroup = document.getElementById('groupe');
            filiereGroup.style.display = this.value === 'groupe' ? 'block' : 'none';
        });
    </script>


        <?php
            }
        }

    public function showAllAvis($allAvis, $error, $success, $title) {
        $isUserConnected = true;
        $this->headerView->showHeader($isUserConnected, $title);
        ?>
    
        <div class="container mt-5">
            <h1>Tous les avis</h1>
    
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
                <a href="/apogee_ens/espaceadmin/ajouteravis" class="btn btn-primary">Ajouter un avis</a>
    
                <form action="/apogee_ens/espaceadmin/deleteavis" method="post" onsubmit="return confirm('Are you sure you want to delete selected reviews?')">
                    <div><button type="submit" class="btn btn-danger" name="deleteSelected">Supprimer les avis cochés</button></div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="selectAll"></th> <!-- Checkbox to select all reviews -->
                                <th>Utilisateur</th>
                                <th>Prénom et Nom</th>
                                <th>Groupe</th>
                                <th>Objet</th>
                                <th>Contenu</th>
                                <th>Pièce jointe</th>
                                <th>Action</th>
                                <!-- Add more columns if needed -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allAvis as $avis): ?>
                                <tr>
                                    <td><input type="checkbox" name="selectedAvis[]" value="<?php echo $avis['id_avis']; ?>"></td>
                                    <td><?php echo $avis['utilisateur_ens']; ?></td>
                                    <td><?php echo $avis['prenom']; ?> <?php echo $avis['nom']; ?></td>
                                    <td><?php echo implode(', ', $avis['groupe']); ?></td>
                                    <td><?php echo $avis['objet']; ?></td>
                                    <td><?php echo $avis['contenu']; ?></td>
                                    <td>
                                        <a href="<?php echo baseUrl() . $avis['piece_jointe']; ?>" target="_blank">
                                            <?php echo $avis['piece_jointe']; ?>
                                        </a>
                                    </td>
                                    <td>
                                        <a href="/apogee_ens/espaceadmin/showModifyAvisForm?id=<?php echo $avis['id_avis']; ?>" class="btn btn-warning btn-sm">Modifier</a>
                                    </td>
                                    <!-- Add more columns if needed -->
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </form>
            </div>
    
            <?php if (empty($allAvis)): ?>
                <p>Aucun avis trouvé.</p>
            <?php endif; ?>
        </div>
    
        <script>
            // JavaScript code to handle "Select All" checkbox functionality
            document.getElementById('selectAll').addEventListener('change', function () {
                var checkboxes = document.getElementsByName('selectedAvis[]');
                checkboxes.forEach(function (checkbox) {
                    checkbox.checked = !checkbox.checked;
                });
            });
        </script>
    
        <?php
    }
    
    // In AvisView.php
    public function showModifyAvisForm($groupeData,$avis,$error, $success,) {
        $isUserConnected = true;
        $this->headerView->showHeader($isUserConnected, "Modifier l'avis");

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
                    
                <h1>Modifier l'avis</h1>
                <?php
$groupeArray = explode(',', $avis['groupe']);
?>
                <form action="/apogee_ens/espaceadmin/modifyavis" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="avis_id" value="<?php echo $avis['id_avis']; ?>">
                    <div class="form-group" id="groupe">
        <label for="filiere">Choisir une ou plusieurs groupes :</label>
        <?php foreach ($groupeData as $row): ?>
            <div class="form-check">
                <?php
                $isChecked = in_array($row['id_grp'], $groupeArray);
                $checkedAttribute = $isChecked ? 'checked' : '';
                ?>
                <input class="form-check-input" type="checkbox" name="groupe[]" value="<?php echo $row['id_grp']; ?>" id="groupe_<?php echo $row['id_grp']; ?>" <?php echo $checkedAttribute; ?>>
                <?php echo $row['nom_grp']; ?>
            </div>
        <?php endforeach; ?>
    </div>

                    <div class="form-group">
                        <label for="modified_objet">Nouvel Objet :</label>
                        <input type="text" class="form-control" id="modified_objet" name="modified_objet" value="<?php echo $avis['objet']; ?>">
                    </div>

                    <div class="form-group">
                        <label for="modified_contenu">Nouveau Contenu :</label>
                        <textarea class="form-control" id="modified_contenu" name="modified_contenu"><?php echo $avis['contenu']; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="modified_pieceJointe">Pièce jointe :</label>
                        <input type="file" class="form-control-file" name="modified_pieceJointe" id="modified_pieceJointe" accept=".pdf, .jpg, .jpeg, .png" required>
                        <small class="form-text text-muted">Téléchargez uniquement des fichiers PDF ou image (.pdf, .jpg, .jpeg, .png).</small>
                    </div>

                    <button type="submit" class="btn btn-primary">Modifier l'avis</button>
                </form>
            </div>

        <?php
    }

}
