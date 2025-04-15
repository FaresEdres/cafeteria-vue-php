<?php
require_once __DIR__ . '/router.php';
require_once __DIR__ . '/vendor/autoload.php';

try {
    Dotenv\Dotenv::createImmutable(__DIR__)->load();
} catch (Exception $e) {
    echo "Failed to load environment file: " . $e->getMessage();
}

/*
echo getenv('APP_ENV');
echo $_ENV['APP_NAME'];
*/
