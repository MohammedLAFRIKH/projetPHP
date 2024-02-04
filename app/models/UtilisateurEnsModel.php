<?php

namespace App\Models;

use App\Database;

class UtilisateurEnsModel {
    private $connexion;

    const TABLE_NAME = 'utilisateur_ens';
    const COL_MATRICULE = 'matricule';
    const COL_EMAIL = 'email';
    const COL_MOT_PASSE = 'motPasse';
    const COL_CONFIRMATION_TOKEN = 'confirmation_token';
    const COL_RESET_TOKEN = 'reset_token';

    const COL_EMAIL_CONFIRMED = 'is_confirmed';

    public function __construct() {
        $database = Database::getInstance();
        $this->connexion = $database->getConnection();
    }

    public function getAllUsers() {
        $query = "SELECT * FROM " . self::TABLE_NAME;

        return $this->executeSelectQuery($query);
    }
    
    public function loginUser($cne, $password)
    {
        try {
            $user = $this->getUserByCNE($cne);
    

            $hashedPasswordFromDB = $user[self::COL_MOT_PASSE];

            if (password_verify($password, $hashedPasswordFromDB)) {
                return $user;
            } else {
                return false; // Incorrect password
            }
        } catch (\PDOException $e) {
            // Log or handle the database exception with more details
            throw new \Exception("An error occurred while trying to log in: " . $e->getMessage());
        }
    }

    // Inside the UtilisateurModel class

public function isUserDataComplete($matricule) {
    $query = "SELECT isUserDataComplete FROM " . self::TABLE_NAME . " WHERE " . self::COL_MATRICULE . " = ?";
    $stmt = $this->connexion->prepare($query);
    $stmt->bind_param('s', $matricule);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['isUserDataComplete'] == 1; // Returns true if user data is complete
    } else {
        return false; // User not found or data not complete
    }
}

    
    // Change private to protected or public
    public function getUserByCNE($cne)
    {
        $query = "SELECT * FROM " . self::TABLE_NAME . " WHERE " . self::COL_MATRICULE . " = ?";
        $stmt = $this->connexion->prepare($query);
        $stmt->bind_param('s', $cne);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }




    
    public function getUserByEmail($email) {
        try {
            $query = "SELECT " . self::COL_MATRICULE . " FROM " . self::TABLE_NAME . " WHERE " . self::COL_EMAIL . " = ?";
            $stmt = $this->connexion->prepare($query);
            $stmt->bind_param('s', $email);  // 's' represents a string, adjust if needed

            $stmt->execute();

            // Bind the result
            $stmt->bind_result($matricule);

            // Fetch the user data
            $stmt->fetch();

            $user = [
                self::COL_MATRICULE => $matricule,
            ];

            return $user;
        } catch (\Exception $e) {
            // Log the error or throw a custom exception
            echo "Error: " . $e->getMessage(); // Debugging: Print the error message
            return null;
        }
    }


    public function generatePasswordResetToken($email) {
        try {
            $token = bin2hex(random_bytes(32)); // Generate a random token
    
            $query = "UPDATE " . self::TABLE_NAME . " SET " . self::COL_CONFIRMATION_TOKEN . " = ? WHERE " . self::COL_EMAIL . " = ?";
            $stmt = $this->connexion->prepare($query);
            $stmt->bind_param('ss', $token, $email);
            $stmt->execute();
    
            return $token;
        } catch (\Exception $e) {
            // Log or handle the exception
            echo "Error: " . $e->getMessage();
            return null;
        }
    }
    
    public function getUserByResetToken($token) {
        try {
            $query = "SELECT * FROM " . self::TABLE_NAME . " WHERE " . self::COL_CONFIRMATION_TOKEN . " = ? LIMIT 1";
            $stmt = $this->connexion->prepare($query);
            $stmt->bind_param('s', $token);
            $stmt->execute();
    
            $result = $stmt->get_result();
    
            if ($result && $result->num_rows > 0) {
                return $result->fetch_assoc();
            } else {
                return null;
            }
        } catch (\Exception $e) {
            // Log or handle the exception
            echo "Error: " . $e->getMessage();
            return null;
        }
    }
    // UtilisateurModel.php

