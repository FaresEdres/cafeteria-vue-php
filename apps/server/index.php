<?php
require_once __DIR__ . '/router.php';
require_once __DIR__.  "/controller/userModel.php"; 

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

$users = getAllUsers();
if ($users) {
    echo "<h2>✅ Users Retrieved</h2>";
    echo "<pre>";
    print_r($users);
    echo "</pre>";
} else {
    echo "<h2>❌ Failed to Retrieve Users</h2>";
}
?>