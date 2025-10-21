<?php
// Garante que a constante BASE_URL esteja disponível
if (!defined('BASE_URL')) {
    // Defina um valor padrão ou lance um erro se não estiver definida
    define('BASE_URL', '/RHease/public');
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?? 'RH Ease' ?></title>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="<?= BASE_URL ?>/css/main.css">
</head>
<body>
<header>
    <i class="bi bi-list menu-toggle"></i>
    <img id="logo" src="<?= BASE_URL ?>/img/rhease-ease 1.png" alt="RHease" width="130">
</header>

<div class="main-container">
    <aside class="sidebar">
        <ul class="menu">
            <li class="<?= ($paginaAtiva ?? '') === 'painel' ? 'active' : '' ?>"><a href="#"><i class="bi bi-clipboard-data-fill"></i> Painel</a></li>
            <li class="<?= ($paginaAtiva ?? '') === 'colaboradores' ? 'active' : '' ?>"><a href="<?= BASE_URL ?>/colaboradores"><i class="bi bi-people-fill"></i> Colaboradores</a></li>
            <li class="<?= ($paginaAtiva ?? '') === 'beneficios' ? 'active' : '' ?>"><a href="<?= BASE_URL ?>/beneficios/gerenciamento"><i class="bi bi-shield-fill-check"></i> Benefícios</a></li>
            <li><a href="#"><i class="bi bi-person-lines-fill"></i> Contato</a></li>
        </ul>
    </aside>

    <main class="content">