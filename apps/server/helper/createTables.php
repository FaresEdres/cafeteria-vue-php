<?php

require_once __DIR__ .'/../helper/errors.php';
require_once __DIR__ . '/../model/productModel.php';

$productModel = new ProductModel();
$productModel->createProductTable();

echo "Products table created successfully!";
?>
