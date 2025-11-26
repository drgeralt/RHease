<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RHease - Painel</title>

    <!-- Fontes -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap e Ícones -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- Estilos -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/dashboard.css">
</head>
<body>

<header>
    <div style="display:flex; align-items:center; gap:10px;">
        <i class="menu-toggle bi bi-list"></i>
        <div class="logo"><img src="<?= BASE_URL ?>/img/rhease-ease 1.png" alt="Logo" style="height:40px;"></div>
    </div>

    <!-- SELETOR DE EMPRESA -->
    <div class="header-right">
        <div class="empresa-selector" onclick="abrirModalEmpresas()">
            <i class="bi bi-building"></i>
            <span id="nomeEmpresaAtiva">Carregando...</span>
            <i class="bi bi-chevron-down small"></i>
        </div>
    </div>
</header>

<!-- MODAL EMPRESAS -->
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

<div class="app-container">

    <!-- SIDEBAR CENTRALIZADA -->
    <?php include BASE_PATH . '/App/View/Common/sidebar.php'; ?>

    <div class="content">
        <h2 class="page-title-content">Painel</h2>

        <section class="dashboard-grid">

            <div class="metric-card-small">
                <div class="icon-wrapper-large"><i class="fas fa-user-friends"></i></div>
                <p class="metric-value-large"><?= htmlspecialchars($dashboard_data['total_colaboradores_ativos'] ?? 0) ?></p>
                <p class="metric-label-small">Colaboradores ativos</p>
            </div>

            <div class="metric-card-small">
                <div class="icon-wrapper-large"><i class="fas fa-briefcase"></i></div>
                <p class="metric-value-large"><?= htmlspecialchars($dashboard_data['total_vagas_publicadas'] ?? 0) ?></p>
                <p class="metric-label-small">Vagas publicadas</p>
            </div>

            <div class="metric-card-small">
                <div class="icon-wrapper-large"><i class="fas fa-wallet"></i></div>
                <p class="metric-value-large"><?= htmlspecialchars($dashboard_data['total_beneficios_ativos'] ?? 0) ?></p>
                <p class="metric-label-small">Benefícios ativos</p>
            </div>
        </section>

        <section class="chart-grid">

            <div class="chart-card-colaborador">
                <h2 class="chart-title">Tipos de contrato</h2>

                <div class="chart-content">
                    <?php
                    $distribuicao = $dashboard_data['distribuicao_contratos'] ?? [];
                    $total_colaboradores = $dashboard_data['total_colaboradores_ativos'] ?? 0;

                    // Cores HEX (Garante funcionamento sem depender de variáveis CSS externas)
                    $mapaCores = [
                            'CLT' => '#d32f2f',       // Vermelho
                            'PJ' => '#489D3B',        // Verde
                            'ESTÁGIO' => '#1976d2',   // Azul
                            'ESTAGIO' => '#1976d2',
                            'TEMPORÁRIO' => '#757575',// Cinza
                            'TEMPORARIO' => '#757575'
                    ];

                    $gradient_parts = [];
                    $start_percent = 0;

                    if ($total_colaboradores > 0) {
                        foreach ($distribuicao as $tipo => $qtd) {
                            if ($qtd > 0) {
                                $tipoNormalizado = mb_strtoupper(trim($tipo));
                                $cor = $mapaCores[$tipoNormalizado] ?? '#ccc';

                                $percent = ($qtd / $total_colaboradores) * 100;
                                $end_percent = $start_percent + $percent;

                                $start_str = number_format($start_percent, 2, '.', '');
                                $end_str = number_format($end_percent, 2, '.', '');

                                $gradient_parts[] = "$cor $start_str% $end_str%";
                                $start_percent = $end_percent;
                            }
                        }
                        $style_grafico = 'background: conic-gradient(' . implode(', ', $gradient_parts) . ');';
                    } else {
                        $style_grafico = 'background: #e0e0e0;';
                    }
                    ?>

                    <div class="donut-chart-mock" style="<?= $style_grafico ?>">
                        <span class="donut-center-value"><?= $total_colaboradores ?></span>
                    </div>

                    <div class="chart-legend">
                        <ul>
                            <?php foreach ($distribuicao as $tipo => $qtd):
                                if($qtd == 0) continue;

                                $tipoNormalizado = mb_strtoupper(trim($tipo));
                                $corLegenda = $mapaCores[$tipoNormalizado] ?? '#ccc';
                                ?>
                                <li>
                                    <span class="dot" style="background-color: <?= $corLegenda ?>;"></span>
                                    <span class="legend-number"><?= $qtd ?></span>
                                    <span class="legend-text"><?= htmlspecialchars($tipo) ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="metric-card-small">
                <div class="icon-wrapper-large"><i class="fas fa-file-alt"></i></div>
                <p class="metric-value-large"><?= htmlspecialchars($dashboard_data['total_curriculos_processados'] ?? 0) ?></p>
                <p class="metric-label-small">Currículos recebidos</p>
            </div>

        </section>
    </div>
</div>

<script>
    const BASE_URL = "<?php echo BASE_URL; ?>";
</script>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="<?php echo BASE_URL; ?>/js/sidebar-toggle.js"></script>
<script src="<?php echo BASE_URL; ?>/js/empresa.js"></script>

</body>
</html>