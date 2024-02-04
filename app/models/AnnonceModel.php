<?php
namespace App\Models;

use App\Database;

class AnnonceModel {
    private $connexion;

    public function __construct() {
        $database = Database::getInstance();
        $this->connexion = $database->getConnection();
    }

    public function getAnnonceByfilieres($filiere) {
        try {
            // Prepare the SQL query with the JOIN operation
            $sql = "SELECT a.*, e.prenom, e.nom
                    FROM annonces a
                    INNER JOIN utilisateur_ens e ON a.editeur = e.matricule
                    WHERE FIND_IN_SET(?, a.filiere)";
    
            $stmt = $this->connexion->prepare($sql);
    
            // Bind parameter
            $stmt->bind_param("s", $filiere);
    
            // Execute the query
            $stmt->execute();
    
            // Get the result set
            $result = $stmt->get_result();
    
            // Fetch all annonces with editor information
            $annonces = $result->fetch_all(MYSQLI_ASSOC);
    
            // Close the database connection
            $this->connexion = null;
    
            // Check if annonces were found
            if ($annonces) {
                return $annonces;
            } else {
                return false;
            }
        } catch (\PDOException $e) {
            // Handle database connection error
            // You may want to log the error or display a user-friendly message
            die("Database connection error: " . $e->getMessage());
        }
    }
    

 
    public function getAllEmploi() {
        try {
            // Perform a join to get nom and prenom
            $query = $this->connexion->query('
                SELECT emploidutemp.*, filiere.intitule_fil
                FROM emploidutemp
                JOIN filiere ON emploidutemp.filiere = filiere.ref_fil
            ');
    
            // Check if the query was successful
            if ($query) {
                // Fetch the results as an associative array
                $results = $query->fetch_all(MYSQLI_ASSOC);
    
                // Return the results
                return $results;
            } else {
                // Handle query error if needed
                return false;
            }
        } catch (PDOException $e) {
            // Handle database connection or query execution errors
            echo 'Error: ' . $e->getMessage();
            return false;
        }
    }
    

    public function getAnnonceByID($matricule) {
        try {


            // Préparez la requête SQL pour récupérer les détails de l'emploi par son ID
            $sql = "SELECT * FROM annonces WHERE editeur = ?";

            $stmt = $this->connexion->prepare($sql);

            // Liez le paramètre
            $stmt->bind_param("i", $matricule);

            // Exécutez la requête
            $stmt->execute();

            // Obtenez le jeu de résultats
            $result = $stmt->get_result();

            // Récupérez les détails de l'emploi
            $annonces = $result->fetch_all(MYSQLI_ASSOC);

            // Fermez la connexion à la base de données
            $this->connexion->close();

            // Vérifiez si l'emploi a été trouvé
            if ($annonces) {
                return $annonces;
            } else {
                return false;
            }
        } catch (\PDOException $e) {
            // Gérez les erreurs de connexion à la base de données
            die("Erreur de connexion à la base de données : " . $e->getMessage());
        }
    }

    public function deleteSelectedannonces(array $selectedEmploi)
    {
       // Ensure that $selectedEmploi is not empty to avoid SQL errors
       if (!empty($selectedEmploi)) {
           // Use placeholders for the IDs in the IN clause
           $placeholders = implode(',', array_fill(0, count($selectedEmploi), '?'));

           $sql = "DELETE FROM emploidutemp WHERE id IN ($placeholders)";
           $stmt = $this->connexion->prepare($sql);

           // Bind parameters dynamically based on the number of selected reviews
           $stmt->bind_param(str_repeat('s', count($selectedEmploi)), ...$selectedEmploi);

           if ($stmt->execute()) {
               return true; // Success
           } else {
               return false; // Error
           }
       }

       return false; // No reviews selected
   }

   // In EmploiModel.php

    public function getEmploiDetails($EmploiId) {
        // Perform a query to get the details of the specific Emplo based on EmploId
        $stmt = $this->connexion->prepare('
            SELECT *
            FROM emploidutemp
            WHERE id = ?
        ');

        $stmt->bind_param("i", $EmploiId);
        $stmt->execute();

        $result = $stmt->get_result();

        // Check if any rows are returned
        if ($result->num_rows > 0) {
            // Fetch the Emplo details as an associative array
            return $result->fetch_assoc();
        } else {
            // Return false if Emplo details are not found
            return false;
        }
    }


   public function getFilePathByAnnonceId($EmploiwId) {
    $query = "SELECT piece_jointe FROM annonces WHERE id = ?";
    $stmt = $this->connexion->prepare($query);
    $stmt->bind_param("s", $EmploiwId);

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['piece_jointe'];
    }

    return null; // or handle accordingly if no record is found
}
public function updateemploi($avisId, $modifiedObjet, $modified_pieceJointe){
    // Prepare and execute the SQL query to update the avis in the database

    $stmt = $this->connexion->prepare('
        UPDATE emploidutemp
        SET objet = ?,

            piece_jointe = ?
        WHERE id = ?
    ');

    $stmt->bind_param("ssi", $modifiedObjet,$modified_pieceJointe, $avisId);

    if ($stmt->execute()) {
        return true; // Success
    } else {
        return false; // Error
    }
}
    
    public function insertAnnouncement( $editeur,$filiere, $texte_annonce,  $pieceJointe ) {

        $sql = "INSERT INTO annonces (editeur, filiere, texte_annonce,  piece_jointe)
                VALUES (?, ?, ? , ? )";
        
        $stmt = $this->connexion->prepare($sql);
        $stmt->bind_param("ssss", $editeur,$filiere, $texte_annonce, $pieceJointe);
    
        if ($stmt->execute()) {
            return true; // Success
        } else {
            return false; // Error
        }
    }

    
/**
 * Search emploi by filiere
 *
 * @param array $selectedFilieres
 * @return mixed|array|null
 */
public function searchEmploiByFiliere(array $selectedFilieres) {
    try {
        // Ensure that $selectedFilieres is not empty
        if (empty($selectedFilieres)) {
            return null; // or handle accordingly
        }

        // Create a placeholder string for the IN clause
        $placeholders = str_repeat('?,', count($selectedFilieres) - 1) . '?';

        // Prepare the SQL query with the IN clause
        $sql = "SELECT * FROM emploidutemp WHERE filiere IN ($placeholders)";

        $stmt = $this->connexion->prepare($sql);

        // Bind parameters dynamically based on the number of selected filieres
        $stmt->bind_param(str_repeat('s', count($selectedFilieres)), ...$selectedFilieres);

        // Execute the query
        $stmt->execute();

        // Get the result set
        $result = $stmt->get_result();

        // Fetch all emploi records
        $emploiRecords = $result->fetch_all(MYSQLI_ASSOC);

        // Close the database connection
        $this->connexion->close();

        return $emploiRecords;
    } catch (\PDOException $e) {
        // Handle database connection error
        // You may want to log the error or display a user-friendly message
        die("Database connection error: " . $e->getMessage());
    }
}


    
    public function getAllgroupe() {
        $query = "SELECT * FROM groupe";

        return $this->executeSelectQuery($query);
    }
    public function getAllfiliere() {
        $query = "SELECT * FROM filiere";

        return $this->executeSelectQuery($query);
    }
    private function executeSelectQuery($query, $params = []) {
        $stmt = $this->connexion->prepare($query);
        
        if (!empty($params)) {
            $stmt->bind_param(...$params);
        }

        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>
