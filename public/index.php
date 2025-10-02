<?php

use App\Controller\ColaboradorController;
use App\Controller\HomeController;
use App\Controller\UserController;
use App\Core\Router;

require_once __DIR__ . '/../vendor/autoload.php';

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


// Instancia o roteador
$router = new Router();

// ----------------------
// Registro de rotas
// ----------------------
// A rota principal '/' agora aponta para a página de login.
$router->addRoute('GET', '/', UserController::class, 'show_login');
$router->addRoute('GET', '/login', UserController::class, 'show_login');
$router->addRoute('GET', '/cadastro', UserController::class, 'show_cadastro');
$router->addRoute('POST', '/register', UserController::class, 'register'); // API de registro
$router->addRoute('GET', '/registro-sucesso', UserController::class, 'show_registro_sucesso');
$router->addRoute('GET', '/verify', UserController::class, 'verify_account');


//ColaboradorController
$router->addRoute('GET', '/colaboradores/adicionar', ColaboradorController::class, 'novo');
$router->addRoute('POST', '/colaboradores/criar', ColaboradorController::class, 'criar');
$router->addRoute('GET', '/colaboradores', ColaboradorController::class, 'listar');


// ----------------------
// Inicia o roteamento
// ----------------------
$router->getRoutes();