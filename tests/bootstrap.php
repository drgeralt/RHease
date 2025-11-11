<?php
// tests/bootstrap.php

// 1. Carrega o autoloader do Composer (essencial!)
//    Isso garante que o \FPDF e o próprio PHPUnit funcionem.
require_once dirname(__DIR__) . '/vendor/autoload.php';

// 2. Define as constantes globais que seu app espera
//    __DIR__ é a pasta 'tests'
//    dirname(__DIR__) é a raiz do projeto (C:\xampp\htdocs\RHease)
define('BASE_PATH', dirname(__DIR__));

// Define outras constantes que seu app possa precisar
define('BASE_URL', 'http://localhost-test'); // Um valor falso, só para o teste
?>