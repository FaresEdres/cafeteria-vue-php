<?php

require_once __DIR__ . '/../helper/errors.php';
require_once __DIR__ . "/../utils.php";
require_once __DIR__ . "/../db/connector.php";
require_once __DIR__ . "/../helper/CRUD.php";
class orderProductsModel
{
  private $db;

  public function __construct()
  {
    $this->db = new CRUD();
  }
  public function createOrderProductsTable()
  {
    $this->db->createTable("order_products", [
      "order_id" => "INT NOT NULL",
      "product_id" => "INT NOT NULL",
      "quantity" => "INT NOT NULL DEFAULT 1",
      "" => "PRIMARY KEY (order_id, product_id)",
      "" => "FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE",
      "" => "FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE"
    ]);
  }

  public function addOrderProduct($data)
  {
    try {
      error_log("Inserting order product: " . print_r($data, true)); // Log the data being inserted
      $result = $this->db->insert("order_products", $data);
      error_log("Insert result: " . print_r($result, true)); // Log the result of the insert operation

      if ($result) {
        return ["message" => "Order product inserted successfully"];
      } else {
        return ["error" => "Failed to insert order product"];
      }
    } catch (Exception $e) {
      error_log("Error inserting order product: " . $e->getMessage());
      return ["error" => $e->getMessage()];
    }
  }
  public function editOrderProduct($id, $data)
  {
    try {
      $updated = $this->db->update("order_products", $data, ["id" => $id]);
      if ($updated) {
        return ["message" => "Order product updated successfully"];
      } else {
        return ["error" => "Order product not found"];
      }
    } catch (Exception $e) {
      return ["error" => $e->getMessage()];
    }
  }
  public function deleteOrderProducts($orderId)
  {
    try {
      $deleted = $this->db->delete("order_products", ["order_id" => $orderId]);
      if ($deleted) {
        return ["message" => "Order products deleted successfully"];
      } else {
        return ["error" => "Order products not found"];
      }
    } catch (Exception $e) {
      return ["error" => $e->getMessage()];
    }
  }

}
