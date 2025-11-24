<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Análise de IA</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/vagas.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<header>
    <div class="logo"><img src="<?= BASE_URL ?>/img/rhease-ease 1.png" alt="RH ease" class="logo"></div>
</header>

<div class="container">
    <div class="content">
        <div class="page-header">
            <a href="<?= BASE_URL ?>/vagas/listar" class="btn-cancelar">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>

        <section class="content-section analise-container">
            <div class="analise-header">
                <h2>Candidato(a): <span style="color: var(--text-color-dark);"><?= htmlspecialchars($candidatura['nome_completo']); ?></span></h2>
                <h3>Vaga: <?= htmlspecialchars($candidatura['titulo_vaga']); ?></h3>
            </div>

            <div class="score-display">
                <div class="score-circle" style="background: conic-gradient(
                        var(--green-light) <?= ($candidatura['pontuacao_aderencia'] ?? 0) * 3.6; ?>deg,
                        #e9ecef <?= ($candidatura['pontuacao_aderencia'] ?? 0) * 3.6; ?>deg
                        );">
                    <span class="score-text"><?= htmlspecialchars($candidatura['pontuacao_aderencia'] ?? 0); ?></span>
                </div>
            </div>

            <div class="justificativa-box">
                <h4 style="color: var(--blue-color); margin-top:0;">Justificativa da IA</h4>
                <p><?= nl2br(htmlspecialchars($candidatura['justificativa_ia'] ?? 'Justificativa não disponível.')); ?></p>
            </div>
        </section>
    </div>
</div>
</body>
</html>