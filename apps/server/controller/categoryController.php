<?php

require_once __DIR__ .'/../helper/errors.php';
header("Content-Type: application/json");

require_once __DIR__ . '/../model/categoryModel.php';
require_once __DIR__ . '/../validation/categoryValidation.php';

$categoryModel = new CategoryModel();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $inputData = json_decode(file_get_contents("php://input"), true);

  if (!$inputData) {
    echo json_encode(["error" => "Invalid JSON input"]);
    exit;
  }

  $validationErrors = validateCategoryData($inputData);

  if (!empty($validationErrors)) {
    echo json_encode(["errors" => $validationErrors]);
    exit;
  }

  $categoryData = [
    "name" => $inputData["name"],
  ];

  $result = $categoryModel->addCategory($categoryData);

  if (isset($result["error"])) {
    echo json_encode(["error" => $result["error"]]);
  } else {
    echo json_encode(["message" => "Category inserted successfully", "id" => $result["id"]]);
  }
}elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
  $inputData = json_decode(file_get_contents("php://input"), true);

  if (!isset($inputData["id"])) {
    echo json_encode(["error" => "Category ID is required"]);
    exit;
  }

  $deleteResult = $categoryModel->deleteCategory($inputData["id"]);

  if (isset($deleteResult["error"])) {
    echo json_encode(["error" => $deleteResult["error"]]);
  } else {
    echo json_encode(["message" => "Category deleted successfully"]);
  }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
  $categories = $categoryModel->displayAllCatergories();
    echo json_encode($categories);

}else {
  echo json_encode(["error" => "Only POST, DELETE and GET requests are allowed"]);
}
?>
