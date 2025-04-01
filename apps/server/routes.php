<?php
require_once __DIR__ . '/controller/productController.php';
require_once __DIR__ . '/controller/categoryController.php';
$routes = [
    "GET /products" => "productController@getAllProducts",
    "GET /products/{id}" => "productController@getProductById",
    "POST /products" => "productController@addProduct",
    "PATCH /products/{id}" => "productController@updateProduct",
    "DELETE /products/{id}" => "productController@deleteProduct",

    "POST /category" => "categoryController@addCategory",
    "GET /category" => "categoryController@getAllCategories",
    "DELETE /category/{id}" => "categoryController@deleteCategory"
];

return $routes;
