<?php
declare(strict_types=1);

// --- MODO DEBUG ATIVADO ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// --------------------------

ob_start();

require_once __DIR__ . '/../vendor/autoload.php';

session_start();

define('BASE_PATH', dirname(__DIR__));

// Carrega Config
if (file_exists(BASE_PATH . '/config.php')) {
    require_once BASE_PATH . '/config.php';
} else {
    die("ERRO CRÍTICO: Arquivo config.php não encontrado em " . BASE_PATH);
}

// Carrega Dotenv
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

use App\Core\Router;
use App\Core\Database;
use App\Model;
use App\Controller;
use PHPMailer\PHPMailer\PHPMailer;

// 1. CONEXÃO E SERVIÇOS
try {
    $pdo = Database::createConnection();

    $mailer = new PHPMailer(true);
    // Configuração de e-mail omitida para brevidade, mas mantenha se necessário
} catch (\Exception $e) {
    die("<div style='background:#fcc;padding:20px;'>ERRO DE CONEXÃO: " . $e->getMessage() . "</div>");
}

// 2. ROTAS
$router = new Router();

// --- AUTH ---
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

// --- DASHBOARD ---
$router->addRoute('GET', '/inicio', Controller\DashboardController::class, 'index');

// --- COLABORADORES ---
$router->addRoute('GET', '/colaboradores', Controller\ColaboradorController::class, 'listar');
$router->addRoute('GET', '/colaboradores/adicionar', Controller\ColaboradorController::class, 'novo');
$router->addRoute('POST', '/colaboradores/criar', Controller\ColaboradorController::class, 'criar');
$router->addRoute('GET', '/colaboradores/buscarDados', Controller\ColaboradorController::class, 'buscarDados');
$router->addRoute('POST', '/colaboradores/atualizar', Controller\ColaboradorController::class, 'atualizar');
$router->addRoute('POST', '/colaboradores/toggleStatus', Controller\ColaboradorController::class, 'toggleStatus');

// --- PONTO ---
$router->addRoute('GET', '/registrarponto', Controller\PontoController::class, 'index');
$router->addRoute('POST', '/registrarponto/salvar', Controller\PontoController::class, 'registrar');
$router->addRoute('POST', '/ponto/registrar-face-api', Controller\PontoController::class, 'registrarFace');

// --- GESTÃO FACIAL ---
$router->addRoute('GET', '/gestao-facial', Controller\GestaoFacialController::class, 'index');
$router->addRoute('POST', '/gestao-facial/resetar', Controller\GestaoFacialController::class, 'resetar');

// --- GESTÃO DE VAGAS ---
$router->addRoute('GET', '/vagas/listar', Controller\GestaoVagasController::class, 'listarVagas');

// --- API VAGAS ---
$router->addRoute('GET', '/api/vagas/listar', Controller\VagaApiController::class, 'listarVagas');
$router->addRoute('POST', '/api/vagas/salvar', Controller\VagaApiController::class, 'salvar');
$router->addRoute('GET', '/api/vagas/editar', Controller\VagaApiController::class, 'editar');
$router->addRoute('POST', '/api/vagas/atualizar', Controller\VagaApiController::class, 'atualizar');
$router->addRoute('GET', '/api/vagas/excluir', Controller\VagaApiController::class, 'excluir');
$router->addRoute('GET', '/api/vagas/candidatos', Controller\VagaApiController::class, 'verCandidatos');

// --- BENEFÍCIOS ---
$router->addRoute('GET', '/beneficios', Controller\BeneficioController::class, 'gerenciamento');
$router->addRoute('POST', '/beneficios/deletar', Controller\BeneficioController::class, 'deletarBeneficio');
$router->addRoute('POST', '/beneficios/salvar', Controller\BeneficioController::class, 'salvarBeneficio');
$router->addRoute('POST', '/beneficios/toggleStatus', Controller\BeneficioController::class, 'toggleStatus');
$router->addRoute('POST', '/beneficios/regras/salvar', Controller\BeneficioController::class, 'salvarRegrasAtribuicao');
$router->addRoute('GET', '/meus-beneficios', Controller\BeneficioController::class, 'meusBeneficios');

// --- BENEFÍCIOS (EXCEÇÕES) ---
$router->addRoute('POST', '/colaborador/buscar', Controller\ColaboradorController::class, 'buscarAjax');
$router->addRoute('POST', '/colaborador/beneficios/carregar', Controller\BeneficioController::class, 'carregarPorColaborador');
$router->addRoute('POST', '/colaborador/beneficios/salvar', Controller\BeneficioController::class, 'salvarPorColaborador');

// --- CANDIDATURA ---
$router->addRoute('GET', '/vagas', Controller\CandidaturaController::class, 'listar');
$router->addRoute('GET', '/candidatura', Controller\CandidaturaController::class, 'listar');
$router->addRoute('POST', '/candidatura/formulario', Controller\CandidaturaController::class, 'exibirFormulario');
$router->addRoute('POST', '/candidatura/aplicar', Controller\CandidaturaController::class, 'aplicar');
$router->addRoute('POST', '/candidatura/analisar', Controller\CandidaturaController::class, 'analisarCurriculo');
$router->addRoute('GET', '/candidatura/ver-analise', Controller\CandidaturaController::class, 'exibirAnaliseIA');
$router->addRoute('POST', '/candidatura/ver-analise', Controller\CandidaturaController::class, 'exibirAnaliseIA');

