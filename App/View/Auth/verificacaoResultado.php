<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Verificação de Conta</title>
    <link rel="stylesheet" href="/RHease/public/css/login.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="login-container">
    <div class="content-box">
        <img src="/RHease/public/img/rhease-ease 1.png" alt="Logo RH Ease" class="logo">

        <p><?= htmlspecialchars($message); ?></p>

        <?php if ($success): ?>
            <a href="/RHease/public/login" class="btn btn-primary">IR PARA LOGIN</a>
        <?php else: ?>
            <a href="/RHease/public/cadastro" class="btn btn-secondary">TENTAR NOVAMENTE</a>
        <?php endif; ?>
    </div>
</div>
</body>
</html>