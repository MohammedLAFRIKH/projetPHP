<?php

namespace App\Views\EspaceProf;

class EmploiProfView
{
    private $headerView;
    private $footerView;

    public function __construct(HeaderProfView $headerView, FooterProfView $footerView)
    {
        $this->headerView = $headerView;
        $this->footerView = $footerView;

         include_once('functions.php');

    }

    public function showAllemploiHtml($tableContent, $error, $success, $title) {
        {
            $isUserConnected = true;
            $this->headerView->showHeader($isUserConnected, $title);
    ?>


    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        #addSessionForm {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<h2>Emploi du temps scolaire</h2>



<?php
echo "<h5 class='text-primary'>L'horaire peut être modifié à partir d'ici :</h5>";
echo '<a href="/apogee_ens/espaceprof/changementenemploiFoem" class="btn btn-warning btn-sm">Changement d\'horaire</a>';

echo $tableContent;

?>


<!-- ... -->
</body>
</html>

        <?php
            }
        }



        public function changementEploiForm($filiereNames, $error, $success,  $title) {
            $isUserConnected = true;
            $this->headerView->showHeader($isUserConnected, $title);
            ?>
            <div class="container mt-5"><!-- ... -->
<div id="addSessionForm">
    <h3>Ajouter une séance</h3>
    <form action="/apogee_ens/espaceprof/processAddSession" method="post" id="sessionForm">

    <label for="selectedFiliere">Choisir une filière :</label>
            <select class="form-control" name="selectedFiliere" id="selectedFiliere">
                <?php foreach ($filiereNames as $row): ?>
                    <option value="<?php echo $row['ref_fil']; ?>"><?php echo $row['intitule_fil']; ?></option>
                <?php endforeach; ?>
            </select>
    <label for="day">Jour :</label>
    <select id="day" name="day">
        <option value="Lundi">Lundi</option>
        <option value="Mardi">Mardi</option>
        <option value="Mercredi">Mercredi</option>
        <option value="Jeudi">Jeudi</option>
        <option value="Vendredi">Vendredi</option>
        <option value="Samedi">Samedi</option>
    </select>
    <br>
    <label for="time">Heure :</label>
    <select id="time" name="time">
        <option value="8h30-11h30">8h30-11h30</option>
        <option value="15h-18h">15h-18h</option>
    </select>
    <br>
    <label for="subject">Matière :</label>
    <input type="text" id="subject" name="subject" placeholder="Ex: Mathématiques">
    <br>
    <br>
    <label for="subject">Location :</label>
    <input type="text" id="Location" name="Location" placeholder="Ex: Salle ELEC 2">
    <br>
    <br>
    <label for="subject">Professor :</label>
    <input type="text" id="Professor" name="Professor" placeholder="Ex: BARKIA">
    <br>
    <button type="button" onclick="addSession()">Ajouter</button>
</form>


</div>
</div>
<!-- ... -->
<script>
   function handleSuccess(data) {
    console.log('Session added successfully:', data);
    // Add any specific actions you want to perform on success
}

async function addSession() {
    try {
        const baseUrl = window.location.origin;
        const response = await fetch(`${baseUrl}/apogee_ens/espaceprof/processAddSession`, {
            method: 'POST',
            body: new URLSearchParams(new FormData(document.getElementById('sessionForm')))
        });

        if (response.ok) {
            const responseData = await response.json();

            if (responseData.status === 'success') {
                handleSuccess(responseData.data);
            } else {
                handleError(responseData.message);
            }
        } else {
            console.error('Failed to submit form to the controller. Status:', response.status);

            try {
                const responseText = await response.json();
                console.error('Response JSON:', responseText);
            } catch (jsonError) {
                console.error('Response Text:', await response.text());
            }
        }
    } catch (error) {
        console.error('Error submitting form:', error);
    }
}





</script>
</body>
            </html>
        
            <?php
        }
    public function showemploiForm($filiereData, $error, $success, $title)
     {
        ?>
            <div class="container mt-5">
                <h1>Ajouter un emploi</h1>

                <form action="/apogee_ens/espaceprof/submit_emploi" method="post" enctype="multipart/form-data">


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



    public function showAllEmploi($filiereData ,$allEmploi, $error, $success,  $title) {
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
                                <div class="container mt-5">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <form action="/apogee_ens/espaceprof/searchemploi" method="post">
                                                <div class="form-group">
                                                <label for="selectedFiliere">Choisir une filière :</label>
                                                <select class="form-control" name="selectedFiliere[]" id="selectedFiliere">
                                                    <?php foreach ($filiereData as $row): ?>
                                                        <option value="<?php echo $row['ref_fil']; ?>"><?php echo $row['intitule_fil']; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                </div>
                                                <input type="submit" class="btn-sm btn btn-primary px-3" value="Rechercher">
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" id="selectAll"></th> <!-- Checkbox to select all reviews -->
                                            <th>ID</th>
                                            <th>Objet</th>
                                            <th>Filiere</th>
                                            <th>Piece Jointe</th>

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

                                                <td>
                                                    <a href="/apogee_ens/espaceprof/formannonce?id=<?php echo $emploi['id']; ?>" class="btn btn-warning btn-sm">Annonce de changement d'horaire</a>
                                                </td>
                                                <!-- Add more columns if needed -->
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </main>
                </div>
            </div>
        </body>
        </html>
    
        <?php
    }
    
    public function showSearchResults($searchResults, $error, $success,  $title) {
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

            <a href="javascript:history.back()" class="btn btn-secondary p-2">
                <span class="mr-2">Retour</span>
                <i class="fas fa-arrow-left"></i>
            </a>

                    <table class="table">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="selectAll"></th> <!-- Checkbox to select all reviews -->
                                <th>ID</th>
                                <th>Objet</th>
                                <th>Filiere</th>
                                <th>Piece Jointe</th>

                                <th>Action</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($searchResults as $emploi): ?>
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

                                    <td>
                                        <a href="/apogee_ens/espaceprof/annonce?id=<?php echo $emploi['id']; ?>" class="btn btn-warning btn-sm">Annonce de changement d'horaire</a>
                                    </td>
                                    <!-- Add more columns if needed -->
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
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


}
