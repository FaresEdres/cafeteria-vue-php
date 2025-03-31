<?php

require_once __DIR__ .'/../helper/errors.php';
require_once __DIR__ . "/../utils.php";
require_once __DIR__ . "/../model/connector.php";
require_once __DIR__ . "/../helper/CRUD.php";

class ProductModel {
    private $db;

    public function __construct() {
        $this->db = new CRUD();
    }
    public function createProductTable(){
        $this->db->createTable('products', [
            "id" => "INT AUTO_INCREMENT PRIMARY KEY",
            "name" => "VARCHAR(255) NOT NULL",
            "image" => "VARCHAR(255) NOT NULL",
            "description" => "TEXT",
            "price" => "DECIMAL(10,2) NOT NULL",
            "category_id" => "INT NOT NULL, FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE",
            "created_at" => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
        ]);
    }
    public function addProduct($data) {
        try {
            $insertedId = $this->db->insert("products", $data);

            if ($insertedId) {
                return ["id" => $insertedId];
            } else {
                return ["error" => "Failed to insert product"];
            }
        } catch (Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }
    public function deleteProduct($id) {
        try {
            $deleted = $this->db->delete("products", ["id" => $id]);
            if ($deleted) {
                return ["message" => "Product deleted successfully"];
            } else {
                return ["error" => "Product not found"];
            }
        } catch (Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }

    public function displayAllProducts() {
        try{
            return $this->db->select("products","*");
        }
        catch (Exception $e) {
            return ["error"=> $e->getMessage()];
        }
    }
    public function displayProductById($id) {
        try {
            $result = $this->db->select("products", "*", ["id" => $id]);
            return $result ? $result[0] : ["error" => "Product not found"];
        } catch (Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }
    public function updateProduct($id, $data) {
        try {
            $result = $this->db->update("products", $data, ["id"=> $id]);
            if ($result) {
                return ["message"=> "Product updeated successfully"];
            }else {
                return ["error" => "Product not found"];
            }
        } catch (Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }
}
?>
