<?php
/**
 * Bootstrap para Testes PHPUnit
 * Este arquivo é carregado ANTES de todos os testes
 */

// Carrega o autoload do Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Define constantes necessárias para os testes
if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost/rhease');
}

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

// Carrega variáveis de ambiente (opcional, mas útil)
// Se o .env não existir, define valores padrão para testes
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->safeLoad(); // safeLoad não dá erro se já estiver carregado
}

// Define variáveis de ambiente para testes (se não estiverem definidas)
$_ENV['MAIL_HOST'] = $_ENV['MAIL_HOST'] ?? 'smtp.example.com';
$_ENV['MAIL_USERNAME'] = $_ENV['MAIL_USERNAME'] ?? 'test@example.com';
$_ENV['MAIL_PASSWORD'] = $_ENV['MAIL_PASSWORD'] ?? 'fake_password';
$_ENV['MAIL_ENCRYPTION'] = $_ENV['MAIL_ENCRYPTION'] ?? 'tls';
$_ENV['MAIL_PORT'] = $_ENV['MAIL_PORT'] ?? '587';
$_ENV['MAIL_FROM_NAME'] = $_ENV['MAIL_FROM_NAME'] ?? 'RHease Testing';

// Inicia sessão se necessário
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}