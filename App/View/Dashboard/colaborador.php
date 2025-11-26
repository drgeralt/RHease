<?php
// 1. Lógica de Dados do Dashboard (Mantida)
$nome_completo = $nome_completo ?? 'Colaborador';
$ultimo_ponto_hora = isset($ultimo_ponto['data_hora_entrada']) ? date('H:i', strtotime($ultimo_ponto['data_hora_entrada'])) : '--:--';
$salario_base_formatado = number_format($salario_base ?? 0, 2, ',', '.');
$beneficios_count = $beneficios_count ?? 0;
$horas_semana = round($horas_semana ?? 0);

// Cálculos de Gráfico
$salario_base = (float)$salario_base;
$beneficios_valor = (float)($beneficios_valor ?? 0);
$descontos_valor = $descontos_valor ?? 0;
$salario_liquido = ($salario_base + $beneficios_valor) - $descontos_valor;
$salario_liquido_formatado = number_format($salario_liquido, 2, ',', '.');

$total = max($salario_base + $beneficios_valor + $descontos_valor, 1);
$porc_base = round(($salario_base / $total) * 100);
$porc_beneficios = round(($beneficios_valor / $total) * 100);

// Estilo do gráfico de Salário
$salario_chart_style = "
    background: conic-gradient(
        #25621C 0% {$porc_base}%,
        #489D3B {$porc_base}% " . ($porc_base + $porc_beneficios) . "%,
        #D32F2F " . ($porc_base + $porc_beneficios) . "% 100%
    );
";

// 2. Lógica de Permissão (Para exibir o seletor de empresa se for gestor)
$perfilUsuario = $_SESSION['user_perfil'] ?? 'colaborador';
$isGestor = in_array($perfilUsuario, ['gestor_rh', 'diretor', 'admin']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RHease - Painel de <?= htmlspecialchars($nome_completo) ?></title>

    <!-- Fontes -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- Estilos -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/dashboard.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/dashboardColaborador.css">
</head>

<body>

<header>
    <div style="display:flex; align-items:center; gap:10px;">
        <i class="menu-toggle bi bi-list"></i>
        <div class="logo"><img src="<?= BASE_URL ?>/img/rhease-ease 1.png" alt="Logo" style="height:40px;"></div>
    </div>

    <?php if ($isGestor): ?>
        <!-- Seletor de Empresa (Apenas se for Gestor visualizando) -->
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

    <!-- SIDEBAR CENTRALIZADA -->
    <?php include BASE_PATH . '/App/View/Common/sidebar.php'; ?>

    <div class="content">
        <h2 class="page-title-content">Painel de <?= htmlspecialchars($nome_completo) ?></h2>

        <section class="dashboard-grid">
            <div class="metric-card-small">
                <div class="icon-wrapper-large"><i class="fas fa-clock"></i></div>
                <p class="metric-value-large metric-value-main"><?= $ultimo_ponto_hora ?></p>
                <p class="metric-label-small">Você bateu o ponto</p>
            </div>

            <div class="metric-card-small">
                <div class="icon-wrapper-large"><i class="fas fa-briefcase"></i></div>
                <p class="metric-value-large metric-value-main"><?= $salario_base_formatado ?></p>
                <p class="metric-label-small">Salário base</p>
            </div>

            <div class="metric-card-small">
                <div class="icon-wrapper-large"><i class="fas fa-hand-holding-usd"></i></div>
                <p class="metric-value-large metric-value-main"><?= $beneficios_count ?></p>
                <p class="metric-label-small">Benefícios ativos</p>
            </div>
        </section>

        <section class="chart-grid">
            <div class="chart-card-colaborador">
                <h2 class="chart-title">Distribuição do salário</h2>
                <div class="chart-content">
                    <div class="donut-chart-mock" style="<?= $salario_chart_style ?>">
                        <div class="donut-center">
                            <span class="donut-center-value"><?= $salario_liquido_formatado ?></span>
                            <span class="donut-center-label">Salário líquido</span>
                        </div>
                    </div>

                    <div class="chart-legend">
                        <ul>
                            <li><span class="dot" style="background: #25621C"></span> Salário base</li>
                            <li><span class="dot" style="background: #489D3B"></span> Benefícios</li>
                            <li><span class="dot" style="background: #D32F2F"></span> Descontos</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="chart-card-colaborador">
                <h2 class="chart-title">Horas trabalhadas</h2>
                <div class="chart-content">
                    <?php if (!empty($horas_chart_data_present)): ?>
                        <div class="hours-donut" style="<?= $horas_chart_style ?>">
                            <div class="hours-donut-center">
                                <span class="hours-chart-value"><?= $horas_semana ?></span>
                                <span class="hours-chart-label">Horas semanais</span>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="no-data-placeholder">Sem dados para exibir</div>
                    <?php endif; ?>

                    <div class="hours-legend">
                        <ul>
                            <li><span class="dot dot-segunda"></span> Segunda</li>
                            <li><span class="dot dot-terca"></span> Terça</li>
                            <li><span class="dot dot-quarta"></span> Quarta</li>
                            <li><span class="dot dot-quinta"></span> Quinta</li>
                            <li><span class="dot dot-sexta"></span> Sexta</li>
                            <li><span class="dot dot-sabado"></span> Sábado</li>
                            <li><span class="dot dot-domingo"></span> Domingo</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<?php if ($isGestor): ?>
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
<?php endif; ?>

<script>
    const BASE_URL = "<?php echo BASE_URL; ?>";
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="<?php echo BASE_URL; ?>/js/sidebar-toggle.js"></script>

<?php if ($isGestor): ?>
    <script src="<?php echo BASE_URL; ?>/js/empresa.js"></script>
<?php endif; ?>

</body>
</html>