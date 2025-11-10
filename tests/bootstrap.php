<?php

/**
 * Bootstrap para testes PHPUnit
 * Carrega o autoloader e define constantes necessárias
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Define constantes para os testes
define('BASE_PATH', dirname(__DIR__));
define('TEST_MODE', true);

// Configurações do banco (não serão usadas nos testes unitários com mocks)
if (!defined('DB_HOST')) {
    define('DB_HOST', 'localhost');
}
if (!defined('DB_USER')) {
    define('DB_USER', 'root');
}
if (!defined('DB_PASS')) {
    define('DB_PASS', '');
}
if (!defined('DB_NAME')) {
    define('DB_NAME', 'rhease');
}

