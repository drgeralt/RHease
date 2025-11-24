<?php
// ... (MANTENHA TODO O SEU PHP INICIAL AQUI: $nome_completo, cálculos, etc) ...
$nome_completo = $nome_completo ?? 'Colaborador';
$ultimo_ponto_hora = isset($ultimo_ponto['data_hora_entrada']) ? date('H:i', strtotime($ultimo_ponto['data_hora_entrada'])) : '--:--';
// ... etc ...
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RHease - Painel de <?= htmlspecialchars($nome_completo) ?></title>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">

    <link rel="stylesheet" href="<?= BASE_URL ?>/css/dashboard.css">

    <link rel="stylesheet" href="<?= BASE_URL ?>/css/dashboardColaborador.css">
</head>

<body>

<header>
    <i class="bi bi-list menu-toggle"></i>
    <div class="logo"><img src="<?= BASE_URL ?>/img/rhease-ease 1.png" alt="Logo RH Ease" class="logo" style="padding:0;"></div>
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
                    <?php if ($horas_chart_data_present): ?>
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

<script>
    const BASE_URL = "<?php echo BASE_URL; ?>";
</script>
<script src="<?php echo BASE_URL; ?>/js/sidebar-toggle.js"></script>
</body>
</html>