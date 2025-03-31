<?php

require_once __DIR__ .'/../helper/errors.php';
require_once __DIR__ . "/../utils.php";
require_once __DIR__ . "/../model/connector.php";
require_once __DIR__ . "/../helper/CRUD.php";
class CategoryModel
{
  private $db;

  public function __construct()
  {
    $this->db = new CRUD();
  }
  public function createCategoryTable()
  {
    $this->db->createTable("categories", [
      "id" => "INT AUTO_INCREMENT PRIMARY KEY",
      "name" => "VARCHAR(255) UNIQUE NOT NULL",
      "created_at" => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP"
    ]);
  }
  public function addCategory($data)
  {
    try {
      $insertedId = $this->db->insert("categories", $data);

      if ($insertedId) {
        return ["id" => $insertedId];
      } else {
        return ["error" => "Failed to insert category"];
      }
    } catch (Exception $e) {
      return ["error" => $e->getMessage()];
    }
  }
  public function deleteCategory($id)
  {
    $this->db->delete("categories", ["id" => $id]);
  }
}
