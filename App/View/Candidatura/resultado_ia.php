<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Análise de IA para <?php echo htmlspecialchars($candidatura['nome_completo']); ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/gestaoVagas.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .analise-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
            max-width: 800px;
            margin: 20px auto;
        }
        .analise-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .analise-header h2 {
            margin: 0;
            font-size: 1.2em;
            color: #555;
        }
        .analise-header h3 {
            margin: 5px 0 0;
            font-size: 1.5em;
            color: #333;
        }
        .score-display {
            text-align: center;
            margin-bottom: 30px;
        }
        .score-circle {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: conic-gradient(
                    #28a745 <?php echo ($candidatura['pontuacao_aderencia'] ?? 0) * 3.6; ?>deg,
                    #e9ecef <?php echo ($candidatura['pontuacao_aderencia'] ?? 0) * 3.6; ?>deg
            );
            position: relative;
        }
        .score-circle::before {
            content: '';
            position: absolute;
            background: #fff;
            width: 100px;
            height: 100px;
            border-radius: 50%;
        }
        .score-text {
            font-size: 2em;
            font-weight: 700;
            color: #28a745;
            z-index: 1;
        }
        .justificativa-box {
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
            padding: 20px;
            border-radius: 4px;
        }
        .justificativa-box h4 {
            margin-top: 0;
            color: #007bff;
        }
        .justificativa-box p {
            margin-bottom: 0;
            line-height: 1.6;
        }
    </style>
</head>
<body>
<div class="app-container">
    <header class="header">
        <div class="header-left">
            <div class="logo">
                <img src="<?php echo BASE_URL; ?>/img/rhease-ease 1.png" alt="RH ease" class="logo-img">
            </div>
        </div>
    </header>

    <div class="main-container">
        <main class="main-content">
            <div class="page-header">
                <!-- Este botão leva de volta à lista principal de gestão de vagas -->
                <a href="<?php echo BASE_URL; ?>/vagas/listar" class="btn btn-secondary" style="margin-bottom: 20px; display: inline-block;">
                    <i class="fas fa-arrow-left"></i> Voltar para Gestão de Vagas
                </a>
                <h1 class="page-title">Análise de Aderência da IA</h1>
            </div>

            <div class="analise-container">
                <div class="analise-header">
                    <h2>Candidato(a)</h2>
                    <h3><?php echo htmlspecialchars($candidatura['nome_completo']); ?></h3>
                    <h2 style="margin-top: 10px;">Vaga</h2>
                    <h3><?php echo htmlspecialchars($candidatura['titulo_vaga']); ?></h3>
                </div>

                <div class="score-display">
                    <div class="score-circle">
                        <span class="score-text"><?php echo htmlspecialchars($candidatura['pontuacao_aderencia'] ?? 0); ?></span>
                    </div>
                </div>

                <div class="justificativa-box">
                    <h4>Justificativa da IA</h4>
                    <p>
                        <?php echo nl2br(htmlspecialchars($candidatura['justificativa_ia'] ?? 'Justificativa não disponível.')); ?>
                    </p>
                </div>
            </div>
        </main>
    </div>
</div>
</body>
</html>
