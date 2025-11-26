<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro Realizado com Sucesso</title>

    <!-- Fontes -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Estilos -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/auth.css">
</head>
<body>

<div class="login-container">

    <!-- Cabeçalho Padronizado -->
    <header class="login-header">
        <img src="<?= BASE_URL ?>/img/rhease-ease 1.png" alt="Logo RH Ease" class="logo">
        <h1 style="font-size: 1.5rem; margin-bottom: 1rem;">Cadastro Realizado!</h1>
    </header>

    <!-- Mensagem -->
    <p style="margin-bottom: 30px; color: var(--text-color-dark); font-size: 1rem; line-height: 1.5;">
        Você irá receber um link de ativação no e-mail fornecido. Após receber o e-mail, clique no link para ativar sua conta e fazer login.
    </p>

    <!-- Ação -->
    <a href="<?= BASE_URL ?>/login" class="btn full-width" style="text-decoration: none; display: block; text-align: center;">
        VOLTAR PARA O LOGIN
    </a>
</div>

</body>
</html>