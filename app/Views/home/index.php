<!DOCTYPE html>
<html lang="pt-br">
<head>
    <title>Bem-vindo ao RHEase</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
</head>
<body>
<div class="container">
    <h1>Bem-vindo ao RHEase</h1>
    <p>Selecione uma função abaixo:</p>

    <div class="section">
        <h2>Gestão de Candidaturas</h2>
        <a href="/RHEase/public/job-application" class="btn btn-primary">Ver Vaga de Emprego</a>
        <a href="/RHEase/public/applications" class="btn btn-secondary">Ver Candidaturas</a>
    </div>

    <div class="section">
        <h2>Gestão de Benefícios e Demissões</h2>
        <a href="/RHEase/public/beneficios" class="btn btn-primary"> Gerenciar Benefícios </a>
        <a href="/RHEase/public/funcionarios/demitidos" class="btn btn-secondary"> Lista de demitidos </a>
        <a href="/RHEase/public/demissao" class="btn btn-primary"> Iniciar Demissão </a>
    </div>

    <div class="section">
        <h2>Gestão de Folha de Pagamento</h2>
        <a href="/RHEase/public/payroll/add" class="btn btn-primary">Criar Pagamento</a>
        <a href="/RHEase/public/payrolls" class="btn btn-secondary">Ver Pagamentos</a>
    </div>

    <div class="section">
        <h2>Comunicação Interna</h2>
        <a href="/RHEase/public/comunicacao" class="btn btn-primary"> Comunicação Interna </a>
    </div>
</div>
<script src="<?= BASE_URL ?>/js/scripts.js"></script>
</body>
</html>
