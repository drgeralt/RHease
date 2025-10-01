<?php

namespace App\Core;

use PDO;

class Controller
{

       /** @var PDO */
    protected PDO $db_connection;

    /**
     * Construtor recebe a conexão PDO
     */
    public function __construct(PDO $pdo)
    {
        $this->db_connection = $pdo;
    }


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
