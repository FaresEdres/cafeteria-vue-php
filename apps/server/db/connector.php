<?php
require_once __DIR__. "/credentials.php";
require_once __DIR__. "/../utils.php";


class SQLConnector{
    private static $instance=null;
    private $pdo;
    function __construct(){
        try{
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";port=".DB_PORT.";charset=utf8";
            $this->pdo = new PDO($dsn, DB_USER, DB_PASSWORD);
        
        }
         catch (PDOException $e) {
        displayError($e->getMessage()); 

      }  

    }
    public static function  getInstance(){
        if(self::$instance===null){
            self::$instance=new SQLConnector();
        }
        return self::$instance->pdo;
    }
  

 public static function connectDatabase() {
    return SQLConnector::getInstance();
}

}


?>