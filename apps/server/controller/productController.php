<?php
require_once __DIR__ . '/../helper/errors.php';
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, POST, PATCH, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

require_once __DIR__ . '/../model/productModel.php';
require_once __DIR__ . '/../validation/productValidation.php';
require_once __DIR__ . '/categoryController.php';

class ProductController
{
    private $productModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
    }


    public function getAllProducts($categoryId = null)
    {
        header('Content-Type: application/json');
        $categoryId = isset($_GET['category']) ? $_GET['category'] : null;
        $products = $this->productModel->displayAllProducts($categoryId);
        if (isset($products["error"])) {
            echo json_encode(["error" => $products["error"]]);
        } else {
            echo json_encode($products);
            exit;
        }
    }

    public function getProductById($id)
    {
        return $this->productModel->displayProductById($id);
    }

    public function addProduct($postData, $files)
    {
        try {
            $required = ['name', 'price', 'category_id'];
            foreach ($required as $field) {
                if (empty($postData[$field])) {
                    throw new Exception("Missing required field: $field");
                }
            }

            if (empty($files['image'])) {
                throw new Exception("Image is required");
            }

            $categoryController = new CategoryController();

            $categories = $categoryController->getAllCategories();
            $categoryIds = array_column($categories, 'id');
            if (!in_array($postData['category_id'], $categoryIds)) {
                throw new Exception("Invalid category ID.");
            }
            $result = $this->productModel->addProduct([
                'name' => $postData['name'],
                'description' => $postData['description'] ?? '',
                'price' => $postData['price'],
                'category_id' => $postData['category_id'],
                'image' => $files['image']
            ]);

            if (!$result['success']) {
                throw new Exception($result['error']);
            }

            return $result;
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }


    public function updateProduct()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception("Invalid request method.");
            }

            if (empty($_POST['id']) || empty($_POST['name']) || empty($_POST['price']) || empty($_POST['category_id'])) {
                throw new Exception("Missing required fields.");
            }

            $data = [
                'id' => $_POST['id'],
                'name' => $_POST['name'],
                'description' => isset($_POST['description']) ? $_POST['description'] : null,
                'price' => $_POST['price'],
                'category_id' => $_POST['category_id'],
            ];

            if (isset($_FILES['image']) && !empty($_FILES['image']['tmp_name'])) {
                $file = $_FILES['image'];

                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                $maxSize = 2 * 1024 * 1024; // 2MB

                if (!in_array($file['type'], $allowedTypes)) {
                    throw new Exception("Only JPG, PNG, and GIF images are allowed.");
                }

                if ($file['size'] > $maxSize) {
                    throw new Exception("Image size exceeds 2MB limit.");
                }

                $data['image'] = $file;
            }

            $result = $this->productModel->updateProduct($data);

            if ($result['success']) {
                echo json_encode(['status' => 'success', 'message' => $result['message']]);
            } else {
                echo json_encode(['status' => 'error', 'error' => $result['error']]);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'error' => $e->getMessage()]);
        }
    }
    // private function validateProductData($data, $isUpdate = false)
    // {
    //     $errors = [];

    //     // Common validations for both add and update
    //     if (empty($data['name'])) {
    //         $errors['name'] = 'Product name is required';
    //     } elseif (strlen($data['name']) > 255) {
    //         $errors['name'] = 'Product name too long';
    //     }

    //     if (isset($data['price']) && !is_numeric($data['price'])) {
    //         $errors['price'] = 'Price must be a number';
    //     }

    //     // Update-specific validations
    //     if ($isUpdate) {
    //         if (isset($data['image']) && !empty($data['image'])) {
    //             // Additional image validations if needed
    //         }
    //     }

    //     return $errors;
    // }
    public function deleteProduct($id)
    {
        return $this->productModel->deleteProduct($id);
    }
}
