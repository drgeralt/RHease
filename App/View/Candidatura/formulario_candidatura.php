<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidatura para <?php echo htmlspecialchars($vaga['titulo_vaga']); ?></title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/vagas.css">
</head>
<body>

<header>
    <div class="logo"><img src="<?= BASE_URL ?>/img/rhease-ease 1.png" alt="RH ease" class="logo"></div>
</header>

<div class="container" style="justify-content: center;"> <main class="content" style="max-width: 800px;">
        <h2 class="page-title-content">Candidatura: <?php echo htmlspecialchars($vaga['titulo_vaga']); ?></h2>
        <p style="margin-bottom: 30px; color: var(--text-color-light);">Preencha os seus dados abaixo para se candidatar.</p>

        <form action="<?php echo BASE_URL; ?>/candidatura/aplicar" method="POST" enctype="multipart/form-data" class="content-section">
            <input type="hidden" name="id_vaga" value="<?php echo htmlspecialchars($vaga['id_vaga']); ?>">

            <div class="form-group">
                <h3>Dados Pessoais</h3>
                <div class="grid">
                    <div class="form-group">
                        <label for="nome_completo">Nome Completo</label>
                        <input id="nome_completo" type="text" name="nome_completo" placeholder="Digite o seu nome completo" required>
                    </div>
                    <div class="form-group">
                        <label for="cpf">CPF</label>
                        <input id="cpf" type="text" name="cpf" placeholder="000.000.000-00" required>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <h3>Currículo</h3>
                <div class="grid">
                    <div class="form-group" style="grid-column: span 2;">
                        <label for="curriculo_pdf">Anexe o seu currículo (PDF)</label>
                        <input id="curriculo_pdf" type="file" name="curriculo_pdf" accept=".pdf" required style="padding: 10px;">
                    </div>
                </div>
            </div>

            <div class="form-actions" style="justify-content: space-between; margin-top: 30px;">
                <button type="button" onclick="window.history.back()" class="btn-cancelar">
                    <i class="fas fa-arrow-left"></i> Voltar
                </button>
                <button type="submit" class="btn-salvar">Enviar Candidatura</button>
            </div>
        </form>
    </main>
</div>

</body>
</html>