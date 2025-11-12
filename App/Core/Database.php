<?php
declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private function __construct() {}

    public static function createConnection(): PDO
    {
        $host = defined('DB_HOST') ? DB_HOST : (getenv('DB_HOST') ?: 'localhost');
        $user = defined('DB_USER') ? DB_USER : (getenv('DB_USER') ?: 'root');
        $password = defined('DB_PASS') ? DB_PASS : (getenv('DB_PASS') ?: '');
        $database = defined('DB_NAME') ? DB_NAME : (getenv('DB_NAME') ?: 'rhease');

        try {
            $connection = new PDO("mysql:host={$host};dbname={$database};charset=utf8mb4", $user, $password);
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $connection;
        } catch (PDOException $e) {
            error_log("Erro ao conectar ao banco de dados: " . $e->getMessage());
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }
}