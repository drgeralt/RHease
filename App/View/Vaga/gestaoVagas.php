<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RHease - Gestão de Vagas</title>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">

    <link rel="stylesheet" href="<?= BASE_URL ?>/css/vagas.css">
</head>
<body>

<header>
    <i class="menu-toggle bi bi-list"></i>
    <div class="logo"><img src="<?= BASE_URL ?>/img/rhease-ease 1.png" alt="Logo RH Ease" class="logo" style="padding:0;"></div>
</header>

<div class="container">
    <div class="sidebar">
        <ul class="menu">
            <li><a href="<?= BASE_URL ?>/inicio"><i class="bi bi-clipboard-data-fill"></i> Painel</a></li>
            <li><a href="<?= BASE_URL ?>/colaboradores"><i class="bi bi-person-vcard-fill"></i> Colaboradores</a></li>
            <li><a href="<?= BASE_URL ?>/registrarponto"><i class="bi bi-calendar2-check-fill"></i> Frequência</a></li>
            <li><a href="<?= BASE_URL ?>/gestao-facial"><i class="bi bi-person-bounding-box"></i> Biometria Facial</a></li>
            <li><a href="<?= BASE_URL ?>/meus-holerites"><i class="bi bi-wallet-fill"></i> Salário</a></li>
            <li><a href="<?= BASE_URL ?>/beneficios"><i class="bi bi-shield-fill-check"></i> Benefícios</a></li>
            <li><a href="<?= BASE_URL ?>/vagas/listar"><i class="bi bi-briefcase-fill"></i> Gestão de Vagas</a></li>
            <li><a href="<?= BASE_URL ?>/contato"><i class="bi bi-person-lines-fill"></i> Contato</a></li>
        </ul>
    </div>

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

        <section class="content-section">
            <div class="section-header">
                <h3 class="section-title">Regras de Atribuição Automática</h3>
            </div>
            <div class="tabela-container">
                <table class="data-table">
                    <thead>
                    <tr>
                        <th>Tipo de Contrato</th>
                        <th>Benefícios Padrão</th>
                        <th style="text-align: center;">Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>CLT</td>
                        <td>Vale Transporte, Vale Refeição</td>
                        <td class="actions">
                            <div class="action-icons">
                                <button class="icon-btn icon-edit" title="Editar Regra">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>

<div id="modal-criacao-vaga" class="modal">
    <div class="modal-content modal-lg"> <div class="modal-header">
            <h3>Criar Nova Vaga</h3>
            <span id="modal-criacao-close-btn" class="close-btn">&times;</span>
        </div>

        <form id="form-criar-vaga">
            <div class="modal-body">
                <div id="api-message-create"></div>

                <div class="form-group">
                    <h4 style="color: var(--green-light); border-bottom: 1px solid #eee; padding-bottom: 5px; margin-bottom: 15px;">Dados da Vaga</h4>
                    <div class="grid">
                        <div class="form-group">
                            <label for="create-titulo">Título da Vaga</label>
                            <input id="create-titulo" type="text" name="titulo" required>
                        </div>
                        <div class="form-group">
                            <label for="create-departamento">Departamento</label>
                            <input id="create-departamento" type="text" name="departamento" required>
                        </div>
                        <div class="form-group" style="grid-column: span 2;">
                            <label for="create-descricao">Descrição da Vaga</label>
                            <textarea id="create-descricao" name="descricao" rows="4" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="create-status">Status</label>
                            <select id="create-status" name="status" required>
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
                            <textarea id="create-skills-necessarias" name="skills_necessarias" rows="2" placeholder="Ex: PHP, SQL, Git"></textarea>
                        </div>
                        <div class="form-group" style="grid-column: span 2;">
                            <label for="create-skills-recomendadas">Skills Recomendadas</label>
                            <textarea id="create-skills-recomendadas" name="skills_recomendadas" rows="2"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" id="modal-criacao-cancel-btn" class="btn-cancelar">Cancelar</button>
                <button type="submit" id="btn-salvar-criacao" class="btn-salvar">Salvar Vaga</button>
            </div>
        </form>
    </div>
</div>

<div id="modal-edicao-vaga" class="modal">
    <div class="modal-content modal-lg">
        <div class="modal-header">
            <h3>Editar Vaga</h3>
            <span id="modal-edicao-close-btn" class="close-btn">&times;</span>
        </div>

        <form id="form-editar-vaga">
            <div class="modal-body">
                <div id="api-message-edit"></div>
                <input type="hidden" id="edit-id-vaga" name="id_vaga">

                <div class="grid">
                    <div class="form-group">
                        <label for="edit-titulo">Título da Vaga</label>
                        <input id="edit-titulo" type="text" name="titulo" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-departamento">Departamento</label>
                        <input id="edit-departamento" type="text" name="departamento" required>
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label for="edit-descricao">Descrição</label>
                        <textarea id="edit-descricao" name="descricao" rows="4" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="edit-status">Status</label>
                        <select id="edit-status" name="status" required>
                            <option value="aberta">Aberta</option>
                            <option value="rascunho">Rascunho</option>
                            <option value="fechada">Fechada</option>
                        </select>
                    </div>
                </div>

                <div class="grid mt-3">
                    <div class="form-group" style="grid-column: span 2;">
                        <label for="edit-skills-necessarias">Skills Necessárias</label>
                        <textarea id="edit-skills-necessarias" name="skills_necessarias" rows="2"></textarea>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" id="modal-edicao-cancel-btn" class="btn-cancelar">Cancelar</button>
                <button type="submit" id="btn-salvar-edicao" class="btn-salvar">Salvar Alterações</button>
            </div>
        </form>
    </div>
</div>

<div id="modal-candidatos" class="modal">
    <div class="modal-content modal-lg">
        <div class="modal-header">
            <h3 id="modal-titulo-vaga">Candidatos</h3>
            <span id="modal-close-btn" class="close-btn">&times;</span>
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

<script>
    const BASE_URL = "<?php echo BASE_URL; ?>";

    // Pequeno script para reativar o comportamento do modal já que mudamos as classes
    // O seu arquivo vagas-gestao.js deve estar esperando classes/IDs específicos.
    // Certifique-se de que os IDs (modal-criacao-vaga, btn-abrir-modal-criacao) batem com o JS.
</script>
<script src="<?php echo BASE_URL; ?>/js/sidebar-toggle.js"></script>
<script src="<?php echo BASE_URL; ?>/js/vagas-gestao.js"></script>

</body>
</html>