<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - RH ease</title>
    <link rel="stylesheet" href="/RHease/public/css/login.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>

<div class="login-container">
    <header class="login-header">
        <img src="/RHease/public/img/rhease-ease 1.png" alt="Logo RH ease" class="logo">
        <h1>Acesse a sua conta</h1>
    </header>

    <?php if (isset($data['error'])): ?>
        <div class="error-message"><?php echo htmlspecialchars($data['error']); ?></div>
    <?php endif; ?>

    <form class="login-form" method="POST" action="<?= BASE_URL ?>/login">
        <div class="input-group">
            <label for="email">Login</label>
            <div class="input-wrapper icon-email">
                <input type="email" id="email" name="email_profissional" placeholder="Seu e-mail" required>
            </div>
        </div>

        <div class="input-group">
            <label for="password">Senha</label>
            <div class="input-wrapper icon-password">
                <input type="password" id="password" name="senha" placeholder="Insira sua senha aqui" required>
            </div>
        </div>

        <div class="options">
            <div class="remember-me">
                <input type="checkbox" id="remember">
                <label for="remember">Lembre-me</label>
            </div>
            <a href="#" class="forgot-password">Esqueceu sua senha?</a>
        </div>

        <div class="button-group">
            <button type="submit" class="btn btn-primary">Entrar como Colaborador</button>
            <a href="/RHease/public/colaboradores" class="btn btn-secondary" role="button">Entrar como Gestor</a>
        </div>
    </form>

    <footer class="login-footer">
        <p>Não tem uma conta?</p>
        <a href="/RHease/public/cadastro">Cadastre-se aqui.</a>
    </footer>
</div>

</body>
</html>