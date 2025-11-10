<?php
declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

class   Database {
    private static ?Database $instance = null;
    private PDO $connection;

    /**
     * Database constructor.
     * Cria a conexão PDO usando constantes definidas em `config.php`.
     */

    private function __construct()
    {
        $host = defined('DB_HOST') ? DB_HOST : (getenv('DB_HOST') ?: 'localhost');
        $user = defined('DB_USER') ? DB_USER : (getenv('DB_USER') ?: 'usuario_app');
        $password = defined('DB_PASS') ? DB_PASS : (getenv('DB_PASS') ?: 'rhease');
        $database = defined('DB_NAME') ? DB_NAME : (getenv('DB_NAME') ?: 'rhease');

        try {
            $this->connection = new PDO("mysql:host={$host};dbname={$database};charset=utf8mb4", $user, $password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            error_log("Erro ao conectar ao banco de dados: " . $e->getMessage());
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

   /**
     * Retorna a instância única da conexão PDO.
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }

        return self::$instance->connection;
    }
}