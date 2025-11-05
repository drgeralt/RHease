<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Verificação de Conta</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/login.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="login-container">
    <div class="content-box">
        <img src="<?= BASE_URL ?>/img/rhease-ease 1.png" alt="Logo RH Ease" class="logo">

        <p><?= htmlspecialchars($message); ?></p>

        <?php if ($success): ?>
            <a href="<?= BASE_URL ?>/login" class="btn btn-primary">IR PARA LOGIN</a>

        <?php else: ?>

            <?php
            // Verificamos se a mensagem de erro contém a palavra "expirou"
            $isExpired = (isset($message) && strpos(strtolower($message), 'expirou') !== false);
            ?>

            <?php if ($isExpired): ?>
                <p style="margin-top:1rem;">Para reenviar o link de verificação, clique abaixo:</p>
                <a href="<?= BASE_URL ?>/reenviar-verificacao" class="btn btn-secondary">REENVIAR E-MAIL</a>
            <?php else: ?>
                <a href="<?= BASE_URL ?>/cadastro" class="btn btn-secondary">TENTAR NOVAMENTE</a>
            <?php endif; ?>

        <?php endif; ?>
    </div>
</div>
</body>
</html>