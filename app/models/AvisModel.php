<?php
namespace App\Models;

use App\Database;

class AvisModel {
    private $connexion;

    public function __construct() {
        $database = Database::getInstance();
        $this->connexion = $database->getConnection();
    }

    public function getAvisByGroupes($group) {
        try {
            // Check if $group is a valid number
            if (!is_numeric($group)) {
                return false;
            }
    
            // Prepare the SQL query with the FIND_IN_SET condition
            $sql = "SELECT * FROM avis WHERE FIND_IN_SET(?, groupe)";
    
            $stmt = $this->connexion->prepare($sql);
    
            // Bind parameter
            $stmt->bind_param("s", $group);
    
            // Execute the query
            $stmt->execute();
    
            // Get the result set
            $result = $stmt->get_result();
    
            // Fetch all avis
            $avis = $result->fetch_all(MYSQLI_ASSOC);
    
            // Close the database connection
            $this->connexion = null;
    
            // Check if avis were found
            if ($avis) {
                return $avis;
            } else {
                return false;
            }
        } catch (\PDOException $e) {
            // Handle database connection error
            // You may want to log the error or display a user-friendly message
            die("Database connection error: " . $e->getMessage());
        }
    }
    

 
    public function getAllAvis() {
        // Perform a join to get nom and prenom
        $query = $this->connexion->query('
            SELECT avis.*, utilisateur_ens.nom, utilisateur_ens.prenom
            FROM avis
            JOIN utilisateur_ens ON avis.utilisateur_ens = utilisateur_ens.matricule
        ');
    
        $results = [];
        while ($row = $query->fetch_assoc()) {
            // If 'groupe' is a comma-separated string, convert it to an array
            $row['groupe'] = explode(',', $row['groupe']);
            $results[] = $row;
        }
    
        return $results;
    }


    public function deleteSelectedAvis(array $selectedAvis)
    {
       // Ensure that $selectedAvis is not empty to avoid SQL errors
       if (!empty($selectedAvis)) {
           // Use placeholders for the IDs in the IN clause
           $placeholders = implode(',', array_fill(0, count($selectedAvis), '?'));

           $sql = "DELETE FROM avis WHERE id_avis IN ($placeholders)";
           $stmt = $this->connexion->prepare($sql);

           // Bind parameters dynamically based on the number of selected reviews
           $stmt->bind_param(str_repeat('s', count($selectedAvis)), ...$selectedAvis);

           if ($stmt->execute()) {
               return true; // Success
           } else {
               return false; // Error
           }
       }

       return false; // No reviews selected
   }

   // In AvisModel.php

    public function getAvisDetails($avisId) {
        // Perform a query to get the details of the specific avis based on avisId
        $stmt = $this->connexion->prepare('
            SELECT *
            FROM avis
            WHERE id_avis = ?
        ');

        $stmt->bind_param("i", $avisId);
        $stmt->execute();

        $result = $stmt->get_result();

        // Check if any rows are returned
        if ($result->num_rows > 0) {
            // Fetch the avis details as an associative array
            return $result->fetch_assoc();
        } else {
            // Return false if avis details are not found
            return false;
        }
    }


   public function getFilePathByReviewId($reviewId) {
    $query = "SELECT piece_jointe FROM avis WHERE id_avis = ?";
    $stmt = $this->connexion->prepare($query);
    $stmt->bind_param("s", $reviewId);

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['piece_jointe'];
    }

    return null; // or handle accordingly if no record is found
}
public function updateAvis($avisId, $modifiedObjet,$selectedGroups , $modifiedContenu ,$modified_pieceJointe){
    // Prepare and execute the SQL query to update the avis in the database

    $stmt = $this->connexion->prepare('
        UPDATE avis
        SET objet = ?,
            contenu = ?,
            piece_jointe = ?,
            groupe = ?

        WHERE id_avis = ?
    ');

    $stmt->bind_param("sssi", $modifiedObjet, $modifiedContenu,$selectedGroups ,$modified_pieceJointe, $avisId);

    if ($stmt->execute()) {
        return true; // Success
    } else {
        return false; // Error
    }
}
    
    public function insertAvis($utilisateurEns, $groupe, $objet, $contenu, $pieceJointe ) {
        $selectedGroupsString = implode(',', $groupe);

        $sql = "INSERT INTO avis (utilisateur_ens, groupe, objet, contenu, piece_jointe)
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $this->connexion->prepare($sql);
        $stmt->bind_param("sssss", $utilisateurEns, $selectedGroupsString, $objet, $contenu, $pieceJointe);
    
        if ($stmt->execute()) {
            return true; // Success
        } else {
            return false; // Error
        }
    }
    public function insertAvisfiliere($utilisateurEns, $filiere, $objet, $contenu, $pieceJointe ) {
        $selectedfiliereString = implode(',', $filiere);

        $sql = "INSERT INTO avis (utilisateur_ens, filiere, objet, contenu, piece_jointe)
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $this->connexion->prepare($sql);
        $stmt->bind_param("sssss", $utilisateurEns, $selectedfiliereString, $objet, $contenu, $pieceJointe);
    
        if ($stmt->execute()) {
            return true; // Success
        } else {
            return false; // Error
        }
    }
    public function insertAvisEDT($utilisateurEns, $objet, $contenu, $pieceJointe ) {

        $sql = "INSERT INTO avis (utilisateur_ens, EDTs, objet, contenu, piece_jointe)
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $this->connexion->prepare($sql);
        $EDTs=1;
        $stmt->bind_param("sssss", $utilisateurEns,$EDTs , $objet, $contenu, $pieceJointe);
    
        if ($stmt->execute()) {
            return true; // Success
        } else {
            return false; // Error
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
