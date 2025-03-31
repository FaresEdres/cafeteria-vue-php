<?php

require_once __DIR__ . '/routes.php';

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$routes = require __DIR__ . '/routes.php';

// Match the route
foreach ($routes as $route => $handler) {
    [$routeMethod, $routePath] = explode(" ", $route, 2);
    
    // Replace {id} with regex pattern
    $routePattern = preg_replace('/{id}/', '(\d+)', $routePath);
    
    if ($method === $routeMethod && preg_match("#^$routePattern$#", $path, $matches)) {
        array_shift($matches); // Remove full match
        [$class, $method] = explode("@", $handler);
        require_once __DIR__ . "/controller/ProductController.php";

        $controller = new $class();
        echo json_encode(call_user_func_array([$controller, $method], $matches));
        exit;
    }
}

// No matching route
http_response_code(404);
echo json_encode(["error" => "Route not found"]);

?>