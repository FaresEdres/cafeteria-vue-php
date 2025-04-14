<?php

require_once __DIR__ . '/../helper/errors.php';
header("Content-Type: application/json");

require_once __DIR__ . '/../model/categoryModel.php';
require_once __DIR__ . '/../validation/categoryValidation.php';

class CategoryController
{
  private $categoryModel;

  public function __construct()
  {
    $this->categoryModel = new CategoryModel();
  }
  public function getAllCategories()
  {
    return $this->categoryModel->displayAllCatergories();
  }
  public function addCategory()
  {
    // Check if the 'name' field exists in the POST data
    if (!isset($_POST['name']) || trim($_POST['name']) === '') {
      return ["error" => "Category name is required."];
    }

    // Sanitize input
    $inputData = [
      'name' => trim($_POST['name'])
    ];

    // Validate input
    $validationErrors = validateCategoryData($inputData);
    if (!empty($validationErrors)) {
      return ["errors" => $validationErrors];
    }

    // Proceed to save category
    return $this->categoryModel->addCategory($inputData);
  }

  public function deleteCategory($id)
  {
    return $this->categoryModel->deleteCategory($id);
  }
}
