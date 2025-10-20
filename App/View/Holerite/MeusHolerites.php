<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Meus Holerites</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/holerites.css">
</head>
<body>
<header class="topbar">
    <div class="logo"><img src="<?php echo BASE_URL; ?>/img/rhease-ease 1.png" alt="Logo RH Ease" class="logo"></div>
</header>

<main class="content-container">

    <h2 style="color: #25621C; font-weight: 600;">
        Meus Holerites - <?php echo isset($colaborador) ? htmlspecialchars($colaborador['nome_completo']) : 'Colaborador'; ?>
    </h2>

    <section>
        <h3>Histórico de Pagamentos</h3>
        <div class="table-wrapper">
            <table class="holerite-table">
                <thead>
                <tr>
                    <th>Referência</th>
                    <th>Data do Pagamento</th>
                    <th>Salário Líquido (R$)</th>
                    <th>Ações</th>
                </tr>
                </thead>
                <tbody>
                <?php
                // Usa a variável $holerites diretamente, como enviado pelo Controller.
                if (empty($holerites)): ?>
                    <tr>
                        <td colspan="4" class="empty-message">Nenhum holerite encontrado.</td>
                    </tr>
                <?php else:
                    // Acessa os dados de cada holerite.
                    foreach ($holerites as $holerite): ?>
                        <tr>
                            <td><?php echo str_pad($holerite['mes_referencia'], 2, '0', STR_PAD_LEFT) . '/' . $holerite['ano_referencia']; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($holerite['data_processamento'])); ?></td>
                            <td><?php echo 'R$ ' . number_format($holerite['salario_liquido'], 2, ',', '.'); ?></td>
                            <td>
                                <form action="<?php echo BASE_URL; ?>/holerite/pdf" method="POST" target="_blank">
                                    <input type="hidden" name="mes" value="<?php echo $holerite['mes_referencia']; ?>">
                                    <input type="hidden" name="ano" value="<?php echo $holerite['ano_referencia']; ?>">

                                    <input type="hidden" name="id_colaborador" value="<?php echo $colaborador['id_colaborador']; ?>">

                                    <button type="submit" class="btn-action">Visualizar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach;
                endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <div class="info-text">
        <p>Para dúvidas sobre seus pagamentos, por favor, entre em contato com o RH.</p>
    </div>
</main>
</body>
</html>