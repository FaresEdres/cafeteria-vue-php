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
            $result = $this->db->insert("order_products", $data);
            var_dump($result);
            if ($result) {
                return ["message" => "Order product inserted successfully"];
            } else {
                return ["error" => "Failed to insert order product"];
            }
        } catch (Exception $e) {
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
    //   public function addCategory($data)
    //   {
    //     try {
    //       $insertedId = $this->db->insert("categories", $data);

    //       if ($insertedId) {
    //         return ["id" => $insertedId];
    //       } else {
    //         return ["error" => "Failed to insert category"];
    //       }
    //     } catch (Exception $e) {
    //       return ["error" => $e->getMessage()];
    //     }
    //   }
    //   public function deleteCategory($id)
    //   {
    //     try {
    //       $deleted = $this->db->delete("categories", ["id" => $id]);
    //       if ($deleted) {
    //         return ["message" => "category deleted successfully"];
    //       } else {
    //         return ["error" => "category not found"];
    //       }
    //     } catch (Exception $e) {
    //       return ["error" => $e->getMessage()];
    //     }
    //   }
    //   public function displayAllCatergories()
    //   {
    //     try {
    //       return $this->db->select("categories", "*");
    //     } catch (Exception $e) {
    //       return ["error" => $e->getMessage()];
    //     }

    //   }
}
