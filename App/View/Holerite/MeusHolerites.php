<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RHease - Meus Holerites</title>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">

    <link rel="stylesheet" href="<?= BASE_URL ?>/css/holerites.css">
</head>
<body>
<header>
    <i class="menu-toggle bi bi-list"></i>
    <div class="logo"><img src="<?= BASE_URL ?>/img/rhease-ease 1.png" alt="Logo RH ease" class="logo" style="padding:0;"></div>
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
        <h2 class="page-title-content">
            Meus Holerites - <?php echo (!empty($colaborador) && isset($colaborador['nome_completo'])) ? htmlspecialchars($colaborador['nome_completo']) : 'Colaborador'; ?>
        </h2>

        <section class="content-section">
            <h3>Histórico de Pagamentos</h3>

            <div class="tabela-container">
                <table>
                    <thead>
                    <tr>
                        <th>Referência</th>
                        <th>Data do Pagamento</th>
                        <th>Salário Líquido (R$)</th>
                        <th style="text-align: center;">Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($holerites)): ?>
                        <tr>
                            <td colspan="4" class="empty-message">Nenhum holerite encontrado.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($holerites as $holerite): ?>
                            <tr>
                                <td><?php echo str_pad($holerite['mes_referencia'], 2, '0', STR_PAD_LEFT) . '/' . $holerite['ano_referencia']; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($holerite['data_processamento'])); ?></td>
                                <td><?php echo 'R$ ' . number_format($holerite['salario_liquido'], 2, ',', '.'); ?></td>
                                <td style="text-align: center;">
                                    <form action="<?php echo BASE_URL; ?>/holerite/gerarPDF" method="POST" target="_blank">
                                        <input type="hidden" name="mes" value="<?php echo $holerite['mes_referencia']; ?>">
                                        <input type="hidden" name="ano" value="<?php echo $holerite['ano_referencia']; ?>">
                                        <input type="hidden" name="id_colaborador" value="<?php echo $colaborador['id_colaborador']; ?>">
                                        <button type="button" class="btn-action" onclick="this.closest('form').submit();">
                                            <i class="fas fa-file-pdf"></i> Visualizar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <div class="info-text">
            <p>Para dúvidas sobre seus pagamentos, por favor, entre em contato com o RH.</p>
        </div>
    </div>
</div>

<script>
    const BASE_URL = "<?php echo BASE_URL; ?>";
</script>
<script src="<?php echo BASE_URL; ?>/js/sidebar-toggle.js"></script>
</body>
</html>