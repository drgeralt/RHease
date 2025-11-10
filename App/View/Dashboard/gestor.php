
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RHease - Painel</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/dashboard.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
</head>
<body>
    <header>
        <i class="bi bi-list menu-toggle"></i>
        <img src="<?= BASE_URL ?>/img/rhease-ease 1.png" alt="Logo RH ease" class="logo">
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
                        $gradient_parts = [];
                        $start_percent = 0;
                        $css_color_vars = ['CLT' => '--chart-clt', 'PJ' => '--chart-pj', 'Estágio' => '--chart-estagio', 'Temporário' => '--chart-temporario'];

                        
                        if ($total_colaboradores > 0) {
                            foreach ($distribuicao as $tipo => $total) {
                                $color_var = $css_color_vars[$tipo] ?? '--text-color-light';
                                $percentage = ($total / $total_colaboradores) * 100;
                                $end_percent = $start_percent + $percentage;
                                $gradient_parts[] = "var({$color_var}) {$start_percent}% {$end_percent}%";
                                $start_percent = $end_percent;
                            }
                        }

                        if (empty($gradient_parts)) {
                            $conic_gradient_style = 'background: #e0e0e0;';
                        } else {
                            $conic_gradient_style = 'background: conic-gradient(' . implode(', ', $gradient_parts) . ');';
                        }
                        ?>
                        <div class="donut-chart-mock" style="<?= $conic_gradient_style ?>">
                            <span class="donut-center-value"><?= htmlspecialchars($total_colaboradores) ?></span>
                        </div>

                        <div class="chart-legend">
                            <ul>
                                <?php
                                $coresContrato = ['CLT' => 'clt', 'PJ' => 'pj', 'Estágio' => 'estagio', 'Temporário' => 'temporario'];
                                ?>
                                <?php foreach ($distribuicao as $tipo => $total): ?>
                                <li class="<?= $coresContrato[$tipo] ?? '' ?>">
                                    <span class="dot"></span>
                                    <?= htmlspecialchars($tipo) ?> (<?= $total ?>)
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
<script src="<?php echo BASE_URL; ?>/js/sidebar-toggle.js"></script>
</body>
</html>