public function updatePasswordWithToken($token, $password) {
    try {
        $query = "UPDATE " . self::TABLE_NAME . " SET " . self::COL_MOT_PASSE . " = ? WHERE " . self::COL_CONFIRMATION_TOKEN . " = ?";
        $stmt = $this->connexion->prepare($query);
        

        $stmt->bind_param('ss', $password, $token);
        $stmt->execute();

        return $stmt->affected_rows > 0;
    } catch (\PDOException $e) {
        // Log or handle the database exception with more details
        throw new \Exception("An error occurred during password update: " . $e->getMessage());
    }
}

    public function clearResetToken($email) {
        try {
            $query = "UPDATE " . self::TABLE_NAME . " SET " . self::COL_CONFIRMATION_TOKEN . " = NULL WHERE " . self::COL_EMAIL . " = ?";
            $stmt = $this->connexion->prepare($query);
            $stmt->bind_param('s', $email);
            $stmt->execute();
    
            return $stmt->affected_rows > 0;
        } catch (\Exception $e) {
            // Log or handle the exception
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
    

    public function registerUser($userData)
    {
        $confirmationToken = $userData['confirmationToken'];
    
        $stmt = $this->connexion->prepare("INSERT INTO " . self::TABLE_NAME . " (" . self::COL_MATRICULE . ", " . self::COL_EMAIL . ", " . self::COL_MOT_PASSE . ", " . self::COL_CONFIRMATION_TOKEN . ") VALUES (?, ?, ?, ?)");
    
        $stmt->bind_param('ssss', $userData['CNE'], $userData['email'], $userData['password'], $confirmationToken);
    
        $result = $stmt->execute();
    
        if ($result) {
            return ['userId' => true, 'confirmationToken' => $confirmationToken];
        } else {
            return false; // Registration failed
        }
    }
    
    public function confirmUser($userId) {
        $query = "UPDATE " . self::TABLE_NAME . " SET is_confirmed = true WHERE " . self::COL_MATRICULE . " = ?";
        return $this->executeUpdateQuery($query, 'i', $userId);
    }

    public function verifyToken($CNE, $token) {
        $storedToken = $this->getTokenFromDatabase($CNE);

        return $token === $storedToken;
    }

    public function getTokenFromDatabase($CNE) {
        $query = "SELECT " . self::COL_CONFIRMATION_TOKEN . " FROM " . self::TABLE_NAME . " WHERE " . self::COL_MATRICULE . " = ?";
        $result = $this->executeSelectQuery($query, 's', $CNE);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row[self::COL_CONFIRMATION_TOKEN];
        }

        return null;
    }
    // Dans la classe UtilisateurModel

public function isUserConfirmed($CNE) {
    $query = "SELECT is_confirmed FROM " . self::TABLE_NAME . " WHERE " . self::COL_MATRICULE . " = ?";
    $stmt = $this->connexion->prepare($query);
    $stmt->bind_param('s', $CNE);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['is_confirmed'] == 1; // Retourne vrai si l'utilisateur est confirmé
    } else {
        return false; // L'utilisateur n'est pas trouvé ou n'est pas confirmé
    }
}


public function checkEmailExists($email) {
    $query = "SELECT COUNT(*) as count FROM " . self::TABLE_NAME . " WHERE " . self::COL_EMAIL . " = ?";
    
  
        $stmt = $this->connexion->prepare($query);

 

        $stmt->bind_param('s', $email);

        if ($stmt->execute()) {
            $result = $stmt->get_result();

            if (!$result) {
                throw new Exception("Error fetching result: " . $stmt->error);
            }

            $row = $result->fetch_assoc();

            if ($row['count'] > 0) {
                return true;
            } else {
                return TRUE;
            }
        } 
}



    private function executeSelectQuery($query, $bindType = null, ...$bindParams) {
        $stmt = $this->connexion->prepare($query);

        if ($bindType && $bindParams) {
            array_unshift($bindParams, $bindType);
            $this->bindParams($stmt, $bindParams);
        }

        $stmt->execute();

        $result = $stmt->get_result();

        return $result;
    }

    private function executeUpdateQuery($query, $bindType = null, ...$bindParams) {
        $stmt = $this->connexion->prepare($query);

        if ($bindType && $bindParams) {
            array_unshift($bindParams, $bindType);
            $this->bindParams($stmt, $bindParams);
        }

        return $stmt->execute();
    }

    private function bindParams($stmt, $params) {
        $bindParams = [];
        foreach ($params as $key => $value) {
            $bindParams[$key] = &$params[$key];
        }

        call_user_func_array([$stmt, 'bind_param'], $bindParams);
    }

}
