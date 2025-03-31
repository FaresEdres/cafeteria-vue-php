<?php

require_once __DIR__ . '/../helper/errors.php';
header("Content-Type: application/json");

require_once __DIR__ . '/../controller/productModel.php';
require_once __DIR__ . '/../validation/productValidation.php';

$productModel = new ProductModel();

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
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
  $inputData = json_decode(file_get_contents("php://input"), true);

  if (!isset($inputData["id"])) {
    echo json_encode(["error" => "Product ID is required"]);
    exit;
  }

  $productModel = new ProductModel();
  $deleteResult = $productModel->deleteProduct($inputData["id"]);

  if (isset($deleteResult["error"])) {
    echo json_encode(["error" => $deleteResult["error"]]);
  } else {
    echo json_encode(["message" => "Product deleted successfully"]);
  }

} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
  if (isset($_GET['id'])) {
    $product = $productModel->displayProductById($_GET['id']);
    echo json_encode($product);
  } else {
    $products = $productModel->displayAllProducts();
    echo json_encode($products);
  }

} elseif ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
  $inputData = json_decode(file_get_contents("php://input"), true);

  if (!isset($inputData["id"])) {
    echo json_encode(["error" => "Product ID is required"]);
    exit;
  }

  $productId = $inputData["id"];
  unset($inputData["id"]);
  $validationErrors = validateProductData($inputData, true);
  if (!empty($validationErrors)) {
    echo json_encode(["errors" => $validationErrors]);
    exit;
  }

  $updateResult = $productModel->updateProduct($productId, $inputData);
  echo json_encode($updateResult);

} else {
  echo json_encode(["error" => "Only GET, POST, DELETE, and PATCH requests are allowed"]);
}
?>
