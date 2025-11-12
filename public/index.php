<?php
declare(strict_types=1);

ob_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';

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
use App\Controller\BeneficioController;
use App\Controller\HoleriteController;
use App\Controller\FolhaPagamentoController;
use App\Controller\DashboardController;
use App\Controller\AuthController;
use App\Controller\VagaApiController;
use App\Core\Router;
use App\Core\Database;
use App\Model;
use App\Controller;
use Smalot\PdfParser\Parser;
use App\Service\Implementations\AnalisadorCurriculoService;
use PHPMailer\PHPMailer\PHPMailer; // Importe o PHPMailer

// 1. INICIALIZAÇÃO ÚNICA DA CONEXÃO E SERVIÇOS
try {
    $pdo = Database::createConnection();

    // Cria a instância única do Mailer
    $mailer = new PHPMailer(true);
    $mailer->isSMTP();
    $mailer->Host = $_ENV['MAIL_HOST'];
    $mailer->SMTPAuth = true;
    $mailer->Username = $_ENV['MAIL_USERNAME'];
    $mailer->Password = $_ENV['MAIL_PASSWORD'];
    $mailer->SMTPSecure = $_ENV['MAIL_ENCRYPTION'];
    $mailer->Port = (int)$_ENV['MAIL_PORT'];

} catch (\Exception $e) {
    die("Erro fatal na inicialização: " . $e->getMessage());
}

// 2. REGISTRO DE ROTAS
$router = new Router();

// --- Rotas de Autenticação (Unificadas no AuthController) ---
$router->addRoute('GET', '/', Controller\AuthController::class, 'showLogin');
$router->addRoute('GET', '/login', Controller\AuthController::class, 'showLogin');
$router->addRoute('POST', '/login', Controller\AuthController::class, 'processLogin');
$router->addRoute('GET', '/cadastro', Controller\AuthController::class, 'showCadastro');
$router->addRoute('POST', '/register', Controller\AuthController::class, 'register');
$router->addRoute('GET', '/registro-sucesso', Controller\AuthController::class, 'showRegistroSucesso');
$router->addRoute('GET', '/verify', Controller\AuthController::class, 'verifyAccount');
$router->addRoute('GET', '/esqueceu-senha', Controller\AuthController::class, 'showForgotPasswordForm');
$router->addRoute('POST', '/solicitar-recuperacao', Controller\AuthController::class, 'handleForgotPasswordRequest');
$router->addRoute('GET', '/redefinir-senha', Controller\AuthController::class, 'showResetPasswordForm');
$router->addRoute('POST', '/atualizar-senha', Controller\AuthController::class, 'handleResetPassword');
// Removidas as rotas do UserController (show_reenviar_verificacao)

// --- Rota de Home/Dashboard ---
$router->addRoute('GET', '/inicio', Controller\DashboardController::class, 'index');

// --- Rotas de Colaboradores ---
$router->addRoute('GET', '/colaboradores/adicionar', Controller\ColaboradorController::class, 'novo');
$router->addRoute('POST', '/colaboradores/criar', Controller\ColaboradorController::class, 'criar');
$router->addRoute('GET', '/colaboradores', Controller\ColaboradorController::class, 'listar');

// --- Rota de Ponto ---
$router->addRoute('GET', '/registrarponto', Controller\PontoController::class, 'index');
$router->addRoute('POST', '/registrarponto/salvar', Controller\PontoController::class, 'registrar');

// --- Rotas de Gestão de Vagas (Admin) ---
$router->addRoute('GET', '/vagas/listar', Controller\GestaoVagasController::class, 'listarVagas');

// --- Rotas da API de Vagas (Para JS) ---
$router->addRoute('GET', '/api/vagas/listar', Controller\VagaApiController::class, 'listarVagas');
$router->addRoute('POST', '/api/vagas/salvar', Controller\VagaApiController::class, 'salvar');
$router->addRoute('GET', '/api/vagas/editar', Controller\VagaApiController::class, 'editar');
$router->addRoute('POST', '/api/vagas/atualizar', Controller\VagaApiController::class, 'atualizar');
$router->addRoute('GET', '/api/vagas/excluir', Controller\VagaApiController::class, 'excluir');
$router->addRoute('GET', '/api/vagas/candidatos', Controller\VagaApiController::class, 'verCandidatos');

// --- Rotas de Benefícios ---
$router->addRoute('GET', '/beneficios', Controller\BeneficioController::class, 'gerenciamento');
$router->addRoute('POST', '/beneficios/deletar', Controller\BeneficioController::class, 'deletarBeneficio');
$router->addRoute('POST', '/beneficios/salvar', Controller\BeneficioController::class, 'salvarBeneficio');
$router->addRoute('POST', '/colaborador/beneficios/salvar', Controller\BeneficioController::class, 'salvarBeneficiosColaborador');
$router->addRoute('POST', '/beneficios/regras/salvar', Controller\BeneficioController::class, 'salvarRegrasAtribuicao');
$router->addRoute('POST', '/beneficios/toggleStatus', Controller\BeneficioController::class, 'toggleStatus');
$router->addRoute('GET', '/meus-beneficios', Controller\BeneficioController::class, 'meusBeneficios');
// Removidas rotas duplicadas de /beneficios/criar e /editar

