<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - RH ease</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/login.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        .input-wrapper {
            position: relative; /* Essencial para o ícone funcionar */
        }
        .toggle-password-icon {
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            width: 22px;
            height: 22px;
            cursor: pointer;
            background-repeat: no-repeat;
            background-size: contain;
        }
    </style>
</head>
<body>

<div class="login-container">
    <header class="login-header">
        <img src="<?= BASE_URL ?>/img/rhease-ease 1.png" alt="Logo RH ease" class="logo">
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
                <span class="toggle-password-icon" onclick="togglePasswordVisibility('password', this)"></span>
            </div>
        </div>

        <div class="options">
            <div class="remember-me">
                <input type="checkbox" id="remember">
                <label for="remember">Lembre-me</label>
            </div>
            <a href="<?= BASE_URL ?>/esqueceu-senha" class="forgot-password">Esqueceu sua senha?</a>
        </div>

        <div class="button-group">
            <button type="submit" class="btn btn-primary">Entrar</button>
        </div>
        <div class="button-group">
            <a href="<?= BASE_URL ?>/candidatura" class="btn btn-third" role="button">Candidatar-se</a>
        </div>
    </form>

    <footer class="login-footer">
        <p>Não tem uma conta? <a href="<?= BASE_URL ?>/cadastro">Cadastre-se</a></p>
    </footer>
</div>

<script>
    const eyeIconSvg = "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%236c757d' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z'></path><circle cx='12' cy='12' r='3'></circle></svg>";
    const eyeOffIconSvg = "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%236c757d' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M9.88 9.88a3 3 0 1 0 4.24 4.24'></path><path d='M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68'></path><path d='M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61'></path><line x1='2' y1='2' x2='22' y2='22'></line></svg>";

    function togglePasswordVisibility(inputId, iconElement) {
        const passwordInput = document.getElementById(inputId);
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            iconElement.style.backgroundImage = `url("${eyeOffIconSvg}")`;
        } else {
            passwordInput.type = 'password';
            iconElement.style.backgroundImage = `url("${eyeIconSvg}")`;
        }
    }

    // Inicia o ícone correto em todos os campos
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.toggle-password-icon').forEach(icon => {
            icon.style.backgroundImage = `url("${eyeIconSvg}")`;
        });
    });
</script>

</body>
</html>