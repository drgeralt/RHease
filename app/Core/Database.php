<?php

class   Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        $host = 'localhost';
        $user = 'root';
        $password = ''; // Default XAMPP password
        $database = 'rhease';

        try {
            $this->connection = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $user, $password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // Log the error and re-throw the exception
            error_log("Erro ao conectar ao banco de dados: " . $e->getMessage());
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Database();
        }

        return self::$instance->connection;
    }
}
