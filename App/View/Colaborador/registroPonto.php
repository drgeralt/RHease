<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Frequência</title>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">

    <link rel="stylesheet" href="<?= BASE_URL ?>/css/registroPonto.css">

    <style>
        /* Pequeno ajuste inline se necessário, ou mova para registroPonto.css */
        .entry-time-info {
            font-size: 16px;
            color: #555;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
<header>
    <i class="bi bi-list menu-toggle"></i>
    <div class="logo"><img src="<?= BASE_URL ?>/img/rhease-ease 1.png" alt="RHease" class="logo" style="padding:0;"></div>
</header>

<div class="container">
    <div class="sidebar">
        <ul class="menu">
            <li><a href="<?= BASE_URL ?>/inicio"><i class="bi bi-clipboard-data-fill"></i> Painel</a></li>
            <li><a href="<?= BASE_URL ?>/dados"><i class="bi bi-person-vcard-fill"></i> Dados cadastrais</a></li>
            <li><a href="<?= BASE_URL ?>/registrarponto"><i class="bi bi-calendar2-check-fill"></i> Frequência</a></li>
            <li><a href="<?= BASE_URL ?>/meus-holerites"><i class="bi bi-wallet-fill"></i> Salário</a></li>
            <li><a href="<?= BASE_URL ?>/beneficios"><i class="bi bi-shield-fill-check"></i> Benefícios</a></li>
            <li><a href="<?= BASE_URL ?>/vagas/listar"><i class="bi bi-briefcase-fill"></i> Gestão de Vagas</a></li>
            <li><a href="<?= BASE_URL ?>/contato"><i class="bi bi-person-lines-fill"></i> Contato</a></li>
        </ul>
    </div>

    <div class="content">
        <div class="header-tabela" style="margin-bottom: 20px;">
            <h2>Registro de Ponto</h2>
        </div>

        <main class="main-content">
            <div class="clock-widget">
                <div id="initial-view">
                    <h1 class="greeting">Olá, <?= htmlspecialchars($colaborador['nome_completo'] ?? 'Colaborador') ?>!</h1>

                    <?php if (isset($horaEntrada) && $horaEntrada): ?>
                        <p class="entry-time-info">A sua entrada foi registada às <strong><?= $horaEntrada; ?></strong>.</p>
                    <?php endif; ?>

                    <div id="current-time" class="time-display">--:--</div>
                </div>

                <div id="camera-view" class="hidden">
                    <video id="camera-feed" class="camera-feed" autoplay playsinline></video>
                    <canvas id="photo-canvas" class="hidden"></canvas>
                </div>

                <div id="feedback-container"></div>

                <button id="register-button" class="register-btn">
                    <?= (isset($horaEntrada) && $horaEntrada) ? 'REGISTAR SAÍDA' : 'REGISTAR ENTRADA'; ?>
                </button>
            </div>
        </main>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="<?= BASE_URL ?>/js/sidebar-toggle.js"></script>

<script>
    const PontoConfig = {
        baseUrl: "<?= BASE_URL ?>",
        isSaida: <?= (isset($horaEntrada) && $horaEntrada) ? 'true' : 'false' ?>,
        precisaCadastrarFace: <?= isset($precisaCadastrarFace) && $precisaCadastrarFace ? 'true' : 'false' ?>
    };
</script>

<script src="<?= BASE_URL ?>/js/registroPonto.js"></script>

</body>
</html>