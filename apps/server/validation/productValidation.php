<?php

require_once __DIR__ .'/../helper/errors.php';
require_once __DIR__ . '/../vendor/autoload.php';
use Respect\Validation\Validator as v;
function validateProductData($data)
{
  $errors = [];

  try {
    v::stringType()->length(1, 255)->check($data['name']);
  } catch (Exception $e) {
    $errors['name'] = "Product name must be between 1 and 255 characters.";
  }

  try {
    $isValid = v::oneOf(v::url(), v::regex("/\.(jpg|jpeg|png|gif)$/i"))->validate($data['image']);
    if (!$isValid) {
      echo "Invalid image format. Allowed: URL or .jpg, .jpeg, .png, .gif";
    }
  } catch (Exception $e) {
    $errors['image'] = "Invalid image format or URL.";
  }

  try {
    v::optional(v::length(0, 1000))->check($data['description']);
  } catch (Exception $e) {
    $errors['description'] = "Description must not exceed 1000 characters.";
  }

  try {
    v::floatVal()->positive()->check((float) $data['price']);
} catch (Exception $e) {
    $errors['price'] = "Price must be a valid positive number.";
}

  return $errors;
}
