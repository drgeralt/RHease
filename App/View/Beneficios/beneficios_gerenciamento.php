<?php
// 1. Funções Auxiliares
function formatarValor($valor) {
    if (is_numeric($valor) && $valor > 0) {
        return 'R$' . number_format((float)$valor, 2, ',', '.');
    }
    return '';
}

// 2. Inicialização de Variáveis
$beneficios = $beneficios ?? [];
$beneficios_selecao = $beneficios_selecao ?? [];
$regras = $regras ?? [];
$tiposContrato = $tiposContrato ?? ["CLT", "PJ", "Estágio", "Temporário"];

// 3. Lógica de Permissão (Para exibir seletor de empresa)
$perfilUsuario = $_SESSION['user_perfil'] ?? 'colaborador';
$isGestor = in_array($perfilUsuario, ['gestor_rh', 'diretor', 'admin']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RHease - Gerenciamento de Benefícios</title>

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
        <!-- SELETOR DE EMPRESA -->
        <div class="header-right">
            <div class="empresa-selector" onclick="abrirModalEmpresas()">
                <i class="bi bi-building"></i>
                <span id="nomeEmpresaAtiva">Carregando...</span>
                <i class="bi bi-chevron-down small"></i>
            </div>
        </div>
    <?php endif; ?>
</header>

<!-- CLASSE CORRETA: .app-container -->
<div class="app-container">

    <!-- SIDEBAR CENTRALIZADA -->
    <?php include BASE_PATH . '/App/View/Common/sidebar.php'; ?>

    <div class="content">

        <!-- === 1. CATÁLOGO DE BENEFÍCIOS === -->
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
                    <th class="col-acoes" style="text-align: center;">Ações</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($beneficios as $b):
                    $tipo_valor_exibicao = $b['tipo_valor'];
                    if (!empty($b['valor_fixo']) && $b['tipo_valor'] === 'Fixo') {
                        $tipo_valor_exibicao = "Fixo (" . formatarValor($b['valor_fixo']) . ")";
                    }
                    ?>
                    <tr data-id="<?= $b['id_beneficio'] ?>"
                        data-tipo-valor-puro="<?= $b['tipo_valor'] ?>"
                        data-valor-fixo="<?= $b['valor_fixo'] ?? '' ?>">
                        <td><?= htmlspecialchars($b['nome']) ?></td>
                        <td><?= htmlspecialchars($b['categoria']) ?></td>
                        <td><?= htmlspecialchars($tipo_valor_exibicao) ?></td>
                        <td><?= htmlspecialchars($b['status']) ?></td>
                        <td class="acoes">
                            <i class="bi bi-pencil-square editar" title="Editar"></i>
                            <i class="bi bi-trash3-fill deletar" title="Excluir"></i>
                            <label class="switch" title="Ativar/Desativar">
                                <input type="checkbox" <?= $b['status'] === 'Ativo' ? 'checked' : '' ?>>
                                <span class="slider round"></span>
                            </label>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- === 2. REGRAS AUTOMÁTICAS === -->
        <div class="secao-regras">
            <h2>Regras de Atribuição Automática</h2>
            <div class="tabela-container regras-container">
                <table class="regras-tabela">
                    <thead>
                    <tr>
                        <th>Tipo de Contrato</th>
                        <th>Benefícios Padrão</th>
                        <th style="text-align:center;">Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($tiposContrato as $tipo):
                        $regra_data = $regras[$tipo] ?? null;
                        $lista_nomes = $regra_data ? implode(", ", $regra_data['nomes']) : 'Nenhum benefício padrão';
                        $lista_ids = $regra_data ? implode(",", $regra_data['ids']) : '';
                        ?>
                        <tr data-tipo-contrato="<?= htmlspecialchars($tipo) ?>" data-beneficios-ids="<?= htmlspecialchars($lista_ids) ?>">
                            <td><?= htmlspecialchars($tipo) ?></td>
                            <td><?= htmlspecialchars($lista_nomes) ?></td>
                            <td style="text-align:center;">
                                <i class="bi bi-pencil-square editar-regra"
                                   style="color:#007bff; cursor:pointer; font-size:1.1em;"
                                   title="Editar Regras">
                                </i>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- === 3. EXCEÇÕES DE COLABORADOR === -->
        <div class="secao-colaborador">
            <h2>Gerenciar Exceções de Colaborador</h2>
            <div class="mb-3 busca-colaborador">
                <label for="inputPesquisarColaborador" class="form-label">Buscar Colaborador (Nome ou Matrícula):</label>
                <input type="text" id="inputPesquisarColaborador" class="form-control" placeholder="Digite no mínimo 3 caracteres...">
            </div>

            <div id="resultadoPesquisa"></div>
        </div>

    </div>
</div>

<!-- ================= MODAIS DO SISTEMA ================= -->

<!-- Modal 1: Benefício (Catálogo) -->
<div class="modal fade" id="modalBeneficio" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tituloModal">Novo Benefício</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formBeneficio">
                    <input type="hidden" id="beneficioId">
                    <div class="mb-3">
                        <label for="nomeBeneficio" class="form-label">Nome do Benefício <span class="required">*</span></label>
                        <input type="text" id="nomeBeneficio" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="categoriaBeneficio" class="form-label">Categoria <span class="required">*</span></label>
                        <select id="categoriaBeneficio" class="form-select" required>
                            <option value="">Selecione</option>
                            <option value="Alimentação">Alimentação</option>
                            <option value="Transporte">Transporte</option>
                            <option value="Saúde">Saúde</option>
                            <option value="Educação">Educação</option>
                            <option value="Outros">Outros</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="tipoValorBeneficio" class="form-label">Tipo de Valor <span class="required">*</span></label>
                        <select id="tipoValorBeneficio" class="form-select" required>
                            <option value="">Selecione</option>
                            <option value="Fixo">Fixo</option>
                            <option value="Variável">Variável</option>
                            <option value="Descritivo">Descritivo (Sem Valor)</option>
                        </select>
                    </div>
                    <div id="valorFixoField" class="mb-3" style="display: none;">
                        <label for="valorFixoBeneficio" class="form-label">Valor Fixo (R$) <span class="required">*</span></label>
                        <input type="number" id="valorFixoBeneficio" class="form-control" step="0.01" min="0">
                    </div>
                    <div id="descricaoField" class="mb-3" style="display: none;">
                        <label for="descricaoBeneficio" class="form-label">Descrição (Opcional)</label>
                        <textarea id="descricaoBeneficio" class="form-control" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancelar" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn-salvar" id="btnSalvarBeneficio">Salvar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal 2: Regras -->
<div class="modal fade" id="modalRegras" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tituloRegrasModal">Regras de Atribuição</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formRegras">
                    <div class="mb-3">
                        <label class="form-label">Tipo de Contrato:</label>
                        <input type="text" id="contratoRegraAtual" class="form-control" readonly style="background-color: #f5f5f5;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Selecione os Benefícios Padrão:</label>
                        <div id="listaBeneficiosRegra">
                            <?php foreach($beneficios_selecao as $b): ?>
                                <label>
                                    <input type="checkbox" name="beneficios_selecionados[]" value="<?= $b['id_beneficio'] ?>">
                                    <?= htmlspecialchars($b['nome']) ?>
                                </label>
                            <?php endforeach; ?>
                            <?php if (empty($beneficios_selecao)): ?>
                                <p>Nenhum benefício ativo no catálogo.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancelar" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn-salvar" id="btnSalvarRegras">Salvar Regras</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal 3: Exceções (Colaborador) -->
<div class="modal fade" id="modalExcecoes" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Gerenciar Exceções de Colaborador</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-light border mb-3">
                    <div class="row">
                        <div class="col-md-6"><strong>Colaborador:</strong> <span id="nomeColaboradorEdicao">...</span></div>
                        <div class="col-md-6"><strong>Matrícula:</strong> <span id="matriculaColaborador">...</span></div>
                        <div class="col-md-12 mt-2"><strong>Contrato Base:</strong> <span id="contratoColaborador">...</span></div>
                    </div>
                </div>

                <p class="text-danger small mb-3">* Marque os benefícios para ATRIBUIR MANUALMENTE (Exceção) ou desmarque para REMOVER.</p>

                <form id="formBeneficiosColaborador">
                    <input type="hidden" id="colaboradorIdAtual">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Selecione os Benefícios:</label>
                        <div id="listaBeneficiosColaborador">
                            <?php if (empty($beneficios_selecao)): ?>
                                <p class="text-muted">Nenhum benefício ativo no catálogo.</p>
                            <?php else: ?>
                                <?php foreach($beneficios_selecao as $b): ?>
                                    <label class="d-flex align-items-center mb-2" style="cursor:pointer;">
                                        <input type="checkbox" class="form-check-input me-2"
                                               name="beneficios_selecionados_colaborador[]"
                                               value="<?= $b['id_beneficio'] ?>">
                                        <?= htmlspecialchars($b['nome']) ?>
                                    </label>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancelar" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn-salvar" id="btnSalvarExcecoes">Salvar Atribuição Manual</button>
            </div>
        </div>
    </div>
</div>

<?php if ($isGestor): ?>
    <!-- Modal 4: Empresas -->
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

<script src="<?php echo BASE_URL; ?>/js/sidebar-toggle.js"></script>
<script src="<?php echo BASE_URL; ?>/js/beneficios.js"></script>

<?php if ($isGestor): ?>
    <script src="<?php echo BASE_URL; ?>/js/empresa.js"></script>
<?php endif; ?>

</body>
</html>