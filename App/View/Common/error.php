<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RHease - Ops!</title>

    <!-- Fontes e Ícones -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --green-light: #489D3B;
            --green-dark: #25621C;
            --text-dark: #333;
            --text-light: #757575;
            --bg-color: #f4f7f6;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-dark);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .error-card {
            background-color: #fff;
            width: 100%;
            max-width: 480px;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        /* Barra colorida no topo para dar identidade */
        .error-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 6px;
            background: linear-gradient(90deg, var(--green-dark), var(--green-light));
        }

        .logo {
            max-width: 140px;
            margin-bottom: 30px;
            opacity: 0.9;
        }

        .icon-container {
            font-size: 60px;
            color: #f8d7da; /* Vermelho suave para erro */
            color: var(--green-light); /* Ou verde se quiser manter a calma */
            margin-bottom: 20px;
        }

        h1 {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 10px;
        }

        p {
            color: var(--text-light);
            line-height: 1.6;
            margin-bottom: 30px;
            font-size: 15px;
        }

        .btn-home {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background-color: var(--green-light);
            color: #fff;
            text-decoration: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 600;
            transition: background-color 0.3s, transform 0.2s;
            border: none;
            font-size: 16px;
        }

        .btn-home:hover {
            background-color: var(--green-dark);
            transform: translateY(-2px);
        }

        /* Detalhe técnico sutil (se existir mensagem) */
        .tech-info {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #999;
            text-align: left;
            background: #fafafa;
            padding: 10px;
            border-radius: 6px;
            display: none; /* Ativar via JS se quiser ver o erro */
        }
    </style>
</head>
<body>

<div class="error-card">
    <!-- Logo do Sistema -->
    <img src="<?= BASE_URL ?>/img/rhease-ease 1.png" alt="RHease" class="logo">

    <!-- Ícone de Destaque -->
    <div class="icon-container">
        <i class="bi bi-cone-striped"></i>
    </div>

    <h1>Ops! Algo deu errado.</h1>

    <p>
        Não conseguimos encontrar a página que você procura ou ocorreu um erro inesperado no processamento.
    </p>

    <!-- Botão de Ação -->
    <a href="<?= BASE_URL ?>/inicio" class="btn-home">
        <i class="bi bi-house-door-fill"></i> Voltar ao Início
    </a>

    <?php if (isset($mensagem) && !empty($mensagem)): ?>
        <!-- Exibe mensagem técnica apenas se passada explicitamente -->
        <div style="margin-top: 20px; font-size: 0.85rem; color: #dc3545;">
            Erro: <?= htmlspecialchars($mensagem) ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>