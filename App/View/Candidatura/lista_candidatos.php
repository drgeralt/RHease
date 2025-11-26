<?php
// 1. Lógica de Permissão
$perfilUsuario = $_SESSION['user_perfil'] ?? 'colaborador';
$isGestor = in_array($perfilUsuario, ['gestor_rh', 'diretor', 'admin']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidatos para: <?= htmlspecialchars($vaga['titulo_vaga']); ?></title>

    <!-- Fontes -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Estilos -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/vagas.css">
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

<!-- MUDANÇA DE CLASSE: .app-container -->
<div class="app-container">

    <!-- SIDEBAR CENTRALIZADA -->
    <?php include BASE_PATH . '/App/View/Common/sidebar.php'; ?>

    <div class="content">
        <div class="section-header">
            <div>
                <a href="<?= BASE_URL ?>/vagas/listar" class="btn-cancelar" style="margin-bottom: 10px; display: inline-flex;">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
                <h2 class="page-title-content" style="margin-bottom: 5px;">Candidatos</h2>
                <h3 style="color: var(--text-color-light); font-weight: 500;"><?= htmlspecialchars($vaga['titulo_vaga']); ?></h3>
            </div>
        </div>

        <section class="content-section">
            <div class="tabela-container">
                <?php if (empty($candidatos)): ?>
                    <p class="empty-message">Nenhum candidato encontrado para esta vaga ainda.</p>
                <?php else: ?>
                    <table>
                        <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Data</th>
                            <th>Score IA</th>
                            <th>Status</th>
                            <th style="text-align: center;">Ações</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($candidatos as $candidato): ?>
                            <tr>
                                <td><?= htmlspecialchars($candidato['nome_completo']); ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($candidato['data_candidatura'])); ?></td>
                                <td>
                                    <?php if (!is_null($candidato['pontuacao_aderencia'])): ?>
                                        <span class="status-badge status-aberta"><?= htmlspecialchars($candidato['pontuacao_aderencia']); ?>%</span>
                                    <?php else: ?>
                                        <span style="color: #999;">N/A</span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="status-badge"><?= htmlspecialchars($candidato['status_triagem']); ?></span></td>
                                <td class="actions">
                                    <a href="<?= BASE_URL . htmlspecialchars($candidato['curriculo']); ?>" target="_blank" class="icon-btn icon-view" title="Ver Currículo">
                                        <i class="fas fa-download"></i>
                                    </a>

                                    <?php if (is_null($candidato['pontuacao_aderencia'])): ?>
                                        <form action="<?= BASE_URL ?>/candidatura/analisar" method="POST" style="display: inline;">
                                            <input type="hidden" name="id_candidatura" value="<?= $candidato['id_candidatura']; ?>">
                                            <button type="submit" class="icon-btn icon-edit" title="Processar com IA">
                                                <i class="fas fa-robot"></i>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <!-- Botão que abre o Modal AJAX -->
                                        <button type="button" class="icon-btn icon-view btn-ver-analise"
                                                data-id="<?= $candidato['id_candidatura']; ?>" title="Ver Análise">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </section>
    </div>
</div>

<!-- MODAL ANALISE IA (Já estava aqui, mantido) -->
<div id="modal-analise-ia" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Análise de Aderência (IA)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div id="analise-loading">
                    <div class="spinner-border text-success" role="status"></div>
                    <p class="mt-2">Consultando Inteligência Artificial...</p>
                </div>
                <div id="analise-conteudo" style="display:none;">
                    <h3 id="analise-candidato" class="mb-4 text-dark"></h3>
                    <div class="score-circle mx-auto mb-4" id="analise-score-bg">
                        <span class="score-text" id="analise-score-val">0%</span>
                    </div>
                    <div class="justificativa-box text-start">
                        <h5 class="text-primary"><i class="fas fa-robot"></i> Justificativa:</h5>
                        <p id="analise-texto" style="white-space: pre-line;"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancelar" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
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
<script src="<?= BASE_URL ?>/js/vagas-gestao.js"></script> <!-- Importante para o modal IA -->

<?php if ($isGestor): ?>
    <script src="<?= BASE_URL ?>/js/empresa.js"></script>
<?php endif; ?>

</body>
</html>