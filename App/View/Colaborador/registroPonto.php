<?php
// 1. Logic for Management Permissions (Multi-tenancy)
$perfilUsuario = $_SESSION['user_perfil'] ?? 'colaborador';
$isGestor = in_array($perfilUsuario, ['gestor_rh', 'diretor', 'admin']);

// 2. Logic for Clock-in Data
$nome_completo = $colaborador['nome_completo'] ?? 'Colaborador';
$horaEntrada = isset($horaEntrada) ? $horaEntrada : null;
$precisaCadastrarFace = isset($precisaCadastrarFace) ? $precisaCadastrarFace : false;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Frequência</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Styles -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/registroPonto.css">

    <style>
        /* Small inline adjustment for specific text, or move to registroPonto.css */
        .entry-time-info {
            font-size: 16px;
            color: var(--text-color-light);
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<header>
    <div style="display:flex; align-items:center; gap:10px;">
        <i class="menu-toggle bi bi-list"></i>
        <div class="logo"><img src="<?= BASE_URL ?>/img/rhease-ease 1.png" alt="Logo" style="height:40px;"></div>
    </div>

    <?php if ($isGestor): ?>
        <!-- COMPANY SELECTOR (Only for Managers) -->
        <div class="header-right">
            <div class="empresa-selector" onclick="abrirModalEmpresas()">
                <i class="bi bi-building"></i>
                <span id="nomeEmpresaAtiva">Carregando...</span>
                <i class="bi bi-chevron-down small"></i>
            </div>
        </div>
    <?php endif; ?>
</header>

<div class="app-container">

    <!-- CENTRALIZED SIDEBAR -->
    <?php include BASE_PATH . '/App/View/Common/sidebar.php'; ?>

    <div class="content">
        <div class="header-tabela" style="margin-bottom: 20px;">
            <h2>Registro de Ponto</h2>
        </div>

        <main class="main-content">
            <div class="clock-widget">
                <div id="initial-view">
                    <h1 class="greeting">Olá, <?= htmlspecialchars($nome_completo) ?>!</h1>

                    <?php if ($horaEntrada): ?>
                        <p class="entry-time-info">A sua entrada foi registrada às <strong><?= htmlspecialchars($horaEntrada) ?></strong>.</p>
                    <?php endif; ?>

                    <div id="current-time" class="time-display">--:--</div>
                </div>

                <div id="camera-view" class="hidden">
                    <video id="camera-feed" class="camera-feed" autoplay playsinline></video>
                    <canvas id="photo-canvas" class="hidden"></canvas>
                </div>

                <div id="feedback-container"></div>

                <button id="register-button" class="register-btn">
                    <?= ($horaEntrada) ? 'REGISTRAR SAÍDA' : 'REGISTRAR ENTRADA'; ?>
                </button>
            </div>
        </main>
    </div>
</div>

<?php if ($isGestor): ?>
    <!-- COMPANY MODAL (Only loaded for Managers) -->
    <div id="modalEmpresas" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Perfil da Empresa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">Selecionar Filial/Perfil Ativo:</label>
                    <div id="listaEmpresas" class="list-group mb-4"></div>
                    <hr>
                    <h6>Editar/Criar Perfil</h6>
                    <form id="formEmpresa">
                        <input type="hidden" id="empresaId" name="id">
                        <div class="row g-2">
                            <div class="col-12"><input type="text" name="razao_social" class="form-control" placeholder="Razão Social" required></div>
                            <div class="col-6"><input type="text" name="cnpj" class="form-control" placeholder="CNPJ" required></div>
                            <div class="col-6"><input type="text" name="cidade_uf" class="form-control" placeholder="Cidade - UF"></div>
                            <div class="col-12"><input type="text" name="endereco" class="form-control" placeholder="Endereço Completo"></div>
                        </div>
                        <div class="mt-2 text-end">
                            <button type="button" onclick="limparFormEmpresa()" class="btn btn-sm btn-outline-secondary">Novo</button>
                            <button type="submit" class="btn btn-sm btn-success">Salvar Dados</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- GLOBAL CONFIGURATION FOR JS -->
<script>
    const BASE_URL = "<?= BASE_URL ?>";

    // Variables for registroPonto.js
    const PontoConfig = {
        baseUrl: "<?= BASE_URL ?>",
        isSaida: <?= ($horaEntrada) ? 'true' : 'false' ?>,
        precisaCadastrarFace: <?= ($precisaCadastrarFace) ? 'true' : 'false' ?>
    };
</script>

<!-- SCRIPTS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="<?= BASE_URL ?>/js/sidebar-toggle.js"></script>

<!-- Specific Scripts -->
<script src="<?= BASE_URL ?>/js/registroPonto.js"></script>

<?php if ($isGestor): ?>
    <script src="<?= BASE_URL ?>/js/empresa.js"></script>
<?php endif; ?>

</body>
</html>