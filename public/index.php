<?php

use App\Controller\ColaboradorController;
use App\Controller\HomeController;
use App\Controller\PontoController;
use App\Controller\UserController;
use App\Controller\CandidaturaController;
use App\Controller\GestaoVagasController;
use App\Core\Router;

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
$router->addRoute('GET', '/registrarponto', PontoController::class, 'index');
$router->addRoute('POST', '/registrarponto/salvar', PontoController::class, 'registrar');
$router->addRoute('GET', '/colaboradores', ColaboradorController::class, 'listar');

// Rota de Gestão de Vagas
$router->addRoute('GET', '/vagas/listar', \App\Controller\GestaoVagasController::class, 'listarVagas');


//rotas para criar nova vaga
$router->addRoute('GET', '/vagas/criar', \App\Controller\GestaoVagasController::class, 'criar');
$router->addRoute('POST', '/vagas/salvar', \App\Controller\GestaoVagasController::class, 'salvar');


//Candidatura
$router->addRoute('GET', '/vagas', CandidaturaController::class, 'listar');
$router->addRoute('POST', '/candidatura/formulario', CandidaturaController::class, 'exibirFormulario');
$router->addRoute('POST', '/candidatura/aplicar', CandidaturaController::class, 'aplicar');
$router->addRoute('GET', '/candidatura/formulario', CandidaturaController::class, 'redirecionarParaVagas');
$router->addRoute('GET', '/candidatura', CandidaturaController::class, 'redirecionarParaVagas');
$router->addRoute('POST', '/vagas/candidatos', GestaoVagasController::class, 'verCandidatos');
$router->addRoute('POST', 'candidatura/analisar', CandidaturaController::class, 'analisarCurriculo');
$router->addRoute('GET', '/candidatura/analise-ia', CandidaturaController::class, 'exibirAnaliseIA');
// Rotas Comuns
//$router->addRoute('GET', '/thank_you', Controller::class, 'show_thank_you');
//$router->addRoute('GET', '/error', Controller::class, 'show_error');

// ----------------------
// Inicia o roteamento
// ----------------------
$router->getRoutes();