<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ .'/../validation/userValidation.php';
require_once __DIR__ .'/../model/userModel.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

Dotenv\Dotenv::createImmutable(__DIR__."/..")->load();

define('SECRET_KEY',$_ENV["SECRET_KEY"]);
class AuthenticateController {
    private $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    private function generateJWT($userId) {
    
        $payload = [
            'iss' => "http://localhost:8000", // Issuer, who created the token
            'aud' => "http://localhost:5173", // Audience, who the token is intended for
            'iat' => time(),           
            'exp' => time() + (60 * 60 * 8),
            'user_id' => $userId
        ];
    
        return JWT::encode($payload, SECRET_KEY, 'HS256');
    }
  public function login() {

    $error = validateUserDataLogin($_POST['email'], $_POST['password']);
    if ($error) {
        http_response_code(400);
        return ["error" => $error];
    }

    
    $user = emailExistence($_POST['email']);
    if (!$user) {
        http_response_code(401);
        return ["error" => "Invalid credentials"];
    }

    if (!password_verify($_POST['password'], $user['password'])) {
        http_response_code(401);
        return ["error" => "Invalid credentials"];
    }
    $jwt = $this->generateJWT($user['id']);

    setcookie("token", $jwt, [
        "expires" => time() + (60 * 60 * 8),
        "path" => "/",              
        "httponly" => true,
        "secure" => false, // https 
        "samesite" => "Strict" 
    ]);
    return ["message" => "Login successful"];
  }

  public function authenticated() {
    $token = $_COOKIE['token'] ?? null;
    if (!$token) {
        return null;
    }
    
    $token = str_replace('Bearer ', '', $token);

    try {
        $decoded = JWT::decode($token, keyOrKeyArray: new Key(SECRET_KEY, 'HS256'));
        $user = $this->userModel->displayUserById($decoded->user_id);
        if (!$user) {
            return ["message" => "null"];
        }
        return ["message" => $user];
    } catch (Exception $e) {
        return ["message" => "null"];
    }
}

  public function logout() {
    if(! $_COOKIE['token']){
        return ["error" => "No token found"];
    }

    setcookie(
        "token", 
        "", 
        [
            "expires" => time() - 3600,
            "path" => "/",              
            "secure" => false,            
            "httponly" => true,            
            "samesite" => "Strict"         
        ]
    );    
    unset($_COOKIE['token']);
    return ["message" => "Logged Out Successfully"];
  }
}
