<?php

use App\Controller\ColaboradorController;
use App\Controller\HomeController;
use App\Controller\UserController;
use App\Core\Router;
use App\Controller\BeneficioController;

require_once __DIR__ . '/../vendor/autoload.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

session_start();

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/config.php';

$router = new Router();

// ----------------------
// Rotas de Autenticação
// ----------------------
$router->addRoute('GET', '/', UserController::class, 'show_login'); // Página inicial é o login
$router->addRoute('GET', '/login', UserController::class, 'show_login');
$router->addRoute('POST', '/login', UserController::class, 'process_login');
$router->addRoute('GET', '/cadastro', UserController::class, 'show_cadastro');
$router->addRoute('POST', '/register', UserController::class, 'register'); // API para o JavaScript
$router->addRoute('GET', '/registro-sucesso', UserController::class, 'show_registro_sucesso');
$router->addRoute('GET', '/verify', UserController::class, 'verify_account');


//Colaboradores
$router->addRoute('GET', '/colaboradores/adicionar', ColaboradorController::class, 'novo');
$router->addRoute('POST', '/colaboradores/criar', ColaboradorController::class, 'criar');
$router->addRoute('GET', '/colaboradores', ColaboradorController::class, 'listar');


// Benefícios
$router->addRoute('GET', '/beneficios', BeneficioController::class, 'index');
$router->addRoute('POST', '/beneficios/criar', BeneficioController::class, 'criar');
$router->addRoute('POST', '/beneficios/editar', BeneficioController::class, 'editar');
$router->addRoute('GET', '/beneficios/desativar/{id}', BeneficioController::class, 'desativar');

$router->addRoute('GET', '/meus_beneficios', BeneficioController::class, 'meusBeneficios'); 

// Rota de Gestão de Vagas
$router->addRoute('GET', '/vagas/listar', \App\Controller\GestaoVagasController::class, 'listarVagas');

//rotas para criar nova vaga
$router->addRoute('GET', '/vagas/criar', \App\Controller\GestaoVagasController::class, 'criar');
$router->addRoute('POST', '/vagas/salvar', \App\Controller\GestaoVagasController::class, 'salvar');

// Rotas Comuns
$router->addRoute('GET', '/thank_you', Controller::class, 'show_thank_you');
$router->addRoute('GET', '/error', Controller::class, 'show_error');

// ----------------------
// Inicia o roteamento
// ----------------------
$router->getRoutes();

