<?php

require_once __DIR__ . '/../helper/errors.php';
require_once __DIR__ . "/../utils.php";
require_once __DIR__ . "/../db/connector.php";
require_once __DIR__ . "/../helper/CRUD.php";

class ProductModel
{
    private $db;

    public function __construct()
    {
        $this->db = new CRUD();
    }

    public function createProductTable()
    {
        $this->db->createTable('products', [
            "id" => "INT AUTO_INCREMENT PRIMARY KEY",
            "name" => "VARCHAR(255) NOT NULL",
            "image" => "VARCHAR(255) NOT NULL",
            "description" => "TEXT",
            "price" => "DECIMAL(10,2) NOT NULL",
            "category_id" => "INT NOT NULL, FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE",
            "created_at" => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
        ]);
    }

    public function addProduct($data)
    {
        try {

            // File upload handling
            $uploadDir = __DIR__ . '/../public/uploads/'; // Absolute path
            $allowedTypes = ['jpg', 'jpeg', 'png'];
            $maxFileSize = 2 * 1024 * 1024; // 2MB

            // Ensure upload directory exists
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0777, true)) {
                    throw new Exception("Failed to create upload directory");
                }
            }

            // Handle image upload
            if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("Image is required and must be uploaded successfully");
            }

            $file = $_FILES['image'];
            $tmpName = $file['tmp_name'];

            // Verify temp file exists
            if (!file_exists($tmpName)) {
                throw new Exception("Temporary upload file not found");
            }

            // Validate file type (extension and MIME)
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowedTypes)) {
                throw new Exception("Only JPG/JPEG/PNG files are allowed");
            }

            $mimeType = mime_content_type($tmpName);
            $validMimeTypes = ['image/jpeg', 'image/png'];
            if (!in_array($mimeType, $validMimeTypes)) {
                throw new Exception("Invalid image type. Only JPG/JPEG/PNG are allowed");
            }

            // Validate file size
            if ($file['size'] > $maxFileSize) {
                throw new Exception("File size exceeds 2MB limit");
            }

            // Generate a unique filename
            $newFilename = uniqid('product_') . '.' . $ext;
            $targetPath = $uploadDir . $newFilename;

            // Move the uploaded file
            if (!move_uploaded_file($tmpName, $targetPath)) {
                throw new Exception("Failed to move uploaded file");
            }

            // Prepare data for database
            $productData = [
                'name' => $data['name'],
                'description' => $data['description'],
                'price' => (float)$data['price'],
                'category_id' => (int)$data['category_id'],
                'image' => $newFilename // Relative path for DB
            ];


            // Validate required fields
            $requiredFields = ['name', 'description', 'price', 'category_id'];
            foreach ($requiredFields as $field) {
                if (empty($productData[$field])) {
                    throw new Exception("Missing required field: $field");
                }
            }

            // Validate price
            if ($productData['price'] <= 0) {
                throw new Exception("Price must be a positive number");
            }


            // Insert into database
            $insertedId = $this->db->insert("products", $productData);

            if (!$insertedId) {
                // Clean up file if insert failed
                if (file_exists($targetPath)) {
                    unlink($targetPath);
                }
                throw new Exception("Failed to insert product into database");
            }

            return [
                'success' => true,
                'id' => $insertedId,
                'message' => 'Product added successfully',
                'image_path' => $productData['image']
            ];
        } catch (Exception $e) {
            // Clean up any uploaded file if error occurred
            if (isset($targetPath) && file_exists($targetPath)) {
                unlink($targetPath);
            }

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function deleteProduct($id)
    {
        try {
            // First, get product to delete its image
            $product = $this->db->select("products", ["image"], ["id" => $id]);



            // Check if product was found
            if (empty($product)) {
                return ['success' => false, 'error' => 'Product not found'];
            }

            // Delete the product record
            $deleted = $this->db->delete("products", ["id" => $id]);

            // If product is deleted and there is an image, remove it
            if ($deleted && !empty($product[0]['image'])) {
                $imagePath = __DIR__ . '/../public/uploads/' . $product[0]['image'];

                // Debug: Check if the image path exists
                if (file_exists($imagePath)) {
                    // Remove old image
                    unlink($imagePath);
                } else {
                    // Handle the case where the image doesn't exist
                    return ['success' => false, 'error' => $imagePath];
                }
            }

            return $deleted
                ? ['success' => true, 'message' => 'Product deleted successfully']
                : ['success' => false, 'error' => 'Product not found'];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }


    public function displayAllProducts()
    {
        try {
            $products = $this->db->select("products", "*");
            return ['success' => true, 'data' => $products];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    public function displayPaginateProducts($page)
    {
        $limit = 5;
        $offset = ($page - 1) * $limit;

        try {
            // Fetch paginated products
            $products = $this->db->select("products", "*", [], "", $limit, $offset);

            // Get total count of products
            $totalCount = $this->db->countAll("products");
            $totalCount = $totalCount[0]['total'];
            // Calculate total pages
            $totalPages = ceil($totalCount / $limit);

            return [
                'success' => true,
                'data' => $products,
                'pagination' => [
                    'currentPage' => $page,
                    'limit' => $limit,
                    'totalItems' => $totalCount,
                    'totalPages' => $totalPages
                ]
            ];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }




    public function displayProductById($id)
    {
        try {

            $product = $this->db->select("products", "*", ["id" => $id]);
            return $product
                ? ['success' => true, 'data' => $product[0]]
                : ['success' => false, 'error' => 'Product not found'];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function updateProduct($data)
    {
        try {
            $id = $data['id'];
            $updateData = [];

            // Retrieve old image BEFORE update
            $oldProduct = $this->db->select("products", ["image"], ["id" => $id]);

            // Handle file upload
            if (isset($_FILES['image']) && !empty($_FILES['image']['tmp_name'])) {
                $file = $_FILES['image'];

                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                $maxSize = 2 * 1024 * 1024; // 2MB

                // Validate file type and size
                if (!in_array($file['type'], $allowedTypes)) {
                    throw new Exception("Only JPG, PNG, and GIF images are allowed");
                }
                if ($file['size'] > $maxSize) {
                    throw new Exception("Image size exceeds 2MB limit");
                }

                $uploadDir = __DIR__ . '/../public/uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $newFilename = uniqid('product_') . '.' . $ext;
                $targetPath = $uploadDir . $newFilename;

                // Move uploaded file
                if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
                    throw new Exception("Failed to upload image");
                }

                $updateData['image'] = $newFilename;
                $newImagePath = $targetPath; // Save path for later deletion if necessary
            }

            // Merge other fields
            foreach (['name', 'description', 'price', 'category_id'] as $field) {
                if (isset($data[$field])) {
                    $updateData[$field] = $data[$field];
                }
            }

            // Update product in database
            $updated = $this->db->update("products", $updateData, ["id" => $id]);

            // If update was successful and we have a new image, delete the old image
            if ($updated && isset($updateData['image']) && !empty($oldProduct[0]['image'])) {
                $oldImagePath = __DIR__ . '/../public/uploads/' . $oldProduct[0]['image'];
                echo $oldImagePath;
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            return $updated
                ? ['success' => true, 'message' => 'Product updated successfully']
                : ['success' => false, 'error' => 'Product not found'];
        } catch (Exception $e) {
            // Cleanup failed upload
            if (isset($newImagePath) && file_exists($newImagePath)) {
                unlink($newImagePath);
            }
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
