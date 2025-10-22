<?php
require_once __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Suporte para configuração local: crie `config.local.php` ao usar XAMPP e sobrescreva constantes.
if (file_exists(__DIR__ . '/config.local.php')) {
	require_once __DIR__ . '/config.local.php';
}

// Configurações do Banco de Dados (valores padrão). Podem ser sobrescritos por config.local.php
if (!defined('DB_HOST')) {
	define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
}
if (!defined('DB_USER')) {
	define('DB_USER', getenv('DB_USER') ?: 'root');
}
if (!defined('DB_PASS')) {
	define('DB_PASS', getenv('DB_PASS') ?: '');
}
if (!defined('DB_NAME')) {
	define('DB_NAME', getenv('DB_NAME') ?: 'rhease');
}

// Configuração da URL base
if (!defined('BASE_URL')) {
    define('BASE_URL', getenv('BASE_URL') ?: 'http://localhost/RHease/public');
}
?>
