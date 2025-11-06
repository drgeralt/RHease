<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidatura para <?php echo htmlspecialchars($vaga['titulo_vaga']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/novaVaga.css"> <!-- Reutilizando o mesmo estilo -->
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

    <main class="main-container">
        <h1>Candidatura para a Vaga: <?php echo htmlspecialchars($vaga['titulo_vaga']); ?></h1>
        <p>Preencha os seus dados abaixo para se candidatar.</p>

        <!-- PONTO CRÍTICO 1: A action deve apontar para a rota que salva os dados -->
        <!-- PONTO CRÍTICO 2: method="POST" e enctype="multipart/form-data" são essenciais -->
        <form action="<?php echo BASE_URL; ?>/candidatura/aplicar" method="POST" enctype="multipart/form-data">

            <!-- PONTO CRÍTICO 3: Envia o ID da vaga de forma oculta -->
            <input type="hidden" name="id_vaga" value="<?php echo htmlspecialchars($vaga['id_vaga']); ?>">

            <section>
                <h3>Dados Pessoais</h3>
                <div class="grid">
                    <div>
                        <label for="nome_completo">Nome Completo</label>
                        <input id="nome_completo" type="text" name="nome_completo" placeholder="Digite o seu nome completo" required>
                    </div>
                    <div>
                        <label for="cpf">CPF</label>
                        <input id="cpf" type="text" name="cpf" placeholder="000.000.000-00" required>
                    </div>
                </div>
            </section>

            <section>
                <h3>Currículo</h3>
                <div class="grid">
                    <div>
                        <label for="curriculo_pdf">Anexe o seu currículo (PDF)</label>
                        <input id="curriculo_pdf" type="file" name="curriculo_pdf" accept=".pdf" required>
                    </div>
                </div>
            </section>

            <div style="display: flex; gap: 15px; align-items: center; justify-content: flex-start; margin-top: 30px; margin-left: 20px;">
                <button type="button" onclick="window.history.back()" class="back-button">
                    <i class="fas fa-arrow-left"></i> Voltar
                </button>
                <button type="submit" class="submit-button">Enviar Candidatura</button>
            </div>

        </form>
    </main>
</div>
</body>
</html>