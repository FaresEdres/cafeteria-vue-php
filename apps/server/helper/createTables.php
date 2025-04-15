<?php

require_once __DIR__ .'/../helper/errors.php';
require_once __DIR__ . '/../model/productModel.php';
require_once __DIR__ . '/../model/userModel.php';
require_once __DIR__ . '/../model/orderModel.php';
require_once __DIR__ . '/../model/orderProuductsModel.php';


$productModel = new ProductModel();
$productModel->createProductTable();
$orderModel = new OrderModel();
$orderModel->createOrderTable();
$orderPModel = new orderProductsModel();
$orderPModel->createOrderProductsTable();

$userModel = new UserModel();
$userModel->createUserTable();

// echo "Products table created successfully!";
echo "USER table created successfully!";
?>
