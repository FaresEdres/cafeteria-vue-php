<?php
require_once __DIR__ . '/routes.php';
// Set CORS headers globally
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, POST, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

// Handle CORS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$routes = require __DIR__ . '/routes.php';
error_log("Received request: $method $path");

foreach ($routes as $route => $handler) {
    [$routeMethod, $routePath] = explode(" ", $route, 2);

    $routePattern = preg_replace('/{id}/', '(\d+)', $routePath);

    if ($method === $routeMethod && preg_match("#^$routePattern$#", $path, $matches)) {
        array_shift($matches);
        [$class, $method] = explode("@", $handler);
        require_once __DIR__ . "/controller/productController.php";

        $controller = new $class();
        echo json_encode(call_user_func_array([$controller, $method], $matches));
        exit;
    }
}

http_response_code(404);
echo json_encode(["error" => "Route not found"]);

?>