// --- Rotas de Candidatura (Público) ---
$router->addRoute('GET', '/vagas', Controller\CandidaturaController::class, 'listar');
$router->addRoute('POST', '/candidatura/formulario', Controller\CandidaturaController::class, 'exibirFormulario');
$router->addRoute('POST', '/candidatura/aplicar', Controller\CandidaturaController::class, 'aplicar');
$router->addRoute('POST', '/candidatura/analisar', Controller\CandidaturaController::class, 'analisarCurriculo');
$router->addRoute('GET', '/candidatura/ver-analise', Controller\CandidaturaController::class, 'exibirAnaliseIA');
// Removidas rotas duplicadas de /candidatura e /ver-analise (POST)

// --- Rotas de Holerites e Folha ---
$router->addRoute('GET', '/meus-holerites', Controller\HoleriteController::class, 'index');
$router->addRoute('POST', '/holerite/gerarPDF', Controller\HoleriteController::class, 'gerarPDF');
$router->addRoute('GET', '/folha/processar', Controller\FolhaPagamentoController::class, 'index');
$router->addRoute('POST', '/folha/processar', Controller\FolhaPagamentoController::class, 'processar');

// 3. PROCESSAMENTO E EXECUÇÃO DA ROTA
$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = dirname($_SERVER['SCRIPT_NAME']);
if ($basePath !== '/' && strpos($uri, $basePath) === 0) {
    $uri = substr($uri, strlen($basePath));
}
$requestUri = $uri ?: '/';

// Usa o método 'match' do Router refatorado
$route = $router->match($requestMethod, $requestUri);

if ($route === null) {
    http_response_code(404);
    require BASE_PATH . '/App/View/Common/error.php';
    exit;
}

$controllerName = $route['controller'];
$action = $route['action'];
$params = $route['params'];

try {
    $controller = null;

    // 4. "FÁBRICA" DE CONTROLLERS (INJEÇÃO DE DEPENDÊNCIA)
    switch ($controllerName) {

        case Controller\AuthController::class:
            $controller = new $controllerName(new Model\AuthModel($pdo, $mailer), $pdo);
            break;

        case Controller\DashboardController::class:
            $controller = new $controllerName(
                new Model\DashboardModel($pdo),
                new Model\ColaboradorModel($pdo),
                $pdo
            );
            break;

        case Controller\ColaboradorController::class:
            $controller = new $controllerName(
                new Model\ColaboradorModel($pdo),
                new Model\EnderecoModel($pdo),
                new Model\CargoModel($pdo),
                new Model\SetorModel($pdo),
                $pdo
            );
            break;

        case Controller\BeneficioController::class:
            $controller = new $controllerName(new Model\BeneficioModel($pdo), $pdo);
            break;

        case Controller\CandidaturaController::class:
            $controller = new $controllerName(
                new Model\CandidaturaModel($pdo),
                new Model\GestaoVagasModel($pdo),
                new Model\CandidatoModel($pdo),
                new AnalisadorCurriculoService($_ENV['GEMINI_API_KEY']),
                new Parser(),
                $pdo
            );
            break;

        case Controller\HoleriteController::class:
            $controller = new $controllerName(new Model\HoleriteModel($pdo), $pdo);
            break;

        case Controller\FolhaPagamentoController::class:
            $service = new \App\Service\Implementations\FolhaPagamentoService(
                new Model\FolhaPagamentoModel($pdo),
                new Model\ColaboradorModel($pdo),
                new Model\ParametrosFolhaModel($pdo),
                new \App\Service\Implementations\PontoService($pdo)
            );
            $controller = new $controllerName($service, $pdo);
            break;

        case Controller\GestaoVagasController::class:
            $controller = new $controllerName(
                new Model\GestaoVagasModel($pdo),
                new Model\CandidaturaModel($pdo),
                $pdo
            );
            break;

        case Controller\PontoController::class:
            $controller = new $controllerName(
                new Model\PontoModel($pdo),
                new Model\ColaboradorModel($pdo),
                $pdo
            );
            break;

        case Controller\VagaApiController::class:
            $controller = new $controllerName(
                new Model\GestaoVagasModel($pdo),
                new Model\CandidaturaModel($pdo),
                new Model\SetorModel($pdo),
                new Model\NovaVagaModel($pdo),
                $pdo
            );
            break;

        // Controllers que foram deletados ou que não têm dependências
        case Controller\UserController::class:
            // O UserController não existe mais, redireciona para o AuthController
            $authModel = new Model\AuthModel($pdo, $mailer);
            $controller = new Controller\AuthController($authModel, $pdo);
            // Mapeia a ação do UserController para a ação correta no AuthController
            $actionMap = [
                'show_login' => 'showLogin',
                'process_login' => 'processLogin',
                'show_cadastro' => 'showCadastro',
                'register' => 'register',
                'show_registro_sucesso' => 'showRegistroSucesso',
                'verify_account' => 'verifyAccount'
            ];
            $action = $actionMap[$action] ?? 'showLogin';
            break;

        default:
            // Para controllers simples que só herdam de Core\Controller
            $controller = new $controllerName($pdo);
            break;
    }

    // 5. EXECUTA A AÇÃO DO CONTROLLER
    call_user_func_array([$controller, $action], $params);

} catch (\Exception $e) {
    error_log($e->getMessage());
    require BASE_PATH . '/App/View/Common/error.php';
}

ob_end_flush();