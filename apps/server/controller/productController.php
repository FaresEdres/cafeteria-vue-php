<?php

require_once __DIR__ . '/../helper/errors.php';
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
        return $this->productModel->displayAllProducts();
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

    public function updateProduct($postData, $files)
    {
        try {


            // Build data array to pass to model
            $dataToUpdate = [
                'id'           => $_POST['id'],
                'name'         => $_POST['name'],
                'description'  => $_POST['description'],
                'price'        => $_POST['price'],
                'category_id'  => $_POST['category_id']
            ];

            // Include image only if a file was actually uploaded
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $dataToUpdate['image'] = $_FILES['image'];
            }


            // Pass data to model
            $result = $this->productModel->updateProduct($dataToUpdate);

            if (!$result['success']) {
                throw new Exception($result['error']);
            }

            return $result;
        } catch (Exception $e) {
            return [
                'success' => false,
                'error'   => $e->getMessage()
            ];
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
