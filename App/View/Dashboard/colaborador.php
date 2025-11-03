<?php
$nome_completo = $nome_completo ?? 'Colaborador';
$ultimo_ponto_hora = isset($ultimo_ponto['data_hora_entrada']) ? date('H:i', strtotime($ultimo_ponto['data_hora_entrada'])) : '--:--';
$salario_base_formatado = number_format($salario_base ?? 0, 2, ',', '.');
$beneficios_count = $beneficios_count ?? 0;
$horas_semana = round($horas_semana ?? 0);



// Calcula salário líquido e formata
$salario_base = (float)$salario_base;
$beneficios_valor = (float)$beneficios_valor;
$descontos_valor = (float)$descontos_valor;
$salario_liquido = ($salario_base ?? 0) + $beneficios_valor - $descontos_valor;
$salario_liquido_formatado = number_format($salario_liquido, 2, ',', '.');


// Calcula proporção para o gráfico
$total = max($salario_base + $beneficios_valor + $descontos_valor, 1);
$porc_base = round(($salario_base / $total) * 100);
$porc_beneficios = round(($beneficios_valor / $total) * 100);
$porc_descontos = round(($descontos_valor / $total) * 100);


$horas_chart_style = $horas_chart_style ?? '';
$horas_chart_data_present = $horas_chart_data_present ?? false;

// Monta o estilo do gráfico com conic-gradient
$salario_chart_style = "
    background: conic-gradient(
        #25621C 0% {$porc_base}%,
        #489D3B {$porc_base}% " . ($porc_base + $porc_beneficios) . "%,
        #D32F2F " . ($porc_base + $porc_beneficios) . "% 100%
    );
";

$horas_chart_data_present = true;
$horas_semana = 35; // total de horas na semana

// Exemplo de distribuição das horas por dia (em %)
$porc_segunda = 20;
$porc_terca   = 20;
$porc_quarta  = 20;
$porc_quinta  = 20;
$porc_sexta   = 20;
$porc_sabado  = 0;
$porc_domingo = 0;

// Monta o estilo do gráfico de horas
$horas_chart_style = "
    background: conic-gradient(
        var(--color-segunda) 0% {$porc_segunda}%,
        var(--color-terca) {$porc_segunda}% " . ($porc_segunda+$porc_terca) . "%,
        var(--color-quarta) " . ($porc_segunda+$porc_terca) . "% " . ($porc_segunda+$porc_terca+$porc_quarta) . "%,
        var(--color-quinta) " . ($porc_segunda+$porc_terca+$porc_quarta) . "% " . ($porc_segunda+$porc_terca+$porc_quarta+$porc_quinta) . "%,
        var(--color-sexta) " . ($porc_segunda+$porc_terca+$porc_quarta+$porc_quinta) . "% 100%
    );
";


?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RHease - Painel de <?= htmlspecialchars($nome_completo) ?></title>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/RHease/public/css/dashboardColaborador.css">
    <link rel="stylesheet" href="/RHease/public/css/dashboard.css">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        /* Cores para os gráficos - Injetadas para garantir o funcionamento */
        :root {
            --color-base: #4CAF50;  
            --color-benefits: #2196F3;  
            --color-discounts: #F44336; 

            --color-domingo: #EDADAD;  
            --color-segunda: #ff7213ff;  
            --color-terca: #489D3B;    
            --color-quarta: #3B5A9D;  
            --color-quinta: #D02C2C;    
            --color-sexta: #25621C;     
            --color-sabado: #667080;    
        }

        .chart-circle {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .no-data-placeholder {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin: 0 auto;
            background-color: #E0E0E0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #757575;
            font-weight: 500;
            text-align: center;
            padding: 10px;
        }
        .hours-legend .dot-segunda { background-color: #ff7213ff; }
    </style>
</head>

<body>

    <header>
        <i class="bi bi-list menu-toggle"></i>
        <img src="<?= BASE_URL ?>/img/rhease-ease 1.png" alt="Logo RH ease" class="logo">
    </header>
    
    <div class="container">
        <div class="sidebar">
            <ul class="menu">
                <li class="active"><a href="/inicio"><i class="bi bi-clipboard-data-fill"></i> Painel</a></li>
                <li><a href="dados.html"><i class="bi bi-person-vcard-fill"></i> Dados cadastrais</a></li>
                <li><a href="<?= BASE_URL ?>/registrarponto"><i class="bi bi-calendar2-check-fill"></i> Frequência</a></li>
                <li><a href="<?= BASE_URL ?>/meus-holerites"><i class="bi bi-wallet-fill"></i> Salário</a></li>
                <li><a href="beneficios.html"><i class="bi bi-shield-fill-check"></i> Benefícios</a></li>
                <li><a href="contato.html"><i class="bi bi-person-lines-fill"></i> Contato</a></li>
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
                        <div class="chart-circle" style="<?= $salario_chart_style ?>">
                            <div class="salary-chart-center">
                                <span class="salary-chart-value"><?= $salario_liquido_formatado ?></span>
                                <span class="salary-chart-label">Salário líquido</span>
                            </div>
                        </div>

                        <div class="salary-legend">
                            <ul>
                                <li><span class="dot dot-base"></span> Salário base</li>
                                <li><span class="dot dot-benefits"></span> Benefícios</li>
                                <li><span class="dot dot-discounts"></span> Descontos</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="chart-card-colaborador">
                    <h2 class="chart-title">Horas trabalhadas</h2>
                    <div class="chart-content">
                        <?php if ($horas_chart_data_present): ?>
                             <div class="chart-circle" style="<?= $horas_chart_style ?>">
                                <div class="hours-chart-center">
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
</body>
</html>