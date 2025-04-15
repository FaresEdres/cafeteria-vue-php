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
            "role" => "ENUM('admin','user') NOT NULL DEFAULT 'user'",
            "room" => "VARCHAR(100) NOT NULL",
            "ext" => "VARCHAR(10) NOT NULL",
            "image" => "VARCHAR(255) NOT NULL",
            "created_at" => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP"
        ]);
    }

    public function addUser($data) {
        try {
            $insertedId = $this->db->insert("users", $data);
            if ($insertedId) {
                return ["id" => $insertedId]; // Return the inserted ID
            }
            return ["error" => "Failed to add user"]; // Return an error if the insert failed
        } catch (Exception $e) {
            return ["error" => $e->getMessage()]; // Return the exception message on error
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
}