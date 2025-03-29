<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$response = [
    "message" => "Welcome to Cafeteria Management System API",
    "timestamp" => time(),
    "environment" => "Development"
];

echo json_encode($response, JSON_PRETTY_PRINT);

