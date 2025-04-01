<?php

require_once __DIR__ . '/controller/ProductController.php';

$routes = [
    "GET /products" => "ProductController@getAllProducts",
    "GET /products/{id}" => "ProductController@getProductById",
    "POST /products" => "ProductController@addProduct",
    "PATCH /products/{id}" => "ProductController@updateProduct",
    "DELETE /products/{id}" => "ProductController@deleteProduct",
];

return $routes;