<?php

namespace App\Core;

use App\Core\Database;
use PDO;


class Controller
{
    /** @var PDO A conexão com o banco de dados */
    protected PDO $db_connection;

    // ==========================================================
    // SEÇÃO ADICIONADA PARA CORRIGIR O ERRO
    // ==========================================================
    public function __construct()
    {
        // Inicializamos a propriedade AQUI, assim que o Controller é criado.
        // Usamos nossa classe Singleton para pegar a conexão.
        $this->db_connection = Database::getInstance();
    }
    // ==========================================================


    public function view($view, $data = [])
    {
        extract($data);

        $viewPath = BASE_PATH . "/App/View/{$view}.php";

        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            self::handleError("View not found: {$viewPath}");
        }
    }

    public static function handleError($message)
    {
        error_log($message);
        require_once BASE_PATH . '/App/View/common/error.php';
        exit;
    }

    public function model(string $modelName)
    {
        $modelClass = "App\\Model\\{$modelName}Model";
        if (class_exists($modelClass)) {
            return new $modelClass($this->db_connection);
        }
        throw new \Exception("Model {$modelClass} não encontrado.");
    }

    
}
