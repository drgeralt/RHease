<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - RH ease</title>
    <link rel="stylesheet" href="css/login.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <div class="login-container">
        <header class="login-header">
            <img src="img/rhease-ease%201.png" alt="Logo RH ease" class="logo">
            <h1>Acesse a sua conta</h1>
        </header>

        <form class="login-form">
            <div class="input-group">
                <label for="email">Login</label>
                <div class="input-wrapper icon-email">
                    <input type="email" id="email" placeholder="Seu e-mail" required>
                </div>
            </div>

            <div class="input-group">
                <label for="password">Senha</label>
                <div class="input-wrapper icon-password">
                    <input type="password" id="password" placeholder="Insira sua senha aqui" required>
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
                <button type="button" class="btn btn-secondary">Entrar como Gestor</button>
            </div>
        </form>

        <footer class="login-footer">
            <p>Não tem uma conta?</p>
            <a href="../App/View/Login/paginaCadastroPlataforma.php">Cadastre-se aqui.</a>
        </footer>
    </div>

</body>
</html>