<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - RH ease</title>
    <link rel="stylesheet" href="/RHease/public/css/login.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>

<div class="login-container">
    <header class="login-header">
        <img src="/RHease/public/img/rhease-ease 1.png" alt="Logo RH ease" class="logo">
        <h1>Cadastre-se na plataforma</h1>
    </header>

    <form class="login-form" id="cadastro-form">
        <div class="input-group">
            <label for="fullname">Nome Completo</label>
            <div class="input-wrapper icon-user">
                <input type="text" id="fullname" name="nome_completo" placeholder="Seu nome" required>
            </div>
        </div>

        <div class="input-group">
            <label for="cpf">CPF</label>
            <div class="input-wrapper icon-cpf">
                <input type="text" id="cpf" name="cpf" placeholder="000.000.000-00" required>
            </div>
        </div>

        <div class="input-group">
            <label for="email">Login (E-mail Profissional)</label>
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

        <ul class="password-rules">
            <li>A senha deve conter:</li>
            <li>No mínimo 8 caracteres.</li>
            <li>Pelo menos uma letra maiúscula.</li>
            <li>Pelo menos um número.</li>
            <li>Pelo menos um caractere especial (ex: !@#$%&*).</li>
        </ul>

        <button type="submit" class="btn btn-primary full-width">Cadastrar</button>
    </form>

    <footer class="login-footer">
        <p>Já tem uma conta?</p>
        <a href="/RHease/public/login">Faça login</a>
    </footer>
</div>


<script src="/RHease/public/js/auth.js"></script>
</body>
</html>