<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha - RH ease</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/auth.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>

<div class="login-container">
    <header class="login-header">
        <img src="<?= BASE_URL ?>/img/rhease-ease 1.png" alt="Logo RH ease" class="logo">
        <h1>Esqueceu sua senha?</h1>
        <p style="color: var(--text-color-light); margin-top: 1rem;">Não se preocupe! Insira seu e-mail abaixo e enviaremos um link para você redefinir sua senha.</p>
    </header>

    <?php if (isset($data['error'])): ?>
        <div class="alert alert-danger" style="margin-bottom: 1rem;"><?= htmlspecialchars($data['error']) ?></div>
    <?php endif; ?>
    <?php if (isset($data['success'])): ?>
        <div class="alert alert-success" style="margin-bottom: 1rem;"><?= htmlspecialchars($data['success']) ?></div>
    <?php endif; ?>

    <form class="login-form" method="POST" action="<?= BASE_URL ?>/solicitar-recuperacao">
        <div class="input-group">
            <label for="email">E-mail</label>
            <div class="input-wrapper icon-email">
                <input type="email" id="email" name="email" placeholder="Insira seu e-mail cadastrado" required>
            </div>
        </div>

        <button type="submit" class="btn full-width">Enviar link de recuperação</button>
    </form>

    <footer class="login-footer">
        <p><a href="<?= BASE_URL ?>/login">Voltar para o Login</a></p>
    </footer>
</div>

</body>
</html>