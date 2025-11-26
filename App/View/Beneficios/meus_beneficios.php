<?php
// 1. Lógica de Permissão (Para exibir recursos de gestor no header)
$perfilUsuario = $_SESSION['user_perfil'] ?? 'colaborador';
$isGestor = in_array($perfilUsuario, ['gestor_rh', 'diretor', 'admin']);

// 2. Helper de Formatação (Apenas visual)
if (!function_exists('formatarValorView')) {
    function formatarValorView($valor) {
        if (is_numeric($valor) && $valor > 0) {
            return 'R$ ' . number_format((float)$valor, 2, ',', '.');
        }
        return '';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RHease - Meus Benefícios</title>

    <!-- Fontes -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Estilos -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/beneficios.css">
</head>
<body>

<header>
    <div style="display:flex; align-items:center; gap:10px;">
        <i class="menu-toggle bi bi-list"></i>
        <div class="logo"><img src="<?= BASE_URL ?>/img/rhease-ease 1.png" alt="Logo" style="height:40px;"></div>
    </div>

    <?php if ($isGestor): ?>
        <div class="header-right">
            <div class="empresa-selector" onclick="abrirModalEmpresas()">
                <i class="bi bi-building"></i>
                <span id="nomeEmpresaAtiva">Carregando...</span>
                <i class="bi bi-chevron-down small"></i>
            </div>
        </div>
    <?php endif; ?>
</header>

<div class="app-container">

    <!-- SIDEBAR CENTRALIZADA -->
    <?php include BASE_PATH . '/App/View/Common/sidebar.php'; ?>

    <div class="content">
        <div class="header-tabela" style="margin-bottom: 20px;">
            <h2>Meus Benefícios</h2>
            <p style="color: var(--text-color-light);">Lista de benefícios ativos vinculados ao seu contrato.</p>
        </div>

        <div class="tabela-container">
            <table>
                <thead>
                <tr>
                    <th style="width: 25%;">Benefício</th>
                    <th style="width: 15%;">Categoria</th>
                    <th style="width: 20%;">Valor / Cobertura</th>
                    <th style="width: 40%;">Detalhes</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($lista_beneficios)): ?>
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 30px; color: #777;">
                            <i class="bi bi-info-circle"></i> Nenhum benefício ativo encontrado.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($lista_beneficios as $beneficio): ?>
                        <?php
                        // Lógica visual de ícones
                        $icone = 'bi-check-circle-fill';
                        $nomeBen = $beneficio['nome_beneficio'] ?? $beneficio['nome']; // Compatibilidade

                        if (stripos($nomeBen, 'Transporte') !== false) $icone = 'bi-bus-front-fill';
                        elseif (stripos($nomeBen, 'Saúde') !== false) $icone = 'bi-heart-fill';
                        elseif (stripos($nomeBen, 'Refeição') !== false || stripos($nomeBen, 'Alimentação') !== false) $icone = 'bi-cup-hot-fill';

                        $valor_final = '';
                        $detalhes_descricao = '';

                        if ($beneficio['tipo_valor'] === 'Fixo') {
                            $valor_a_usar = !empty($beneficio['valor_especifico'])
                                    ? $beneficio['valor_especifico']
                                    : ($beneficio['custo_padrao_empresa'] ?? 0);
                            $valor_final = "Fixo (" . formatarValorView($valor_a_usar) . ")";
                            $detalhes_descricao = "—";

                        } elseif ($beneficio['tipo_valor'] === 'Variável') {
                            $valor_final = "Variável";
                            $detalhes_descricao = !empty($beneficio['valor_especifico'])
                                    ? htmlspecialchars($beneficio['valor_especifico'])
                                    : "Consulte a política ou o RH.";

                        } elseif ($beneficio['tipo_valor'] === 'Descritivo') {
                            $valor_final = "Benefício em Serviço";
                            $detalhes_descricao = !empty($beneficio['valor_especifico'])
                                    ? htmlspecialchars($beneficio['valor_especifico'])
                                    : "Detalhes disponíveis com o RH.";
                        }
                        ?>
                        <tr>
                            <td>
                                <i class="bi <?php echo $icone; ?> me-2" style="color:var(--green-light);"></i>
                                <strong><?php echo htmlspecialchars($nomeBen); ?></strong>
                            </td>
                            <td><?php echo htmlspecialchars($beneficio['categoria']); ?></td>
                            <td><?php echo $valor_final; ?></td>
                            <td style="color: #555;"><?php echo $detalhes_descricao; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="alert alert-light mt-4 border" role="alert">
            <i class="bi bi-info-circle-fill text-primary"></i>
            Para solicitar a inclusão ou alteração de benefícios, por favor, entre em contato com o RH ou seu gestor direto.
        </div>
    </div>
</div>

<?php if ($isGestor): ?>
    <!-- MODAL EMPRESAS (Apenas Gestores) -->
    <div id="modalEmpresas" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Perfil da Empresa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">Selecionar Filial/Perfil Ativo:</label>
                    <div id="listaEmpresas" class="list-group mb-4"></div>
                    <hr>
                    <h6>Editar/Criar Perfil</h6>
                    <form id="formEmpresa">
                        <input type="hidden" id="empresaId" name="id">
                        <div class="row g-2">
                            <div class="col-12"><input type="text" name="razao_social" class="form-control" placeholder="Razão Social" required></div>
                            <div class="col-6"><input type="text" name="cnpj" class="form-control" placeholder="CNPJ" required></div>
                            <div class="col-6"><input type="text" name="cidade_uf" class="form-control" placeholder="Cidade - UF"></div>
                            <div class="col-12"><input type="text" name="endereco" class="form-control" placeholder="Endereço Completo"></div>
                        </div>
                        <div class="mt-2 text-end">
                            <button type="button" onclick="limparFormEmpresa()" class="btn btn-sm btn-outline-secondary">Novo</button>
                            <button type="submit" class="btn btn-sm btn-success">Salvar Dados</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
    const BASE_URL = "<?php echo BASE_URL; ?>";
</script>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="<?php echo BASE_URL; ?>/js/sidebar-toggle.js"></script>

<?php if ($isGestor): ?>
    <script src="<?php echo BASE_URL; ?>/js/empresa.js"></script>
<?php endif; ?>

</body>
</html>