<?php

namespace App\Core;

use PDO;

class Controller
{
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

    
}
