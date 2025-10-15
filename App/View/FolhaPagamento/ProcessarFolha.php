<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Processar Folha de Pagamento</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
   
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/holerites.css">
</head>
<body>
<header class="topbar">
    <div class="logo"><img src="<?php echo BASE_URL; ?>/img/rhease-ease 1.png" alt="Logo RH Ease" class="logo"></div>
</header>

<main class="content-container">
    <h2 style="color: #25621C; font-weight: 600;">Processar Folha de Pagamento</h2>

    <section>
        <h3>Período de Referência</h3>

        <!-- Formulário para o RH escolher o mês e ano -->
        <form action="<?php echo BASE_URL; ?>/folha/processar" method="POST" class="processar-form">
            <div class="form-group">
                <label for="mes">Mês:</label>
                <input type="number" id="mes" name="mes" min="1" max="12" placeholder="Ex: 9" required value="<?php echo date('m'); ?>">
            </div>
            <div class="form-group">
                <label for="ano">Ano:</label>
                <input type="number" id="ano" name="ano" min="2020" max="2050" placeholder="Ex: 2025" required value="<?php echo date('Y'); ?>">
            </div>
            <div class="form-group">
                <button type="submit" class="btn-action">Processar Folha</button>
            </div>
        </form>
    </section>

    <!-- Área para exibir mensagens de sucesso ou erro -->
    <div class="messages">
        <?php if (isset($data['sucesso'])): ?>
            <div class="alert alert-success">
                <?php echo $data['sucesso']; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($data['erro'])): ?>
            <div class="alert alert-danger">
                <?php echo $data['erro']; ?>
            </div>
        <?php endif; ?>
    </div>

</main>
</body>
</html>
