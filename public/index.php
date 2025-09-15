<?php
// Código para forçar a exibição de todos os erros do PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Inicia a sessão para usar as mensagens de feedback
session_start();

// Define o caminho base do projeto
define('BASE_PATH', dirname(__DIR__));


// Carrega as configurações principais (DB_HOST, BASE_URL, etc.)
require_once BASE_PATH . '/config.php';

// Autoloader simples
spl_autoload_register(function ($class_name) {
    $paths = [
        BASE_PATH . '/app/Core/',
        BASE_PATH . '/app/Controller/',
        BASE_PATH . '/app/Models/'
    ];

    foreach ($paths as $path) {
        $file = $path . $class_name . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Instancia o roteador
$router = new Router();

// ----------------------
// Registro de rotas
// ----------------------
$router->addRoute('GET', '/', 'HomeController', 'show_index');

// Rotas Comuns
$router->addRoute('GET', '/thank_you', CommonController::class, 'show_thank_you');
$router->addRoute('GET', '/error', CommonController::class, 'show_error');

// ----------------------
// Inicia o roteamento
// ----------------------
$router->getRoutes();