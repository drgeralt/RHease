<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha - RH ease</title>

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
        <h1>Redefina sua senha</h1>
        <p style="color: var(--text-color-light); margin-top: 1rem;">Por favor, crie uma nova senha forte e segura.</p>
    </header>

    <?php if (isset($data['error'])): ?>
        <div class="alert alert-danger" style="margin-bottom: 1rem;"><?= htmlspecialchars($data['error']) ?></div>
    <?php endif; ?>

    <form class="login-form" method="POST" action="<?= BASE_URL ?>/atualizar-senha">
        <input type="hidden" name="token" value="<?= htmlspecialchars($data['token'] ?? '') ?>">

        <div class="input-group">
            <label for="nova_senha">Nova senha</label>
            <div class="input-wrapper icon-password">
                <input type="password" id="nova_senha" name="nova_senha" required>
                <span class="toggle-password-icon" onclick="togglePasswordVisibility('nova_senha', this)"></span>
            </div>
        </div>

        <div class="input-group">
            <label for="confirmar_senha">Confirmar nova senha</label>
            <div class="input-wrapper icon-password">
                <input type="password" id="confirmar_senha" name="confirmar_senha" required>
                <span class="toggle-password-icon" onclick="togglePasswordVisibility('confirmar_senha', this)"></span>
            </div>
        </div>

        <ul class="password-rules">
            <li>A senha deve conter no mínimo 8 caracteres, uma letra maiúscula, um número e um caractere especial.</li>
        </ul>

        <button type="submit" class="btn full-width">Redefinir senha</button>
    </form>
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

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.toggle-password-icon').forEach(icon => {
            icon.style.backgroundImage = `url("${eyeIconSvg}")`;
        });
    });
</script>

</body>
</html>