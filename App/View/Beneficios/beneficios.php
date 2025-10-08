<?php
// VIEW: beneficios.php
// Variáveis esperadas: $beneficios, $beneficios_selecao, $regras, $tiposContrato
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Benefícios - RH Ease</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/beneficiostyle.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
<header>
    <i class="bi bi-list menu-toggle"></i>
    <img id="logo" src="<?= BASE_URL ?>/img/rhease-ease 1.png" alt="RHease" width="130">
</header>

<div class="container">
    <div class="sidebar">
        <ul class="menu">
            <li><a href="index.html"><i class="bi bi-clipboard-data-fill"></i> Painel</a></li>
            <li><a href="dados.html"><i class="bi bi-person-vcard-fill"></i> Dados Cadastrais</a></li>
            <li><a href="frequencia.html"><i class="bi bi-calendar2-check-fill"></i> Frequência</a></li>
            <li><a href="salario.html"><i class="bi bi-wallet-fill"></i> Salário</a></li>
            <li class="active"><a href="#"><i class="bi bi-shield-fill-check"></i> Benefícios</a></li>
            <li><a href="contato.html"><i class="bi bi-person-lines-fill"></i> Contato</a></li>
        </ul>
    </div>

    <div class="content">
        <!-- Catálogo de Benefícios -->
        <div class="header-tabela">
            <h2>Catálogo de Benefícios</h2>
            <button class="btn-adicionar"><i class="bi bi-plus-lg"></i> Adicionar Benefício</button>
        </div>

        <div class="tabela-container">
            <table>
                <thead>
                <tr>
                    <th>Benefício</th>
                    <th>Categoria</th>
                    <th>Tipo de Valor</th>
                    <th>Status</th>
                    <th class="col-acoes">Ações</th>
                </tr>
                </thead>
                <tbody>
                <?php if(!empty($beneficios)): ?>
                    <?php foreach($beneficios as $b): ?>
                        <?php
                        $tipo_valor_exibicao = $b['tipo_valor'] ?? '';
                        $valor_fixo = $b['valor_fixo'] ?? $b['custo_padrao_empresa'] ?? null;

                        if (!empty($valor_fixo) && $tipo_valor_exibicao === 'Fixo') {
                            $tipo_valor_exibicao = "Fixo (R$" . number_format((float)$valor_fixo, 2, ',', '.') . ")";
                        }
                        ?>
                        <tr data-id="<?= $b['id_beneficio'] ?? '' ?>"
                            data-tipo-valor-puro="<?= $b['tipo_valor'] ?? '' ?>"
                            data-valor-fixo="<?= $valor_fixo ?? '' ?>">
                            <td><?= htmlspecialchars($b['nome'] ?? '') ?></td>
                            <td><?= htmlspecialchars($b['categoria'] ?? '') ?></td>
                            <td><?= htmlspecialchars($tipo_valor_exibicao) ?></td>
                            <td><?= htmlspecialchars($b['status'] ?? '') ?></td>
                            <td class="acoes">
                                <i class="bi bi-pencil-square editar"></i>
                                <i class="bi bi-trash3-fill deletar"></i>
                                <label class="switch">
                                    <input type="checkbox" <?= (!empty($b['status']) && $b['status'] === 'Ativo') ? 'checked' : '' ?>>
                                    <span class="slider round"></span>
                                </label>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align:center;">Nenhum benefício cadastrado</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Regras de Atribuição -->
        <div class="secao-regras">
            <h2>Regras de Atribuição Automática</h2>
            <div class="tabela-container regras-container">
                <table class="regras-tabela">
                    <thead>
                    <tr>
                        <th>Tipo de Contrato</th>
                        <th>Benefícios Padrão</th>
                        <th id="acoes_style">Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if(!empty($tiposContrato)): ?>
                        <?php foreach($tiposContrato as $tipo): ?>
                            <?php
                            $lista_nomes = isset($regras[$tipo]['nomes']) ? implode(", ", $regras[$tipo]['nomes']) : 'Nenhum benefício padrão';
                            $lista_ids = isset($regras[$tipo]['ids']) ? implode(",", $regras[$tipo]['ids']) : '';
                            ?>
                            <tr data-tipo-contrato="<?= htmlspecialchars($tipo) ?>" data-beneficios-ids="<?= htmlspecialchars($lista_ids) ?>">
                                <td><?= htmlspecialchars($tipo) ?></td>
                                <td><?= htmlspecialchars($lista_nomes) ?></td>
                                <td style="text-align:center;">
                                    <i class="bi bi-pencil-square editar-regra" title="Editar Regras"></i>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" style="text-align:center;">Nenhum tipo de contrato definido</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="<?= BASE_URL ?>/js/beneficios.js"></script>
</body>
</html>
