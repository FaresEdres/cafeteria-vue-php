<?php

require_once __DIR__ .'/../helper/errors.php';
header("Content-Type: application/json");

require_once __DIR__ . '/../controller/categoryModel.php';
require_once __DIR__ . '/../validation/categoryValidation.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode(["error" => "Only POST requests are allowed"]);
  exit;
}

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

$categoryModel = new CategoryModel();
$result = $categoryModel->addCategory($categoryData);

if (isset($result["error"])) {
  echo json_encode(["error" => $result["error"]]);
} else {
  echo json_encode(["message" => "Category inserted successfully", "id" => $result["id"]]);
}
?>
