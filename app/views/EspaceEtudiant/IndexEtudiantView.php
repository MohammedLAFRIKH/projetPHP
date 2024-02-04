<?php

namespace App\Views\EspaceEtudiant;

class IndexEtudiantView
{
    private $headerView;
    private $footerView;

    public function __construct(HeaderEtudiantView $headerView, FooterEtudiantView $footerView)
    {
        $this->headerView = $headerView;
        $this->footerView = $footerView;
    }


    public function showAllemploiHtml($emploiDetails,$tableContent, $error, $success, $title) {
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



<div id="content">
<!-- Assuming $emploiDetails is the array you're working with -->
<?php if (isset($emploiDetails['piece_jointe'])) : ?>
    <a href="<?php echo $emploiDetails['piece_jointe']; ?>" target="_blank">Download Piece Jointe</a>
<?php else : ?>
    <!-- Handle the case when "piece_jointe" key is not set -->
    <p>No piece jointe available</p>
<?php endif; ?>

<h2>Emploi du temps scolaire</h2>

<?php
// Assuming you have the schedule data in CSV format
echo $tableContent;

?>


<!-- ... -->
</body>
</html>

        <?php
            }
        }



    public function showDashboard($isUserConnected, $userData, $title) {
        $this->headerView->showHeader($isUserConnected, $title);
        ?>


        <!-- Main content -->
        <div id="content">
            <h1>Avis pour Etudiant</h1>

            <?php
            // Assuming $userData contains information about the current etudiant
            if ($userData) {
                $etudiantgroupe = $_SESSION['groupe'];
                $avisForEtudiant = $this->AvisModel->getAvisByGroupes([$etudiantgroupe]);
                

                if (!empty($avisForEtudiant)) {
                    // Display avis for etudiant
                    foreach ($avisForEtudiant as $avis) {
                        echo '<p>' . $avis['contenu'] . '</p>';
                        // Add more details if needed
                    }
                } else {
                    echo '<p>Aucun avis trouvé pour cet étudiant.</p>';
                }
            } else {
                echo '<p>Informations sur l\'étudiant non disponibles.</p>';
            }
            ?>
        </div>

        <?php
        $this->footerView->showFooter();
    }


    public function showAllAvis($currentAvis, $totalPages, $currentPage, $title) {
        $this->headerView->showHeader($isUserConnected = true, $title);
        ?>
    
        <div class="container mt-5">
            <h1 class="mb-4">Avis pour Étudiant</h1>
    
            <?php if (!empty($currentAvis)) : ?>
                <?php foreach ($currentAvis as $avis) : ?>
                    <div class="card mb-4">
                        <div class="card-body">
                            <?php if (isset($avis['objet'])) : ?>
                                <h3 class="card-title"><?= htmlspecialchars($avis['objet']) ?></h3>
                            <?php endif; ?>
    
                            <?php if (isset($avis['contenu'])) : ?>
                                <p class="card-text"><?= htmlspecialchars($avis['contenu']) ?></p>
                            <?php endif; ?>
    
                            <?php if (isset($avis['piece_jointe'])) : ?>
                                <?php $pieceJointePath = BASE_URL . '/' . htmlspecialchars($avis['piece_jointe']); ?>
                                <?php if (pathinfo($pieceJointePath, PATHINFO_EXTENSION) === 'pdf') : ?>
                                    <!-- If the piece jointe is a PDF, provide a link to download -->
                                    <a href="<?= $pieceJointePath ?>" target="_blank" class="btn btn-primary">Télécharger la pièce jointe (PDF)</a>
                                <?php else : ?>
                                    <!-- If the piece jointe is an image, display it with a maximum width of 500px -->
                                    <img src="<?= $pieceJointePath ?>" class="img-fluid" alt="Piece Jointe" style="max-height: 300px; width: auto; cursor: pointer;" onclick="openImage()">
                                    <script>
                                        function openImage() {
                                            var newWindow = window.open('<?= $pieceJointePath ?>', '_blank');
                                            newWindow.focus();
                                        }
                                        </script>

                                <?php endif; ?>
                            <?php endif; ?>

    
                            <!-- Add similar checks for other keys as needed -->
                        </div>
                    </div>
                <?php endforeach; ?>
    
                <!-- Pagination links -->
                <div class="d-flex justify-content-center mt-4">
                    <nav aria-label="Page navigation">
                        <ul class="pagination">
                            <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                                <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                </div>
            <?php else : ?>
                <p class="no-avis">Aucun avis trouvé pour cet étudiant.</p>
            <?php endif; ?>
        </div>
    
        <?php
        $this->footerView->showFooter();
    }
 
    
    public function showAllAnnonce($currentAnnonce, $totalPages, $currentPage, $title) {
        $this->headerView->showHeader($isUserConnected = true, $title);
        ?>
    
            <div class="container mt-5">
            <h1 class="mb-4">Annonce pour Etudiant</h1>

            <?php if (!empty($currentAnnonce)) : ?>
                <?php foreach ($currentAnnonce as $Annonce) : ?>
                    <div class="card mb-4">
                        <div class="card-body">
                            <?php if (isset($Annonce['prenom'])) : ?>
                                <h3 class="card-title">Posté par <?= htmlspecialchars($Annonce['prenom']) ?> <?= htmlspecialchars($Annonce['nom']) ?></h3>
                            <?php endif; ?>

                            <?php if (isset($Annonce['texte_annonce'])) : ?>
                                <p class="card-text"><?= htmlspecialchars($Annonce['texte_annonce']) ?></p>
                            <?php endif; ?>

                            <?php if (isset($Annonce['piece_jointe'])) : ?>
                                <!-- Handle 'piece_jointe' accordingly -->
                                <?php $pieceJointePath = BASE_URL . '/' . htmlspecialchars($Annonce['piece_jointe']); ?>
                                <?php if (pathinfo($pieceJointePath, PATHINFO_EXTENSION) === 'pdf') : ?>
                                    <!-- If the piece jointe is a PDF, provide a link to download -->
                                    <a href="<?= $pieceJointePath ?>" target="_blank" class="btn btn-primary">Télécharger la pièce jointe (PDF)</a>
                                <?php else : ?>
                                    <!-- If the piece jointe is an image, display it -->
                                    <img src="<?= $pieceJointePath ?>" class="img-fluid" alt="Piece Jointe">
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php if (isset($Annonce['created_at'])) : ?>
                                <p class="card-text"><small class="text-muted">Date de publication: <?= htmlspecialchars($Annonce['created_at']) ?></small></p>
                            <?php endif; ?>

                            <!-- Add similar checks for other keys as needed -->
                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- Pagination links -->
                <div class="d-flex justify-content-center mt-4">
                    <nav aria-label="Page navigation">
                        <ul class="pagination">
                            <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                                <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                </div>
                <?php else : ?>
                    <p class="no-annonce">Aucun Annonce trouvé pour cet étudiant.</p>
                <?php endif; ?>
            </div>

        <?php
        $this->footerView->showFooter();
    }
    

    public function renderLoginFormetudiant($userData)
    {
        $this->headerView->showHeader($true=true, "Modifier les informations de l'utilisateur");
        ?>
            <div class="container mt-2">
                <h1>Modifier les informations de l'utilisateur</h1>

                <form action="/apogee_ens/user/update" method="post">
                    <div class="form-group">
                        <label for="userId">Matricule :</label>
                        <input class="form-control" name="userId" id="userId" value="<?php echo $userData['matricule']; ?>" readonly>
                        
                        <label for="filiere">Filiere :</label>
                        <input type="text" class="form-control" id="filiere" name="filiere" value="<?php echo $userData['filiere']; ?>" readonly required>

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
                        <input class="form-control" type="password" id="password" name="password"  required>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirmer le mot de passe :</label>
                        <input  class="form-control" type="password" id="confirm_password" name="confirm_password" required>
                    </div>


                    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                    <a href="javascript:history.back()" class="btn btn-secondary p-2">
                    <span class="mr-2">Retour</span>
                    <i class="fas fa-arrow-left"></i>
                </a>
                </form>
            </div>
        <?php
    }


}
