<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RHease - Processar Folha</title>
    
    <!-- Estilos do Dashboard para consistência -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/dashboard.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <!-- Cabeçalho Padrão -->
    <header>
        <i class="bi bi-list menu-toggle"></i>
        <img src="<?= BASE_URL ?>/img/rhease-ease 1.png" alt="Logo RH ease" class="logo">
    </header> 
    
    <div class="container">
        <!-- Sidebar Padrão -->
        <div class="sidebar">
            <ul class="menu">
                <li><a href="<?= BASE_URL ?>/inicio"><i class="bi bi-clipboard-data-fill"></i> Painel</a></li>
                <li><a href="<?= BASE_URL ?>/colaboradores"><i class="bi bi-person-vcard-fill"></i> Colaboradores</a></li>
                <li><a href="<?= BASE_URL ?>/registrarponto"><i class="bi bi-calendar2-check-fill"></i> Frequência</a></li>
                <li class="active"><a href="<?= BASE_URL ?>/folha/processar"><i class="bi bi-wallet-fill"></i> Folha de Pagamento</a></li>
                <li><a href="<?= BASE_URL ?>/beneficios"><i class="bi bi-shield-fill-check"></i> Benefícios</a></li>
                <li><a href="<?= BASE_URL ?>/vagas/listar"><i class="bi bi-briefcase-fill"></i> Gestão de Vagas</a></li>
            </ul>
        </div>

        <!-- Conteúdo da Página -->
        <div class="content">
            <h2 class="page-title-content">Processar Folha de Pagamento</h2>

            <div class="messages">
                <?php if (isset($data['sucesso'])): ?>
                    <div class="alert alert-success"><?php echo $data['sucesso']; ?></div>
                <?php endif; ?>
                <?php if (isset($data['erro'])): ?>
                    <div class="alert alert-danger"><?php echo $data['erro']; ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-container-card">
                 <form action="<?php echo BASE_URL; ?>/folha/processar" method="POST" class="form-processar">
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
            </div>
        </div>
    </div>
</body>
</html>
