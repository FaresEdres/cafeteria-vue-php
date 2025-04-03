<?php
require_once __DIR__ . '/../helper/errors.php';
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, POST, PATCH, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

require_once __DIR__ . '/../model/productModel.php';
require_once __DIR__ . '/../validation/productValidation.php';

class ProductController {
  private $productModel;

  public function __construct() {
      $this->productModel = new ProductModel();
  }

  public function getAllProducts() {
    header('Content-Type: application/json');
    $products = $this->productModel->displayAllProducts();
    if (isset($products["error"])) {
        echo json_encode(["error" => $products["error"]]);
    } else {
        echo json_encode($products);
        exit;
    }
  }

  public function getProductById($id) {
      return $this->productModel->displayProductById($id);
  }

  public function addProduct() {
      $inputData = json_decode(file_get_contents("php://input"), true);
      if (!$inputData) return ["error" => "Invalid JSON input"];

      $validationErrors = validateProductData($inputData);
      if (!empty($validationErrors)) return ["errors" => $validationErrors];

      return $this->productModel->addProduct($inputData);
  }

  public function updateProduct($id) {
      $inputData = json_decode(file_get_contents("php://input"), true);
      if (!$inputData) return ["error" => "Invalid JSON input"];

      $validationErrors = validateProductData($inputData, true);
      if (!empty($validationErrors)) return ["errors" => $validationErrors];

      return $this->productModel->updateProduct($id, $inputData);
  }

  public function deleteProduct($id) {
      return $this->productModel->deleteProduct($id);
  }
}
