<?php
require_once __DIR__ .'/../helper/errors.php';
require_once __DIR__ . "/../utils.php";
require_once __DIR__ . "/../db/connector.php";
require_once __DIR__ . "/../helper/CRUD.php";

class UserModel {
    private $db;

    public function __construct() {
        $this->db = new CRUD();
    }

    public function createUserTable() {
        $this->db->createTable('users', [
            "id" => "INT AUTO_INCREMENT PRIMARY KEY",
            "firstname" => "VARCHAR(100) NOT NULL",
            "lastname" => "VARCHAR(100) NOT NULL",
            "email" => "VARCHAR(255) NOT NULL UNIQUE",
            "password" => "VARCHAR(255) NOT NULL",
            "role" => "ENUM('admin','user') DEFAULT 'user'",
            "phoneNumber" => "VARCHAR(20)",
            "address" => "TEXT",
            "image" => "VARCHAR(255)",
            "gender" => "ENUM('male','female','other')",
            "created_at" => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP"
        ]);
    }

    public function addUser($data) {
        try {
            $insertedId = $this->db->insert("users", $data);
            return $insertedId ? ["id" => $insertedId] : ["error" => "Failed to add user"];
        } catch (Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }

    public function displayAllUsers() {
        try {
            return $this->db->select("users", "*");
        } catch (Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }

    public function displayUserById($id) {
        try {
            $result = $this->db->select("users", "*", ["id" => $id]);
            return $result ? $result[0] : ["error" => "User not found"];
        } catch (Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }

    public function updateUser($id, $data) {
        try {
            $result = $this->db->update("users", $data, ["id" => $id]);
            return $result ? ["message" => "User updated"] : ["error" => "User not found"];
        } catch (Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }

    public function deleteUser($id) {
        try {
            $deleted = $this->db->delete("users", ["id" => $id]);
            return $deleted ? ["message" => "User deleted"] : ["error" => "User not found"];
        } catch (Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }

    public function authenticateUser($email, $password) {
        try {
            $user = $this->db->select("users", "*", ["email" => $email]);
            if (!$user || !password_verify($password, $user[0]['password'])) {
                return ["error" => "Invalid credentials"];
            }
            
            // Remove password before returning
            unset($user[0]['password']);
            return $user[0];
        } catch (Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }
}