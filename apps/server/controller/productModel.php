<?php

require_once __DIR__ . "/../utils.php";
require_once __DIR__ . "/../model/connector.php";
require_once __DIR__ . "/../helper/CRUD.php";

$db = new CRUD();

$db->createTable("categories", [
    "id" => "INT AUTO_INCREMENT PRIMARY KEY",
    "name" => "VARCHAR(255) UNIQUE NOT NULL",
    "created_at" => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP"
]);


$db->createTable('products', [
    "id" => "INT AUTO_INCREMENT PRIMARY KEY",
    "name" => "VARCHAR(255) NOT NULL",
    "image" => "VARCHAR(255) NOT NULL",
    "description" => "TEXT",
    "price" => "DECIMAL(10,2) NOT NULL",
    "category_id" => "INT NOT NULL",
    "created_at" => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
    "" => "FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE"
]);

// function insertProduct ($data){
//     global $db;
//     $db->insert("products", $data);
// }
