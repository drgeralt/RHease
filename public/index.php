<?php
// Código para forçar a exibição de todos os erros do PHP
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Inicia a sessão para usar as mensagens de feedback
session_start();

// Define o caminho base do projeto
define('BASE_PATH', dirname(__DIR__));
define('BASE_URL', '/Rhease/public'); // Defina a URL base do seu projeto aqui

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
$router->addRoute('GET', '/job-application', 'JobController', 'show_job');
$router->addRoute('POST', '/submit-application', 'JobController', 'store_application');
$router->addRoute('GET', '/applications', 'JobController', 'show_applications');
$router->addRoute('GET', '/view-bio/{id}', 'JobController', 'show_bio');
$router->addRoute('GET', '/application/edit/{id}', 'JobController', 'show_update_form');
$router->addRoute('POST', '/application/update/{id}', 'JobController', 'process_update');
$router->addRoute('GET', '/delete-application/{id}', 'JobController', 'delete_application');

// Rotas de Payroll (Folha de Pagamento)
$router->addRoute('GET', '/payroll/add', PayrollController::class, 'show_payroll_form');
$router->addRoute('POST', '/payroll', PayrollController::class, 'store_payroll');
$router->addRoute('GET', '/payrolls', PayrollController::class, 'show_payrolls');
$router->addRoute('POST', '/payroll/remove', PayrollController::class, 'remove_payroll');

// Rotas Comuns
$router->addRoute('GET', '/thank_you', CommonController::class, 'show_thank_you');
$router->addRoute('GET', '/error', CommonController::class, 'show_error');

// ROTAS - MÓDulos DE BENEFÍCIOS E DEMISSÃO
$router->addRoute('GET', '/beneficios', 'BeneficioController', 'index');
$router->addRoute('GET', '/beneficios/criar', 'BeneficioController', 'create');
$router->addRoute('POST', '/beneficios', 'BeneficioController', 'store');
$router->addRoute('GET', '/beneficios/editar/(\d+)', 'BeneficioController', 'edit');
$router->addRoute('POST', '/beneficios/editar/(\d+)', 'BeneficioController', 'update');
$router->addRoute('POST', '/beneficios/deletar/(\d+)', 'BeneficioController', 'destroy');
$router->addRoute('GET', '/demissao', 'DemissaoController', 'showIniciarForm');
$router->addRoute('POST', '/demissao', 'DemissaoController', 'processar');
$router->addRoute('GET', '/demissao/resumo/(\d+)', 'DemissaoController', 'verResumo');
$router->addRoute('GET', '/funcionarios/demitidos', 'FuncionarioController', 'listarDemitidos');

// Rotas de Comunicação Interna
$router->addRoute('GET', '/comunicacao', InternalCommunicationController::class, 'index');
$router->addRoute('POST', '/comunicacao/enviar', InternalCommunicationController::class, 'sendMessage');

// ----------------------
// Inicia o roteamento
// ----------------------
$router->getRoutes();