<?php
namespace App\Core;

use PDO;
use PDOException;

class Database{
    private static $instance;

    public static function connection(){
        if(!self::$instance){
            try {
                self::$instance = new PDO(
                    "mysql:host=". $_ENV['DB_HOST'] .";dbname=".$_ENV["DB_NAME"].";charset=utf8",
                    $_ENV['DB_USER'],
                    $_ENV['DB_PASS'],
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(["error" => $e]);
                exit;
            }
        }

        return self::$instance;
    }
}