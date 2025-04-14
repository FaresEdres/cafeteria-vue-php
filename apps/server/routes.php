<?php

require_once __DIR__ . '/controller/productController.php';
require_once __DIR__ . '/controller/categoryController.php';

require_once __DIR__ . '/controller/userController.php'; 
require_once __DIR__ . '/controller/authenticateController.php';


$routes = [
    "GET /products" => "productController@getAllProducts",
    "GET /products/{id}" => "productController@getProductById",
    "POST /products" => "productController@addProduct",
    "POST /products/{id}" => "productController@updateProduct",
    "DELETE /products/{id}" => "productController@deleteProduct",


    "POST /logout" => "authenticateController@logout",
    "POST /login" => "authenticateController@login",
    "POST /authenticated" => "authenticateController@authenticated",

    "POST /category" => "categoryController@addCategory",
    "GET /category" => "categoryController@getAllCategories",
    "DELETE /category/{id}" => "categoryController@deleteCategory",


    // User routes
    "GET /users" => "userController@getAllUsers",
    "GET /users/{id}" => "userController@getUserById",
    "POST /users" => "userController@addUser",
    "PATCH /users/{id}" => "userController@updateUser",
    "DELETE /users/{id}" => "userController@deleteUser",
    "POST /users/login" => "userController@loginUser",

    "POST /orders" => "OrdersController@addOrder",
    "GET /orders/{id}" => "OrdersController@getAllOrders",
    "PATCH /orders/{id}" => "OrdersController@editOrder",
    "GET /orders" => "OrdersController@getAllOrdersForAdmin"

];

return $routes;