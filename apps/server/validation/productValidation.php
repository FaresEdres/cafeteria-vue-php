<?php

require_once __DIR__ .'/../helper/errors.php';
require_once __DIR__ . '/../vendor/autoload.php';
use Respect\Validation\Validator as v;
function validateProductData($data, $isUpdate = false)
{
    $errors = [];

    if (isset($data['name'])) {
        try {
            v::stringType()->length(1, 255)->check($data['name']);
        } catch (Exception $e) {
            $errors['name'] = "Product name must be between 1 and 255 characters.";
        }
    }

    if (!$isUpdate || isset($data['image'])) {
        try {
            $isValid = v::oneOf(v::url(), v::regex("/\.(jpg|jpeg|png|gif)$/i"))->validate($data['image'] ?? '');
            if (!$isValid) {
                $errors['image'] = "Invalid image format. Allowed: URL or .jpg, .jpeg, .png, .gif";
            }
        } catch (Exception $e) {
            $errors['image'] = "Invalid image format or URL.";
        }
    }

    if (isset($data['description'])) {
        try {
            v::optional(v::length(0, 1000))->check($data['description']);
        } catch (Exception $e) {
            $errors['description'] = "Description must not exceed 1000 characters.";
        }
    }

    if (isset($data['price'])) {
        try {
            v::floatVal()->positive()->check((float) $data['price']);
        } catch (Exception $e) {
            $errors['price'] = "Price must be a valid positive number.";
        }
    }

    //check that category_id exist..

    return $errors;
}
