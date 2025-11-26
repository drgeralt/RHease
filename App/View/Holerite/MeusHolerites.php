<?php
// CORREÇÃO: Usando 'user_perfil' para casar com o AuthController e a Sidebar
$perfilUsuario = $_SESSION['user_perfil'] ?? 'colaborador';
$isGestor = in_array($perfilUsuario, ['gestor_rh', 'diretor', 'admin']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RHease - Meus Holerites</title>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/holerites.css">
</head>
<body>

<header>
    <div style="display:flex; align-items:center; gap:10px;">
        <i class="menu-toggle bi bi-list"></i>
        <div class="logo"><img src="<?= BASE_URL ?>/img/rhease-ease 1.png" alt="Logo RH Ease" class="logo" style="padding:0;"></div>
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

    <?php include BASE_PATH . '/App/View/Common/sidebar.php'; ?>

    <div class="content">
        <h2 class="page-title-content">
            Meus Holerites - <?php echo (!empty($colaborador) && isset($colaborador['nome_completo'])) ? htmlspecialchars($colaborador['nome_completo']) : 'Colaborador'; ?>
        </h2>

        <section class="content-section">
            <div class="section-header">
                <h3 class="section-title">Histórico de Pagamentos</h3>
            </div>

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
                                        <button type="submit" class="btn-action btn-sm">
                                            <i class="bi bi-file-earmark-pdf"></i> Visualizar
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

<?php if ($isGestor): ?>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="<?php echo BASE_URL; ?>/js/sidebar-toggle.js"></script>

<?php if ($isGestor): ?>
    <script src="<?php echo BASE_URL; ?>/js/empresa.js"></script>
<?php endif; ?>

</body>
</html>