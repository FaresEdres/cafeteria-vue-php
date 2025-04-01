<?php

require_once __DIR__ . '/../helper/errors.php';
require_once __DIR__ . '/../model/productModel.php';
require_once __DIR__ . '/../validation/productValidation.php';

class ProductController {
  private $productModel;

  public function __construct() {
      $this->productModel = new ProductModel();
  }

  public function getAllProducts() {
      return $this->productModel->displayAllProducts();
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
