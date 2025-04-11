<?php
// CORS headers
header("Access-Control-Allow-Origin: *"); // Allow all origins (you can restrict to specific domains if needed)
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); // Allowed methods
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With"); // Allowed headers

// Handle preflight request for OPTIONS method
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$routes = require __DIR__ . '/routes.php';

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];
$matchedRoute = null;
$params = [];

// Match route with support for dynamic {id}
foreach ($routes as $route => $action) {
    [$method, $uri] = explode(' ', $route, 2);

    // Convert /products/{id} to regex
    $pattern = preg_replace('#\{[^/]+\}#', '([^/]+)', $uri);
    $pattern = "#^" . $pattern . "$#";

    if ($requestMethod === $method && preg_match($pattern, $requestUri, $matches)) {
        array_shift($matches); // First match is the full string
        $params = $matches;
        $matchedRoute = $action;
        break;
    }
}

if (!$matchedRoute) {
    http_response_code(404);
    echo json_encode(['error' => 'Route not found']);
    exit;
}

[$controllerName, $methodName] = explode('@', $matchedRoute);

$controllerFile = __DIR__ . "/controller/{$controllerName}.php";
if (!file_exists($controllerFile)) {
    http_response_code(500);
    echo json_encode(['error' => 'Controller file not found']);
    exit;
}

require_once $controllerFile;
$controller = new $controllerName();

try {
    // Detect the content type
    $contentType = $_SERVER["CONTENT_TYPE"] ?? '';
    $isJson = str_contains($contentType, 'application/json');
    $isFormData = str_contains($contentType, 'multipart/form-data');

    // Handle input data (POST data and files)
    if ($isJson) {
        // For JSON data
        $postData = json_decode(file_get_contents("php://input"), true);
        $files = $_FILES;  // $_FILES is always available for form-data
    } elseif ($isFormData) {
        // For FormData (multipart form-data with files)
        $postData = $_POST;
        $files = $_FILES;
    } else {
        // Handle other content types, or assume form data
        $postData = $_POST;
        $files = $_FILES;
    }

    // Detect if the method expects data (POST, PUT typically)
    $reflection = new ReflectionMethod($controller, $methodName);
    $numParams = $reflection->getNumberOfParameters();

    if ($requestMethod === 'POST' || $requestMethod === 'PUT') {
        // Handle method expecting both postData and files
        if ($numParams === 2) {
            $response = $controller->$methodName($postData, $files);
        } elseif ($numParams === 1 && !empty($params)) {
            $response = $controller->$methodName($params[0]); // e.g., updateProduct($id)
        } else {
            $response = $controller->$methodName();
        }
    } else {
        // For GET/DELETE requests
        $response = $controller->$methodName(...$params);
    }

    echo json_encode($response);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
