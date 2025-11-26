<?php
// --- 1. PREPARAÇÃO DE DADOS E SEGURANÇA ---
$data = $data ?? [];

$mesRef = $data['mes_ref'] ?? date('m');
$anoRef = $data['ano_ref'] ?? date('Y');
$msgSucesso = $data['sucesso'] ?? null;
$msgErro = $data['erro'] ?? null;

// Tratamento de Resultados (Evita erro "string offset")
$rawResultados = $data['resultados'] ?? null;
$listaSucesso = [];
$listaFalha = [];
$debugConteudo = '';
$erroTecnico = false;

if ($rawResultados !== null) {
    if (is_array($rawResultados)) {
        $listaSucesso = $rawResultados['sucesso'] ?? [];
        $listaFalha = $rawResultados['falha'] ?? [];
    } else {
        $erroTecnico = true;
        $debugConteudo = (string)$rawResultados;
    }
}

// Permissões
$perfilUsuario = $_SESSION['user_perfil'] ?? 'colaborador';
$isGestor = in_array($perfilUsuario, ['gestor_rh', 'diretor', 'admin']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>RHease - Processar Folha</title>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Estilos -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/holerites.css">
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
        <h2 class="page-title-content">Processar Folha de Pagamento</h2>

        <!-- MENSAGENS -->
        <div class="messages">
            <?php if ($msgSucesso): ?>
                <div class="alert alert-success"><?= $msgSucesso ?></div>
            <?php endif; ?>
            <?php if ($msgErro): ?>
                <div class="alert alert-danger"><?= $msgErro ?></div>
            <?php endif; ?>
        </div>

        <!-- FORMULÁRIO -->
        <section class="content-section" style="max-width: 800px;">
            <h3 style="margin-top: 0;">Selecione o Período</h3>
            <p class="text-muted mb-4">Isso irá calcular os salários de todos os colaboradores ativos.</p>

            <form action="<?= BASE_URL ?>/folha/processar" method="POST" class="processar-form">
                <div class="form-group">
                    <label for="mes">Mês:</label>
                    <input type="number" id="mes" name="mes" min="1" max="12" class="form-control" required value="<?= $mesRef ?>">
                </div>
                <div class="form-group">
                    <label for="ano">Ano:</label>
                    <input type="number" id="ano" name="ano" min="2020" max="2050" class="form-control" required value="<?= $anoRef ?>">
                </div>
                <div class="form-group">
                    <button type="submit" class="btn-action">
                        <i class="bi bi-gear-wide-connected"></i> Processar Folha
                    </button>
                </div>
            </form>
        </section>

        <!-- AVISO TÉCNICO (Caso o service retorne string em vez de array) -->
        <?php if ($erroTecnico): ?>
            <div class="alert alert-warning mt-4 border-warning">
                <h4 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Aviso Técnico</h4>
                <p>O processamento ocorreu, mas o retorno não é uma lista válida.</p>
                <hr>
                <pre class="bg-light p-2 mt-2 border rounded"><?= htmlspecialchars($debugConteudo) ?></pre>
            </div>
        <?php endif; ?>

        <!-- RESULTADOS: SUCESSO -->
        <?php if (!empty($listaSucesso)): ?>
            <section class="content-section mt-4">
                <h3>Holerites Gerados (<?= str_pad($mesRef, 2, '0', STR_PAD_LEFT) . '/' . $anoRef ?>)</h3>
                <div class="tabela-container">
                    <table>
                        <thead>
                        <tr>
                            <th>Colaborador</th>
                            <th>Salário Líquido</th>
                            <th style="text-align: center;">Ações</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($listaSucesso as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['nome'] ?? 'Desconhecido') ?></td>
                                <td>R$ <?= number_format($item['salario_liquido'] ?? 0, 2, ',', '.') ?></td>
                                <td style="text-align: center;">
                                    <form action="<?= BASE_URL ?>/holerite/gerarPDF" method="POST" target="_blank">
                                        <input type="hidden" name="mes" value="<?= $mesRef ?>">
                                        <input type="hidden" name="ano" value="<?= $anoRef ?>">
                                        <input type="hidden" name="id_colaborador" value="<?= $item['id_colaborador'] ?? 0 ?>">
                                        <button type="submit" class="btn-action btn-sm">
                                            <i class="bi bi-file-earmark-pdf"></i> PDF
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        <?php endif; ?>

        <!-- RESULTADOS: FALHAS -->
        <?php if (!empty($listaFalha)): ?>
            <section class="content-section mt-4">
                <h3 class="text-danger">Erros no Processamento</h3>
                <div class="alert alert-warning">
                    <ul class="mb-0">
                        <?php foreach ($listaFalha as $erro): ?>
                            <li><strong><?= htmlspecialchars($erro['nome'] ?? 'Desconhecido') ?>:</strong> <?= htmlspecialchars($erro['erro'] ?? 'Erro sem descrição') ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </section>
        <?php endif; ?>

    </div>
</div>

<?php if ($isGestor): ?>
    <!-- MODAL EMPRESAS -->
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
    const BASE_URL = "<?= BASE_URL ?>";
</script>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="<?= BASE_URL ?>/js/sidebar-toggle.js"></script>
<?php if ($isGestor): ?>
    <script src="<?= BASE_URL ?>/js/empresa.js"></script>
<?php endif; ?>

</body>
</html>