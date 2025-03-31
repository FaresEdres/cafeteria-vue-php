<?php

require_once __DIR__ .'/../helper/errors.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . "/../model/connector.php";
require_once __DIR__ . "/../helper/CRUD.php";

use Respect\Validation\Validator as v;
function validateCategoryData($data)
{
  $errors = [];

  try {
    v::stringType()->length(1, 255)->check($data['name']);
  } catch (Exception $e) {
    $errors['name'] = "Product name must be between 1 and 255 characters.";
  }

  $db = new CRUD();
  $existingCategory = $db->select("categories", ["name" => $data['name']]);

  if ($existingCategory) {
      $errors['name'] = "Category name already exists. Please choose another.";
  }

  return $errors;
}
