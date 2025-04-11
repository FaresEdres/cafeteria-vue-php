<?php
require_once __DIR__ . '/router.php';

require __DIR__ . '/vendor/autoload.php';

Dotenv\Dotenv::createImmutable(__DIR__)->load();

/*
echo getenv('APP_ENV');
echo $_ENV['APP_NAME'];
*/
