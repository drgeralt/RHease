<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - RH ease</title>

    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/auth.css">

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<div class="login-container">
    <header class="login-header">
        <img src="<?= BASE_URL ?>/img/rhease-ease 1.png" alt="Logo RH ease" class="logo">
        <h1>Acesse a sua conta</h1>
    </header>

    <?php if (isset($data['error'])): ?>
        <div class="alert alert-danger" style="margin-bottom: 20px;">
            <?= htmlspecialchars($data['error']); ?>
        </div>
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

        <div class="options" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; font-size: 0.9em;">
            <div class="remember-me" style="display: flex; gap: 5px;">
                <input type="checkbox" id="remember">
                <label for="remember" style="margin:0; font-weight:400;">Lembre-me</label>
            </div>
            <a href="<?= BASE_URL ?>/esqueceu-senha" style="color: var(--green-light); text-decoration: none;">Esqueceu sua senha?</a>
        </div>

        <div style="display: flex; flex-direction: column; gap: 15px;">
            <button type="submit" class="btn full-width">Entrar</button>

            <a href="<?= BASE_URL ?>/candidatura" class="btn-cancelar" style="text-align: center; text-decoration: none; display: block;">
                Candidatar-se
            </a>
        </div>
    </form>

    <footer class="login-footer">
        <p>Não tem uma conta? <a href="<?= BASE_URL ?>/cadastro">Cadastre-se</a></p>
    </footer>
</div>

<script>
    // Ícones SVG para o toggle de senha
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

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.toggle-password-icon').forEach(icon => {
            icon.style.backgroundImage = `url("${eyeIconSvg}")`;
        });
    });
</script>

</body>
</html>