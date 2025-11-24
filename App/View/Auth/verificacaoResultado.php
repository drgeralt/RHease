<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Verificação de Conta</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/auth.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="login-container">
    <div class="content-box">
        <img src="<?= BASE_URL ?>/img/rhease-ease 1.png" alt="Logo RH Ease" class="logo" style="margin-bottom: 20px;">

        <p style="margin-bottom: 30px; color: var(--text-color-dark);"><?= htmlspecialchars($message); ?></p>

        <?php if ($success): ?>
            <a href="<?= BASE_URL ?>/login" class="btn full-width" style="text-decoration: none; display: inline-block;">IR PARA LOGIN</a>

        <?php else: ?>

            <?php
            // Verificamos se a mensagem de erro contém a palavra "expirou"
            $isExpired = (isset($message) && strpos(strtolower($message), 'expirou') !== false);
            ?>

            <?php if ($isExpired): ?>
                <p style="margin-top:1rem; color: var(--text-color-light);">Para reenviar o link de verificação, clique abaixo:</p>
                <a href="<?= BASE_URL ?>/reenviar-verificacao" class="btn-cancelar" style="text-decoration: none; display: inline-block; width: 100%; text-align: center; box-sizing: border-box;">REENVIAR E-MAIL</a>
            <?php else: ?>
                <a href="<?= BASE_URL ?>/cadastro" class="btn-cancelar" style="text-decoration: none; display: inline-block; width: 100%; text-align: center; box-sizing: border-box;">TENTAR NOVAMENTE</a>
            <?php endif; ?>

        <?php endif; ?>
    </div>
</div>
</body>
</html>