
<?php

require_once __DIR__.  "/../utils.php";
require_once __DIR__.  "/../model/connector.php";

function createTable(){
    try{
        $conn= SQLConnector::connectDatabase();
        $create_query= "CREATE TABLE IF NOT EXISTS `users` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(50) NOT NULL,
            `email` VARCHAR(50) UNIQUE NOT NULL,
            `password` VARCHAR(255) NOT NULL,
            `room` VARCHAR(50),
            `ext` VARCHAR(10),
            `image` VARCHAR(255)
        );";

        $stmt= $conn->prepare($create_query);
        $stmt->execute();
        
    }
    catch(PDOException $e){
        displayError($e->getMessage());
    }
    finally{
        $conn = null;
    }
}
function insertUser($data) {
    try {
        $conn = SQLConnector::connectDatabase();
        if ($conn) {
            $check_query = "SELECT COUNT(*) FROM `users` WHERE `email` = ?";
            $stmt = $conn->prepare($check_query);
            $stmt->execute([$data['email']]);
            $count = $stmt->fetchColumn();
          

            if ($count > 1) {
                displayError("Email already registered!");
return false;                
            }

            $insert_query = "INSERT INTO `users` (name, email, password, room, ext, profilePic)
                             VALUES (?, ?, ?, ?, ?, ?);";
            $stmt = $conn->prepare($insert_query);
            $success = $stmt->execute([
                $data['name'],
                $data['email'],
                $data['password'], 
                $data['room'],
                $data['ext'],
                $data['profilePic'],
            ]);

            if ($success) {
                return true;
            } else {
                displayError("Failed to insert user.");
                return false;
            }
        }
    } catch (PDOException $e) {
        displayError($e->getMessage());
        return false;
    }
    finally{
         $conn = null;
    }
}


function getAllUsers() {
    try {
        $conn = SQLConnector::connectDatabase();

        $query = "SELECT id, name, email, room, ext, profilePic FROM users";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all users as an associative array
    } catch (PDOException $e) {
        error_log("Database Error: " . $e->getMessage()); // Log the error
        return false;
    }
      finally{
         $conn = null;
    }
}

/**
 * Get a single user by ID
 */
function getUserById($id) {
    $conn = SQLConnector::connectDatabase();
    if (!$conn) return false; // Return false if the connection failed

    try {
        $stmt = $conn->prepare("SELECT id, name, email, room, ext, profilePic FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC); 
    } catch (PDOException $e) {
        error_log("Error fetching user: " . $e->getMessage());
        return false;
    }
      finally{
         $conn = null;
    }
}
