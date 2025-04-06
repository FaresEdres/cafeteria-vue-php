<?php

require_once __DIR__ .'/../helper/errors.php';
require_once __DIR__ . '/../model/productModel.php';
require_once __DIR__ . '/../model/userModel.php';

$productModel = new ProductModel();
$productModel->createProductTable();

$userModel = new UserModel();
$userModel->createUserTable();

// echo "Products table created successfully!";
echo "USER table created successfully!";
?>