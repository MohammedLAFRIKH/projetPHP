<?php

namespace App\Views\EspaceProf;

class IndexProfView
{
    private $headerView;
    private $footerView;

    public function __construct(HeaderProfView $headerView, FooterProfView $footerView)
    {
        $this->headerView = $headerView;
        $this->footerView = $footerView;
    }


    // AvisController.php
    public function showError($errorMessage)
    {
        // Display the error message to the user
        echo "<p>Error: $errorMessage</p>";
    }


    public function Annonce( $emploi, $title) {
        $this->headerView->showHeader($isUserConnected=true, $title);
        ?>


        <?php foreach ($emploi as $emploiItem): ?>
            <div class="container mt-5">
                <h1>Formulaire d'Annonce</h1>

                <form action="/apogee_ens/espaceprof/publierannonce" method="post" enctype="multipart/form-data">
                    <input type="hidden" id="filiere" name="filiere" value='<?php echo $emploiItem['filiere']; ?>' required>

                    <div class="form-group">
                        <label for="annonceText">Texte de l'Annonce :</label>
                        <textarea class="form-control" id="annonceText" name="annonceText" rows="4" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="pieceJointe">Pièce Jointe :</label>
                        <input type="file" class="form-control-file" id="pieceJointe" name="pieceJointe">
                    </div>

                    <button type="submit" class="btn btn-primary">Publier l'Annonce</button>
                </form>
            </div>
        <?php endforeach; ?>



        <?php
        $this->footerView->showFooter();
    }
    public function MesAnnonces( $emploi, $title) {
        $this->headerView->showHeader($isUserConnected=true, $title);
        ?>
        

       <h1>Formulaire d'Annonce</h1>
        <h3>Vous pouvez changer le sujet de chaque annonce et la republier</h3>
        <?php foreach ($emploi as $emploiItem): ?>
            <div class="container mt-5">

                <form action="/apogee_ens/espaceprof/publierannonce" method="post" enctype="multipart/form-data">
                    <input type="hidden" id="filiere" name="filiere" value='<?php echo $emploiItem['filiere']; ?>' required>
                    <input type="hidden" id="id" name="id" value='<?php echo $emploiItem['id']; ?>' required>

                    <div class="form-group">
                        <label for="annonceText">Texte de l'Annonce :</label>
                        <textarea class="form-control" id="annonceText" name="annonceText" rows="4" value='<?php echo $emploiItem['texte_annonce']; ?>' required><?php echo $emploiItem['texte_annonce']; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="currentPieceJointe">Fichier actuel :</label>
                        <p><?php echo $emploiItem['piece_jointe']; ?></p>
                    </div>

                    <div class="form-group">
                        <label for="pieceJointe">Nouvelle pièce jointe :</label>
                        <input type="file" class="form-control-file" id="pieceJointe" name="pieceJointe" accept=".pdf, .jpg, .jpeg, .png">
                        <small class="form-text text-muted">Formats acceptés : PDF, JPG, JPEG, PNG.</small>
                    </div>


                    <button type="submit" class="btn btn-primary">Publier l'Annonce</button>
                </form>
            </div>
        <?php endforeach; ?>



        <?php
        $this->footerView->showFooter();
    }
    public function showAllAvis($currentAvis, $totalPages, $currentPage, $title) {
        $this->headerView->showHeader($isUserConnected = true, $title);
        ?>
    
        <div class="container mt-5">
            <h1 class="mb-4">Avis pour Prof</h1>
    
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
                                <!-- Handle 'piece_jointe' accordingly -->
                                <?php $pieceJointePath = BASE_URL . '/' . htmlspecialchars($avis['piece_jointe']); ?>
                                <?php if (pathinfo($pieceJointePath, PATHINFO_EXTENSION) === 'pdf') : ?>
                                    <!-- If the piece jointe is a PDF, provide a link to download -->
                                    <a href="<?= $pieceJointePath ?>" target="_blank" class="btn btn-primary">Télécharger la pièce jointe (PDF)</a>
                                <?php else : ?>
                                    <!-- If the piece jointe is an image, display it -->
                                    <img src="<?= $pieceJointePath ?>" class="img-fluid" alt="Piece Jointe">
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
                <p class="no-avis">Aucun avis trouvé pour cet Prof.</p>
            <?php endif; ?>
        </div>
    
        <?php
        $this->footerView->showFooter();
    }
    
    

    public function renderLoginFormprof($userData)
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
