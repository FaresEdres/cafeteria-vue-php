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
            "updated_at"=> "TIMESTAMP DEFAULT CURRENT_TIMESTAMP"
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
}
?>
