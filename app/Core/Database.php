<?php

namespace App\Core; // Verifique se o namespace está correto para sua estrutura

use Dotenv\Dotenv;
use PDO;
use PDOException;

class Database {
    private static $instance = null;
    private $connection;

    // O construtor agora lê o .env, mas continua privado (regra do Singleton)
    private function __construct() {
        // Carrega as variáveis de ambiente do arquivo .env
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();

        // Pega as credenciais do .env em vez de estarem fixas no código
        $host = $_ENV['DB_HOST'];
        $user = $_ENV['DB_USERNAME'];
        $password = $_ENV['DB_PASSWORD'];
        $database = $_ENV['DB_DATABASE'];

        try {
            $this->connection = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $user, $password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // Seu ótimo tratamento de erros foi mantido!
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