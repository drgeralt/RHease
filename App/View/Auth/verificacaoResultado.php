<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Verificação de Conta</title>

    <!-- Fontes -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Estilos (Global + Auth) -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/auth.css">
</head>
<body>

<div class="login-container">

    <!-- Cabeçalho Padronizado (Igual Login) -->
    <header class="login-header">
        <img src="<?= BASE_URL ?>/img/rhease-ease 1.png" alt="Logo RH Ease" class="logo">

        <!-- Título Dinâmico baseado no sucesso -->
        <h1 style="font-size: 1.5rem; margin-bottom: 1rem;">
            <?= $success ? 'Conta Verificada!' : 'Atenção' ?>
        </h1>
    </header>

    <!-- Mensagem -->
    <p style="margin-bottom: 30px; color: var(--text-color-dark); font-size: 1rem; line-height: 1.5;">
        <?= htmlspecialchars($message); ?>
    </p>

    <!-- Ações -->
    <?php if ($success): ?>

        <a href="<?= BASE_URL ?>/login" class="btn full-width" style="text-decoration: none; display: block; text-align: center;">
            IR PARA LOGIN
        </a>

    <?php else: ?>

        <?php
        // Verifica se é erro de expiração para oferecer reenvio
        $isExpired = (isset($message) && stripos($message, 'expirou') !== false);
        ?>

        <?php if ($isExpired): ?>
            <p style="margin-bottom: 15px; font-size: 0.9rem; color: var(--text-color-light);">
                O link venceu? Solicite um novo abaixo:
            </p>
            <a href="<?= BASE_URL ?>/reenviar-verificacao" class="btn full-width" style="background-color: var(--text-color-light); text-decoration: none; display: block; text-align: center;">
                REENVIAR E-MAIL
            </a>
        <?php else: ?>
            <a href="<?= BASE_URL ?>/cadastro" class="btn-cancelar" style="text-decoration: none; display: block; text-align: center;">
                TENTAR NOVAMENTE
            </a>
        <?php endif; ?>

    <?php endif; ?>

</div>

</body>
</html>