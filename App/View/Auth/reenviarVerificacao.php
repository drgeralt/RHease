<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reenviar Verificação - RH ease</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/login.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>

<div class="login-container">
    <header class="login-header">
        <img src="<?= BASE_URL ?>/img/rhease-ease 1.png" alt="Logo RH ease" class="logo">
        <h1>Reenviar E-mail de Verificação</h1>
        <p style="color: var(--light-text); margin-top: 1rem;">Insira seu e-mail para enviarmos um novo link de ativação da sua conta.</p>
    </header>

    <?php if (isset($data['error'])): ?>
        <div class="error-message" style="margin-bottom: 1rem;"><?= htmlspecialchars($data['error']) ?></div>
    <?php endif; ?>
    <?php if (isset($data['success'])): ?>
        <div class="success-message" style="margin-bottom: 1rem; background-color: #d4edda; color: #155724; padding: 10px; border-radius: 5px;"><?= htmlspecialchars($data['success']) ?></div>
    <?php endif; ?>


    <form class="login-form" method="POST" action="<?= BASE_URL ?>/reenviar-verificacao">
        <div class="input-group">
            <label for="email">E-mail</label>
            <div class="input-wrapper icon-email">
                <input type="email" id="email" name="email" placeholder="Insira seu e-mail cadastrado" required>
            </div>
        </div>

        <button type="submit" class="btn-login">ENVIAR NOVO LINK</button>

        <div class="login-footer">
            <a href="<?= BASE_URL ?>/login">Voltar para o Login</a>
        </div>
    </form>
</div>

</body>
</html>