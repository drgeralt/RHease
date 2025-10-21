<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Database;
use JetBrains\PhpStorm\NoReturn;
use PDO;


class Controller
{
    /** @var PDO A conexão com o banco de dados */
    protected PDO $db_connection;

    public function __construct()
    {
        $this->db_connection = Database::getInstance();
    }

    /**
     * Renderiza uma view.
     *
     * @param string $view Caminho relativo da view (ex: 'Vaga/gestaoVagas')
     * @param array $data Dados a disponibilizar na view
     */

    public function view($view, $data = []): void
    {
        extract($data);

        $viewPath = BASE_PATH . "/App/View/{$view}.php";

       if (file_exists($viewPath)) {
            require_once $viewPath;
            return;
        }

        // Se o arquivo da view não existir, logamos e exibimos a página de erro.
        self::handleError("View not found: {$viewPath}");
    }

    public static function handleError($message): void
    {
        error_log($message);
        require_once BASE_PATH . '/App/View/Common/error.php';
        exit;
    }

    /**
     * Retorna uma instância do Model pedido.
     *
     * @param string $modelName
     * @return object
     */
    public function model(string $modelName): object
    {
        $modelClass = "App\\Model\\{$modelName}Model";
        if (class_exists($modelClass)) {
            return new $modelClass($this->db_connection);
        }

        throw new \Exception("Model {$modelClass} não encontrado.");
    }


    /**
     * Função auxiliar para padronizar e enviar respostas JSON.
     * Idealmente, esta função estaria em um Core\Controller base.
     */
    #[NoReturn]
    protected function jsonResponse(bool $success, string $mensagem, $data = null, int $statusCode = 200) {
        header('Content-Type: application/json');
        http_response_code($statusCode);

        $response = ['success' => $success, 'mensagem' => $mensagem];
        if ($data !== null) {
            $response['data'] = $data;
        }

        echo json_encode($response);
        exit;
    }
    function formatarValor($valor) {
        if (is_numeric($valor) && $valor > 0) {
            return 'R$ ' . number_format((float)$valor, 2, ',', '.');
        }
        return '';
    }
}
