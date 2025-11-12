<?php

declare(strict_types=1);

namespace App\Core;

use JetBrains\PhpStorm\NoReturn;
use PDO;

class Controller
{
    protected PDO $db_connection;

    public function __construct(PDO $pdo)
    {
        $this->db_connection = $pdo;
    }

    public function view($view, $data = []): void
    {
        extract($data);

        $viewPath = BASE_PATH . "/App/View/{$view}.php";

        if (file_exists($viewPath)) {
            require_once $viewPath;
            return;
        }

        self::handleError("View not found: {$viewPath}");
    }

    public static function handleError($message): void
    {
        error_log($message);
        require_once BASE_PATH . '/App/View/Common/error.php';
        exit;
    }

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

    protected function formatarValor($valor) {
        if (is_numeric($valor) && $valor > 0) {
            return 'R$ ' . number_format((float)$valor, 2, ',', '.');
        }
        return '';
    }
}