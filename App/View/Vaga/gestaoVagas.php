<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RHease - Gestão de Vagas</title>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/vagas.css">
</head>
<body>

<header>
    <div style="display:flex; align-items:center; gap:10px;">
        <i class="menu-toggle bi bi-list"></i>
        <div class="logo"><img src="<?= BASE_URL ?>/img/rhease-ease 1.png" alt="Logo RH Ease" class="logo" style="padding:0;"></div>
    </div>

    <div class="header-right">
        <div class="empresa-selector" onclick="abrirModalEmpresas()">
            <i class="bi bi-building"></i>
            <span id="nomeEmpresaAtiva">Carregando...</span>
            <i class="bi bi-chevron-down small"></i>
        </div>
    </div>
</header>

<div class="app-container">

    <?php include BASE_PATH . '/App/View/Common/sidebar.php'; ?>

    <div class="content">
        <h2 class="page-title-content">Gestão de Vagas</h2>

        <section class="content-section">
            <div class="section-header">
                <h3 class="section-title">Vagas Cadastradas</h3>
                <button id="btn-abrir-modal-criacao" class="btn-action">
                    <i class="fas fa-plus"></i> Criar Nova Vaga
                </button>
            </div>

            <div class="tabela-container">
                <table class="data-table">
                    <thead>
                    <tr>
                        <th>Título</th>
                        <th>Departamento</th>
                        <th>Status</th>
                        <th style="text-align: center;">Ações</th>
                    </tr>
                    </thead>
                    <tbody id="tabela-vagas-corpo">
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 20px;">Carregando vagas...</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>

<div id="modal-criacao-vaga" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" style="margin:0; color:var(--green-dark);">Criar Nova Vaga</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="form-criar-vaga">
                <div class="modal-body">
                    <div id="api-message-create"></div>

                    <div class="form-group">
                        <h4 style="color: var(--green-light); border-bottom: 1px solid #eee; padding-bottom: 5px; margin-bottom: 15px;">Dados da Vaga</h4>
                        <div class="grid">
                            <div class="form-group">
                                <label for="create-titulo">Título da Vaga</label>
                                <input id="create-titulo" type="text" name="titulo" required class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="create-departamento">Departamento</label>
                                <input id="create-departamento" type="text" name="departamento" required class="form-control">
                            </div>
                            <div class="form-group" style="grid-column: span 2;">
                                <label for="create-descricao">Descrição da Vaga</label>
                                <textarea id="create-descricao" name="descricao" rows="4" required class="form-control"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="create-status">Status</label>
                                <select id="create-status" name="status" required class="form-select">
                                    <option value="aberta">Aberta</option>
                                    <option value="rascunho" selected>Rascunho</option>
                                    <option value="fechada">Fechada</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <h4 style="color: var(--green-light); border-bottom: 1px solid #eee; padding-bottom: 5px; margin-bottom: 15px;">Requisitos</h4>
                        <div class="grid">
                            <div class="form-group" style="grid-column: span 2;">
                                <label for="create-skills-necessarias">Skills Necessárias</label>
                                <textarea id="create-skills-necessarias" name="skills_necessarias" rows="2" class="form-control" placeholder="Ex: PHP, SQL, Git"></textarea>
                            </div>
                            <div class="form-group" style="grid-column: span 2;">
                                <label for="create-skills-recomendadas">Skills Recomendadas</label>
                                <textarea id="create-skills-recomendadas" name="skills_recomendadas" rows="2" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancelar" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" id="btn-salvar-criacao" class="btn-salvar">Salvar Vaga</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="modal-edicao-vaga" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" style="margin:0; color:var(--green-dark);">Editar Vaga</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="form-editar-vaga">
                <div class="modal-body">
                    <div id="api-message-edit"></div>
                    <input type="hidden" id="edit-id-vaga" name="id_vaga">

                    <div class="grid">
                        <div class="form-group">
                            <label for="edit-titulo">Título da Vaga</label>
                            <input id="edit-titulo" type="text" name="titulo" required class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="edit-departamento">Departamento</label>
                            <input id="edit-departamento" type="text" name="departamento" required class="form-control">
                        </div>
                        <div class="form-group" style="grid-column: span 2;">
                            <label for="edit-descricao">Descrição</label>
                            <textarea id="edit-descricao" name="descricao" rows="4" required class="form-control"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="edit-status">Status</label>
                            <select id="edit-status" name="status" required class="form-select">
                                <option value="aberta">Aberta</option>
                                <option value="rascunho">Rascunho</option>
                                <option value="fechada">Fechada</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid mt-3">
                        <div class="form-group" style="grid-column: span 2;">
                            <label for="edit-skills-necessarias">Skills Necessárias</label>
                            <textarea id="edit-skills-necessarias" name="skills_necessarias" rows="2" class="form-control"></textarea>
                        </div>
                        <div class="form-group" style="grid-column: span 2;">
                            <label for="edit-skills-recomendadas">Skills Recomendadas</label>
                            <textarea id="edit-skills-recomendadas" name="skills_recomendadas" rows="2" class="form-control"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancelar" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" id="btn-salvar-edicao" class="btn-salvar">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="modal-candidatos" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modal-titulo-vaga" class="modal-title" style="margin:0; color:var(--green-dark);">Candidatos</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="tabela-container">
                    <table class="data-table">
                        <thead>
                        <tr>
                            <th>Candidato</th>
                            <th>Data</th>
                            <th>Score IA</th>
                            <th>Ações</th>
                        </tr>
                        </thead>
                        <tbody id="modal-tabela-candidatos">
                        <tr><td colspan="4" style="text-align: center;">Carregando...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

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

<script>
    const BASE_URL = "<?php echo BASE_URL; ?>";
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="<?php echo BASE_URL; ?>/js/sidebar-toggle.js"></script>
<script src="<?php echo BASE_URL; ?>/js/vagas-gestao.js"></script>
<script src="<?php echo BASE_URL; ?>/js/empresa.js"></script>

</body>
</html>