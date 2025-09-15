<?php

class Controller
{
    public function view($view, $data = [])
    {
        // Extract data to be used in the view
        extract($data);

        $viewPath = BASE_PATH . "/app/Views/{$view}.php";

        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            // Handle view not found error
            self::handleError("View not found: {$viewPath}");
        }
    }

    public static function handleError($message)
    {
        // Simple error handling
        error_log($message);
        // You might want to show a generic error page
        require_once BASE_PATH . '/app/Views/common/error.php';
        exit;
    }
}
