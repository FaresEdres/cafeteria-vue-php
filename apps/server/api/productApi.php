<?php

require_once __DIR__ .'/../helper/errors.php';
header("Content-Type: application/json");

require_once __DIR__ . '/../controller/productModel.php';
require_once __DIR__ . '/../validation/productValidation.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode(["error" => "Only POST requests are allowed"]);
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $inputData = json_decode(file_get_contents("php://input"), true);

  if (!$inputData) {
    echo json_encode(["error" => "Invalid JSON input"]);
    exit;
  }

  $validationErrors = validateProductData($inputData);
  //var_dump($inputData);

  if (!empty($validationErrors)) {
    echo json_encode(["errors" => $validationErrors]);
    exit;
  }

  $productData = [
    "name" => $inputData["name"],
    "image" => $inputData["image"],
    "description" => $inputData["description"] ?? null,
    "price" => $inputData["price"],
    "category_id" => $inputData["category_id"]
  ];

  $productModel = new ProductModel();
  $result = $productModel->addProduct($productData);

  if (isset($result["error"])) {
    echo json_encode(["error" => $result["error"]]);
  } else {
    echo json_encode(["message" => "Product inserted successfully", "id" => $result["id"]]);
  }
} else {
  echo json_encode(["error" => "Only POST requests are allowed"]);
}
?>