// --- FOLHA E HOLERITES ---
$router->addRoute('GET', '/meus-holerites', Controller\HoleriteController::class, 'index');
$router->addRoute('POST', '/holerite/gerarPDF', Controller\HoleriteController::class, 'gerarPDF');
$router->addRoute('GET', '/folha/processar', Controller\FolhaPagamentoController::class, 'index');
$router->addRoute('POST', '/folha/processar', Controller\FolhaPagamentoController::class, 'processar');

// --- ROTAS DE EMPRESA ---
$router->addRoute('GET', '/api/empresas/listar', Controller\EmpresaController::class, 'listar');
$router->addRoute('POST', '/api/empresas/trocar', Controller\EmpresaController::class, 'trocar');
$router->addRoute('POST', '/api/empresas/salvar', Controller\EmpresaController::class, 'salvar');

// 3. RESOLUÇÃO DA URL
$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = dirname($_SERVER['SCRIPT_NAME']);

// Remove base path (se estiver em subpasta)
if ($basePath !== '/' && strpos($uri, $basePath) === 0) {
    $uri = substr($uri, strlen($basePath));
}
$requestUri = $uri ?: '/';

// Tenta casar a rota
$route = $router->match($requestMethod, $requestUri);

// --- DIAGNÓSTICO DE ROTA NÃO ENCONTRADA (404) ---
if ($route === null) {
    ob_clean(); // Limpa qualquer saída anterior
    echo "<div style='background:#fff3cd; color:#856404; padding:20px; border:1px solid #ffeeba; font-family:monospace; margin:20px;'>";
    echo "<h1>DIAGNÓSTICO: ROTA NÃO ENCONTRADA (404)</h1>";
    echo "<p>O sistema não encontrou um Controller para a URL acessada.</p>";
    echo "<ul>";
    echo "<li><strong>URL Recebida:</strong> " . htmlspecialchars($requestUri) . "</li>";
    echo "<li><strong>Método:</strong> " . htmlspecialchars($requestMethod) . "</li>";
    echo "<li><strong>BasePath Detectado:</strong> " . htmlspecialchars($basePath) . "</li>";
    echo "</ul>";
    echo "<h3>Verifique se a rota está registrada acima com a grafia correta.</h3>";
    echo "</div>";
    exit;
}

// 4. DISPATCHER (Criação dos Controllers)
$controllerName = $route['controller'];
$action = $route['action'];
$params = $route['params'];

try {
    $controller = null;
    $colaboradorModel = new \App\Model\ColaboradorModel($pdo);
    $cargoModel = new \App\Model\CargoModel($pdo);
    $usuarioService = new \App\Service\Implementations\UsuarioService($colaboradorModel, $cargoModel);

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

            // 1. Cria o Cliente do Gemini com a chave
            $apiKey = $_ENV['GEMINI_API_KEY'] ?? '';
            $geminiClient = new \GeminiAPI\Client($apiKey);

            $controller = new $controllerName(
                new Model\CandidaturaModel($pdo),
                new Model\GestaoVagasModel($pdo),
                new Model\CandidatoModel($pdo),

                // 2. Passa o OBJETO cliente, e não a string solta
                new \App\Service\Implementations\AnalisadorCurriculoService($geminiClient),

                new \Smalot\PdfParser\Parser(),
                $pdo
            );
            break;

        case Controller\HoleriteController::class:
            $controller = new $controllerName(
                new Model\HoleriteModel($pdo),
                new Model\EmpresaModel($pdo),
                $pdo
            );
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
            // ATUALIZADO: Apenas 2 argumentos
            $controller = new $controllerName(new Model\GestaoVagasModel($pdo), $pdo);
            break;

        case Controller\PontoController::class:
            $controller = new $controllerName(new Model\PontoModel($pdo), new Model\ColaboradorModel($pdo), $pdo);
            break;

        case Controller\VagaApiController::class:
            $controller = new $controllerName(new Model\GestaoVagasModel($pdo), $pdo);
            break;

        case Controller\GestaoFacialController::class:
            $controller = new $controllerName(new Model\ColaboradorModel($pdo), $pdo);
            break;

        case Controller\EmpresaController::class:
            $controller = new $controllerName(new Model\EmpresaModel($pdo), $pdo);
            break;

        default:
            // Fallback genérico
            $controller = new $controllerName($pdo);
            break;
    }

    // Executa a ação
    call_user_func_array([$controller, $action], $params);

} catch (\Throwable $e) {
    // --- DIAGNÓSTICO DE ERRO FATAL (500) ---
    if (ob_get_level()) ob_clean(); // Limpa a tela
    echo "<div style='background:#f8d7da; color:#721c24; padding:20px; border:1px solid red; margin:20px; font-family:sans-serif;'>";
    echo "<h1>ERRO FATAL NO SISTEMA (500)</h1>";
    echo "<h3>" . $e->getMessage() . "</h3>";
    echo "<p><strong>Arquivo:</strong> " . $e->getFile() . " (Linha " . $e->getLine() . ")</p>";
    echo "<pre style='background:#fff; padding:10px; border:1px solid #ccc;'>" . $e->getTraceAsString() . "</pre>";
    echo "</div>";
    exit;
}

ob_end_flush();