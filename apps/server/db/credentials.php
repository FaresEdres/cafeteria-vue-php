<?php
// require __DIR__ . '/../vendor/autoload.php';

// Dotenv\Dotenv::createImmutable(__DIR__."/..")->load();

// define('DB_HOST',$_ENV["DB_HOST"]);
// define('DB_USER',$_ENV["DB_USER"]);
// define('DB_PASSWORD',$_ENV["DB_PASSWORD"]);
// define('DB_NAME',$_ENV["DB_NAME"]);
// define('DB_PORT',$_ENV["DB_PORT"]);

require __DIR__ . '/../vendor/autoload.php';
Dotenv\Dotenv::createImmutable(__DIR__."/..")->load();

define('DB_HOST',$_ENV["DB_HOST"]);
define('DB_USER',$_ENV["DB_USER"]);
define('DB_PASSWORD',$_ENV["DB_PASSWORD"]);
define('DB_NAME',$_ENV["DB_NAME"]);
define('DB_PORT',$_ENV["DB_PORT"]);
?>
