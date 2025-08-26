<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rhease - Sistema RH</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
</head>
<body>
    <header>
        <h1>Rhease</h1>
        <nav>
            <a href="<?= BASE_URL ?>/beneficios">Benefícios</a>
            <a href="<?= BASE_URL ?>/demissao">Demissão</a>
            <a href="<?= BASE_URL ?>/funcionarios/demitidos">Demitidos</a> </nav>
    </header>
    <main class="container">
        <?php if (isset($_SESSION['flash_success'])) : ?>
            <div class="flash-message success"><?= $_SESSION['flash_success'] ?></div>
            <?php unset($_SESSION['flash_success']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['flash_error'])) : ?>
            <div class="flash-message error"><?= $_SESSION['flash_error'] ?></div>
            <?php unset($_SESSION['flash_error']); ?>
        <?php endif; ?>