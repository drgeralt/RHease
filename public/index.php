<?php

use App\Controller\ColaboradorController;
use App\Controller\HomeController;
use App\Core\Router;

require_once __DIR__ .'/../vendor/autoload.php';

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
$router->addRoute('GET', '/', HomeController::class, 'show_index');

//ColaboradorController
$router->addRoute('GET', '/colaboradores/adicionar', ColaboradorController::class, 'novo');
$router->addRoute('POST', '/colaboradores/criar', ColaboradorController::class, 'criar');

// Rota de Gestão de Vagas
$router->addRoute('GET', '/vagas/listar', \App\Controller\GestaoVagasController::class, 'listarVagas');

//rotas para criar nova vaga
$router->addRoute('GET', '/vagas/criar', \App\Controller\GestaoVagasController::class, 'criar');
$router->addRoute('POST', '/vagas/salvar', \App\Controller\GestaoVagasController::class, 'salvar');

// Rotas Comuns
//$router->addRoute('GET', '/thank_you', Controller::class, 'show_thank_you');
//$router->addRoute('GET', '/error', Controller::class, 'show_error');

// ----------------------
// Inicia o roteamento
// ----------------------
$router->getRoutes();
