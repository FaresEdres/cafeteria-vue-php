<?php
require_once __DIR__ . '/router.php';
require_once __DIR__ . '/controller/userController.php';
require_once __DIR__ . '/model/userModel.php'; 

require __DIR__ . '/vendor/autoload.php';

Dotenv\Dotenv::createImmutable(__DIR__)->load();

/*
echo getenv('APP_ENV');
echo $_ENV['APP_NAME'];
*/


$connection = SQLConnector::getInstance();
if ($connection) {
    echo "<h2>✅ Database Connection Successful</h2>";
} else {
    echo "<h2>❌ Database Connection Failed</h2>";
}

$userModel = new UserModel();
$users = $userModel->displayAllUsers(); 

if ($users && !isset($users["error"])) {
    echo "<h2>✅ Users Retrieved</h2>";
    echo "<pre>";
    print_r($users);
    echo "</pre>";
} else {
    echo "<h2>❌ Failed to Retrieve Users</h2>";
    if (isset($users["error"])) {
        echo "<p>Error: " . $users["error"] . "</p>";
    }
    }
?>