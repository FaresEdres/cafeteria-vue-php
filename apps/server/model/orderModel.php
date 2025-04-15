<?php

require_once __DIR__ . '/../helper/errors.php';
require_once __DIR__ . "/../utils.php";
require_once __DIR__ . "/../db/connector.php";
require_once __DIR__ . "/../helper/CRUD.php";
class OrderModel
{
    private $db;

    public function __construct()
    {
        $this->db = new CRUD();
    }
    public function createOrderTable()
    {
        $this->db->createTable("orders", [
            "id" => "INT AUTO_INCREMENT PRIMARY KEY",
            "total_price" => "DECIMAL(10, 2) NOT NULL DEFAULT 0",
            "user_id" => "INT NOT NULL, FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE",
            "comment" => "TEXT DEFAULT NULL",
            "status" => "ENUM('processing', 'completed', 'cancelled') NOT NULL DEFAULT 'processing'",
            "created_at" => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP"
        ]);
    }
    public function addOrder($data)
    {
        try {
            $insertedId = $this->db->insert("orders", $data);
            if ($insertedId) {
                return ["id" => $insertedId];
            } else {
                return ["error" => "Failed to insert order"];
            }
        } catch (Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }
    public function editOrder($data, $id)
    {
        try {
            $updated = $this->db->update("orders", $data, ["id" => $id]);
            if ($updated) {
                return ["message" => "Order updated successfully"];
            } else {
                return ["error" => "Order not found"];
            }
        } catch (Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }
    public function deleteOrder($id)
    {
        try {
            $deleted = $this->db->delete("orders", ["id" => $id]);
            if ($deleted) {
                return ["message" => "Order deleted successfully"];
            } else {
                return ["error" => "Order not found"];
            }
        } catch (Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }
}
