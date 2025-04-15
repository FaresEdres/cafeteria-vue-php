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


    public function getAllProducts()
    {
        header('Content-Type: application/json');
        $products = $this->productModel->displayAllProducts();
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
            // Validate required fields
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
            // Process with model
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
            // Ensure it's a POST request
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception("Invalid request method.");
            }




            // Collect the product data from the form
            $data = [
                'id' => $_POST['id'],
                'name' => $_POST['name'],
                'description' => isset($_POST['description']) ? $_POST['description'] : null,
                'price' => $_POST['price'],
                'category_id' => $_POST['category_id'],
            ];


            // Handle image file upload if provided
            if (isset($_FILES['image']) && !empty($_FILES['image']['tmp_name'])) {
                $file = $_FILES['image'];

                // Validate the file type and size in the controller before passing it to the model
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                $maxSize = 2 * 1024 * 1024; // 2MB

                if (!in_array($file['type'], $allowedTypes)) {
                    throw new Exception("Only JPG, PNG, and GIF images are allowed.");
                }

                if ($file['size'] > $maxSize) {
                    throw new Exception("Image size exceeds 2MB limit.");
                }

                // Add the image to the data array for processing
                $data['image'] = $file;
            }

            // Call the model to update the product
            $result = $this->productModel->updateProduct($data);

            // Return success or failure message based on the result
            if ($result['success']) {
                echo json_encode(['status' => 'success', 'message' => $result['message']]);
            } else {
                echo json_encode(['status' => 'error', 'error' => $result['error']]);
            }
        } catch (Exception $e) {
            // Handle any exceptions that occur during the update process
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

    public function getProducts()
    {
        // Get the 'page' and 'limit' query parameters from the URL
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Default to page 1 if not set
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10; // Default to 10 items per page if not set

        // If both 'page' and 'limit' are provided, use the pagination method
        if ($page > 0 && $limit > 0) {
            return $this->productModel->displayPaginateProducts($page, $limit);
        }

        // If no pagination parameters are provided, return all products
        return $this->productModel->displayAllProducts();
    }
}
