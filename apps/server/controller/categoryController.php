<?php

require_once __DIR__ .'/../helper/errors.php';
header("Content-Type: application/json");

require_once __DIR__ . '/../model/categoryModel.php';
require_once __DIR__ . '/../validation/categoryValidation.php';

class CategoryController{
  private $categoryModel;

  public function __construct() {
    $this->categoryModel = new CategoryModel();
  }
  public function getAllCategories() {
    return $this->categoryModel->displayAllCatergories();
  }
  public function addCategory(){
    $inputData = json_decode(file_get_contents("php://input"), true);
      if (!$inputData) return ["error" => "Invalid JSON input"];

      $validationErrors = validateCategoryData($inputData);
      if (!empty($validationErrors)) return ["errors" => $validationErrors];

      return $this->categoryModel->addCategory($inputData);
  }
  public function deleteCategory($id) {
    return $this->categoryModel->deleteCategory($id);
}
}


?>
