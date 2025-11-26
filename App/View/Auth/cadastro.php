<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - RH ease</title>

    <!-- Fontes -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Estilos -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/auth.css">
</head>
<body>

<div class="login-container">
    <header class="login-header">
        <img src="<?= BASE_URL ?>/img/rhease-ease 1.png" alt="Logo RH ease" class="logo">
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
                <span class="toggle-password-icon" id="togglePassBtn"></span>
            </div>
        </div>

        <ul class="password-rules">
            <li>A senha deve conter:</li>
            <li>No mínimo 8 caracteres.</li>
            <li>Pelo menos uma letra maiúscula.</li>
            <li>Pelo menos um número.</li>
            <li>Pelo menos um caractere especial (ex: !@#$%&*).</li>
        </ul>

        <button type="submit" class="btn full-width">Cadastrar</button>
    </form>

    <footer class="login-footer">
        <p>Já tem uma conta? <a href="<?= BASE_URL ?>/login">Faça o login</a></p>
    </footer>
</div>

<script>
    // Script simples para o Toggle de Senha
    // (Idealmente mover para auth.js se quiser deixar o PHP 100% limpo)
    const eyeIconSvg = "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%236c757d' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z'></path><circle cx='12' cy='12' r='3'></circle></svg>";
    const eyeOffIconSvg = "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%236c757d' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M9.88 9.88a3 3 0 1 0 4.24 4.24'></path><path d='M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68'></path><path d='M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61'></path><line x1='2' y1='2' x2='22' y2='22'></line></svg>";

    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('togglePassBtn');
        const passInput = document.getElementById('password');

        // Inicializa ícone
        toggleBtn.style.backgroundImage = `url("${eyeIconSvg}")`;

        toggleBtn.addEventListener('click', function() {
            if (passInput.type === 'password') {
                passInput.type = 'text';
                toggleBtn.style.backgroundImage = `url("${eyeOffIconSvg}")`;
            } else {
                passInput.type = 'password';
                toggleBtn.style.backgroundImage = `url("${eyeIconSvg}")`;
            }
        });
    });
</script>

<script src="<?= BASE_URL ?>/js/auth.js"></script>

</body>
</html>