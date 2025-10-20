<?php

require_once __DIR__ . '/../vendor/autoload.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

session_start();

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/config.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

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
use App\Controller\AuthController;

// registro de rotas
$router = new Router();

// --- Rotas de Autenticação ---
$router->addRoute('GET', '/', AuthController::class, 'showLogin');
$router->addRoute('GET', '/login', AuthController::class, 'showLogin');
$router->addRoute('POST', '/login', AuthController::class, 'processLogin');
$router->addRoute('GET', '/cadastro', AuthController::class, 'showCadastro');
$router->addRoute('POST', '/register', AuthController::class, 'register');
$router->addRoute('GET', '/verify', AuthController::class, 'verifyAccount');
$router->addRoute('GET', '/registro-sucesso', AuthController::class, 'showRegistroSucesso');
$router->addRoute('GET', '/esqueceu-senha', AuthController::class, 'showForgotPasswordForm');// Exibe a página "Esqueci minha senha"
$router->addRoute('POST', '/solicitar-recuperacao', AuthController::class, 'handleForgotPasswordRequest');
$router->addRoute('GET', '/redefinir-senha', AuthController::class, 'showResetPasswordForm');// Exibe a página para o usuário definir a nova senha (acessada pelo link no e-mail)
$router->addRoute('POST', '/atualizar-senha', AuthController::class, 'handleResetPassword');

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
$router->addRoute('GET', '/vagas/editar', GestaoVagasController::class, 'editar');
$router->addRoute('POST', '/vagas/atualizar', GestaoVagasController::class, 'atualizar');
$router->addRoute('GET', '/vagas/excluir', GestaoVagasController::class, 'excluir');

// Benefícios
$router->addRoute('GET', '/beneficios', BeneficioController::class, 'index');
$router->addRoute('POST', '/beneficios/criar', BeneficioController::class, 'criar');
$router->addRoute('POST', '/beneficios/editar', BeneficioController::class, 'editar');
$router->addRoute('GET', '/beneficios/desativar', BeneficioController::class, 'desativar');

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
$router->addRoute('POST', '/holerite/pdf', HoleriteController::class, 'gerarPDF');

$router->addRoute('GET', '/folha/processar', FolhaPagamentoController::class, 'index');
$router->addRoute('POST', '/folha/processar', FolhaPagamentoController::class, 'processar');

// Rotas Comuns
//$router->addRoute('GET', '/thank_you', Controller::class, 'show_thank_you');
//$router->addRoute('GET', '/error', Controller::class, 'show_error');

// ----------------------
// Inicia o roteamento
// ----------------------
$router->getRoutes();