<?php
require_once __DIR__ . '/../helper/errors.php';
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, POST, PATCH, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

require_once __DIR__ . '/../model/productModel.php';
require_once __DIR__ . '/../model/orderModel.php';
require_once __DIR__ . '/../model/orderProuductsModel.php';
require_once __DIR__ . '/../validation/productValidation.php';
require_once __DIR__ . '/../helper/CRUD.php';

class OrdersController
{
    private $ordersModel;
    private $orderProductsModel;
    private $db;

    public function __construct()
    {
        $this->ordersModel = new OrderModel();
        $this->orderProductsModel = new orderProductsModel(); // Remove duplicate line
        $this->db = new CRUD();
    }

    public function addOrder()
{
    $inputData = json_decode(file_get_contents("php://input"), true);
    if (!$inputData) {
        echo json_encode(["error" => "Invalid JSON input"]);
        return;
    }

    $orderData = [
        "user_id" => $inputData['user_id'],
        "comment" => $inputData['comment']
    ];

    $result = $this->ordersModel->addOrder($orderData);

    if (isset($result["error"])) {
        echo json_encode($result);
        return;
    }

    $orderId = $result["id"];
    $errors = [];

    foreach ($inputData['products'] as $product) {
        $orderProductData = [
            "order_id" => $orderId,
            "product_id" => $product['id'],
            "quantity" => $product['quantity']
        ];

        error_log("Order Product Data: " . print_r($orderProductData, true)); // Log the data

        $orderProductResult = $this->orderProductsModel->addOrderProduct($orderProductData);

        if (isset($orderProductResult["error"])) {
            $errors[] = $orderProductResult["error"];
        }
    }

    if (!empty($errors)) {
        echo json_encode(["errors" => $errors]);
        return;
    }

    echo json_encode(["success" => true, "order_id" => $orderId]);
}

    public function editOrder($id)
    {
        $inputData = json_decode(file_get_contents("php://input"), true);
        if (!$inputData) {
            header('Content-Type: application/json');
            echo json_encode(["error" => "Invalid JSON input"]);
            return;
        }

        // Only update comment if it's provided
        if (isset($inputData['comment'])) {
            $orderData = [
                "comment" => $inputData['comment']
            ];

            $result = $this->ordersModel->editOrder($orderData, $id);

            if (isset($result["error"])) {
                header('Content-Type: application/json');
                echo json_encode($result);
                return;
            }
        }
        if (isset($inputData['status'])) {
            $orderData = [
                "status" => $inputData['status']
            ];

            $result = $this->ordersModel->editOrder($orderData, $id);

            if (isset($result["error"])) {
                header('Content-Type: application/json');
                echo json_encode($result);
                return;
            }
        }
        if (isset($inputData['products']) && is_array($inputData['products'])) {
            $result = $this->orderProductsModel->deleteOrderProducts($id);
            if (isset($result["error"])) {
                header('Content-Type: application/json');
                echo json_encode($result);
                return;
            }

            $errors = [];
            foreach ($inputData['products'] as $product) {
                $orderProductResult = $this->orderProductsModel->addOrderProduct([
                    "order_id" => $id,
                    "product_id" => $product['id'],
                    "quantity" => $product['quantity']
                ]);

                if (isset($orderProductResult["error"])) {
                    $errors[] = $orderProductResult["error"];
                }
            }


            if (!empty($errors)) {
                header('Content-Type: application/json');
                echo json_encode(["errors" => $errors]);
                return;
            }
        }

        header('Content-Type: application/json');
        echo json_encode([
            "success" => true,
            "message" => "Order updated successfully"
        ]);
        exit;
    }
    public function getAllOrders($userId)
    {
        $result = $this->db->select("order_details_view", "*", [
            "user_id" => $userId
        ]);

        if (!$result || !is_array($result)) {
            header('Content-Type: application/json');
            echo json_encode(["error" => "Query failed or invalid result"]);
            exit;
        }

        $orders = [];

        foreach ($result as $row) {
            $orderId = $row['order_id'];
            $productId = $row['product_id'];

        if (!isset($orders[$orderId])) {
            $orders[$orderId] = [
                'user_name' => $row['user_name'],
                'user_id' => $row['user_id'],
                'status' => $row['status'],
                'order_id' => $orderId,
                'total_price' => $row['total_price'],
                'products' => []
            ];
        }

            $productExists = false;

            foreach ($orders[$orderId]['products'] as &$product) {
                if ($product['product_id'] === $productId) {
                    $product['quantity'] += $row['quantity']; // Sum quantity
                    $productExists = true;
                    break;
                }
            }

            if (!$productExists) {
                $orders[$orderId]['products'][] = [
                    'product_id' => $productId,
                    'name' => $row['product_name'],
                    'image' => $row['image'],
                    'description' => $row['description'],
                    'price' => $row['price'],
                    'quantity' => $row['quantity']
                ];
            }
        }

        echo json_encode(array_values($orders), JSON_PRETTY_PRINT);
        exit;
    }
    public function getAllOrdersForAdmin()
    {
        $result = $this->db->select("order_details_view");

        if (!$result || !is_array($result)) {
            header('Content-Type: application/json');
            echo json_encode(["error" => "Query failed or invalid result"]);
            exit;
        }

        $orders = [];

        foreach ($result as $row) {
            $orderId = $row['order_id'];
            $productId = $row['product_id'];

            if (!isset($orders[$orderId])) {
                $orders[$orderId] = [
                    'user_name' => $row['user_name'],
                    'order_id' => $orderId,
                    'total_price' => $row['total_price'],
                    'products' => []
                ];
            }

            $productExists = false;

            foreach ($orders[$orderId]['products'] as &$product) {
                if ($product['product_id'] === $productId) {
                    $product['quantity'] += $row['quantity']; // Sum quantity
                    $productExists = true;
                    break;
                }
            }

            if (!$productExists) {
                $orders[$orderId]['products'][] = [
                    'product_id' => $productId,
                    'name' => $row['product_name'],
                    'image' => $row['image'],
                    'description' => $row['description'],
                    'price' => $row['price'],
                    'quantity' => $row['quantity']
                ];
            }
        }

        echo json_encode(array_values($orders), JSON_PRETTY_PRINT);
        exit;
    }
}