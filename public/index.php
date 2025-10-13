<?php

use App\Controller\ColaboradorController;
use App\Controller\HomeController;
use App\Controller\PontoController;
use App\Controller\UserController;
use App\Controller\CandidaturaController;
use App\Controller\GestaoVagasController;
use App\Core\Router;
use App\Controller\BeneficioController;
use App\Controller\HoleriteController;
use App\Controller\FolhaPagamentoController;


require_once __DIR__ . '/../vendor/autoload.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

session_start();

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/config.php';

$router = new Router();

// --- Rotas de Autenticação ---
$router->addRoute('GET', '/', UserController::class, 'show_login');
$router->addRoute('GET', '/login', UserController::class, 'show_login');
$router->addRoute('POST', '/login', UserController::class, 'process_login');
$router->addRoute('GET', '/cadastro', UserController::class, 'show_cadastro');
$router->addRoute('POST', '/register', UserController::class, 'register');
$router->addRoute('GET', '/registro-sucesso', UserController::class, 'show_registro_sucesso');
$router->addRoute('GET', '/verify', UserController::class, 'verify_account');

// --- Rotas de Colaboradores ---
$router->addRoute('GET', '/colaboradores/adicionar', ColaboradorController::class, 'novo');
$router->addRoute('POST', '/colaboradores/criar', ColaboradorController::class, 'criar');
$router->addRoute('GET', '/registrarponto', PontoController::class, 'index');
$router->addRoute('POST', '/registrarponto/salvar', PontoController::class, 'registrar');
$router->addRoute('GET', '/colaboradores', ColaboradorController::class, 'listar');


// --- Rotas de Gestão de Vagas ---
$router->addRoute('GET', '/vagas/listar', GestaoVagasController::class, 'listarVagas');
$router->addRoute('GET', '/vagas/criar', GestaoVagasController::class, 'criar');
$router->addRoute('POST', '/vagas/salvar', GestaoVagasController::class, 'salvar');
$router->addRoute('POST', '/vagas/candidatos', GestaoVagasController::class, 'verCandidatos');

// Benefícios
$router->addRoute('GET', '/beneficios', BeneficioController::class, 'index');
$router->addRoute('POST', '/beneficios/criar', BeneficioController::class, 'criar');
$router->addRoute('POST', '/beneficios/editar', BeneficioController::class, 'editar');
$router->addRoute('GET', '/beneficios/desativar/{id}', BeneficioController::class, 'desativar');

$router->addRoute('GET', '/meus_beneficios', BeneficioController::class, 'meusBeneficios'); 


// --- Rotas de Candidatura e IA ---
$router->addRoute('GET', '/vagas', CandidaturaController::class, 'listar');
$router->addRoute('POST', '/candidatura/formulario', CandidaturaController::class, 'exibirFormulario');
$router->addRoute('POST', '/candidatura/aplicar', CandidaturaController::class, 'aplicar');
$router->addRoute('POST', '/candidatura/analisar', CandidaturaController::class, 'analisarCurriculo');
$router->addRoute('POST', '/candidatura/ver-analise', CandidaturaController::class, 'exibirAnaliseIA');
$router->addRoute('GET', '/candidatura/ver-analise', CandidaturaController::class, 'exibirAnaliseIA');

// --- Rotas de Redirecionamento ---
$router->addRoute('GET', '/candidatura/formulario', CandidaturaController::class, 'redirecionarParaVagas');
$router->addRoute('GET', '/candidatura', CandidaturaController::class, 'redirecionarParaVagas');

// ----------------------
// Holerites
// ----------------------
// Rota para a página de listagem de holerites do colaborador
$router->addRoute('GET', '/meus-holerites', HoleriteController::class, 'index');
$router->addRoute('GET', '/holerite/pdf/{id}', HoleriteController::class, 'gerarPDF');

$router->addRoute('GET', '/folha/processar', FolhaPagamentoController::class, 'index');
$router->addRoute('POST', '/folha/processar', FolhaPagamentoController::class, 'processar');

// Rotas Comuns
//$router->addRoute('GET', '/thank_you', Controller::class, 'show_thank_you');
//$router->addRoute('GET', '/error', Controller::class, 'show_error');

// ----------------------
// Inicia o roteamento
// ----------------------
$router->getRoutes();