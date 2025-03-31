<?php
// server/router.php
require_once __DIR__ . '/helper/errors.php';
header("Content-Type: application/json");

// 1. Get Request Information
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); // e.g., "/api/orders"
$requestMethod = $_SERVER['REQUEST_METHOD']; // e.g., "POST"

// 2. Remove base path
$basePath = '/api';
$route = str_replace($basePath, '', $requestUri); // e.g., "/orders"

// 3. Split into parts
$routeParts = explode('/', trim($route, '/')); // e.g., ["orders"]

// 4. Define available resources
$resources = [
    'products' => 'ProductModel',
    'orders' => 'OrderModel',
    'users' => 'UserModel',
    'categories' => 'CategoryModel'
];

// 5. Route the request
if (count($routeParts) > 0 && isset($resources[$routeParts[0]])) {
    $resourceName = $routeParts[0]; // e.g., "orders"
    $resourceId = isset($routeParts[1]) ? $routeParts[1] : null;
    $controllerClass = $resources[$resourceName];
    
    // 6. Include and instantiate the controller
    require_once __DIR__ . "/controller/{$controllerClass}.php";
    $controller = new $controllerClass();
    
    // 7. Handle different HTTP methods
    switch ($requestMethod) {
        case 'GET':
            if ($resourceId) {
                // e.g., GET /api/orders/123
                $response = $controller->displayById($resourceId);
            } else {
                // e.g., GET /api/orders
                $response = $controller->displayAll();
            }
            break;
            
        case 'POST':
            // e.g., POST /api/orders
            $inputData = json_decode(file_get_contents("php://input"), true);
            $response = $controller->add($inputData);
            break;
            
        case 'PUT':
        case 'PATCH':
            // e.g., PATCH /api/orders/123
            $inputData = json_decode(file_get_contents("php://input"), true);
            $response = $controller->update($resourceId, $inputData);
            break;
            
        case 'DELETE':
            // e.g., DELETE /api/orders/123
            $response = $controller->delete($resourceId);
            break;
            
        default:
            $response = ["error" => "Method not allowed"];
            http_response_code(405);
    }
    
    echo json_encode($response);
} else {
    http_response_code(404);
    echo json_encode(["error" => "Resource not found"]);
}
?>