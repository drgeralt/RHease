<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RHease - Gestão de Vagas</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/gestaoVagas.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
</head>
<body>
    <header>
        <i class="menu-toggle bi bi-list "></i>
        <img src="<?= BASE_URL ?>/img/rhease-ease 1.png" alt="Logo RH ease" class="logo">
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
            <h2 class="page-title-content">Gestão de Vagas</h2>

            <section class="content-section">
                <div class="section-header">
                    <h2 class="section-title">Gestão de Vagas</h2>
                   <button id="btn-abrir-modal-criacao" class="btn btn-primary">
    <i class="fas fa-plus"></i>
    Criar Nova Vaga
</button>
                </div>

                <div class="table-container">
                    
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Departamento</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        
                        <tbody id="tabela-vagas-corpo">
                            <tr>
                                <td colspan="4" style="text-align: center;">Carregando vagas...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="content-section">
                <div class="section-header">
                    <h2 class="section-title">Regras de Atribuição Automática</h2>
                </div>

                <div class="table-container">
                    <table class="data-table">
                        <thead>
                        <tr>
                            <th>Tipo de Contrato <i class="fas fa-sort"></i></th>
                            <th>Benefícios Padrão <i class="fas fa-sort"></i></th>
                            <th>Ações <i class="fas fa-sort"></i></th>
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

<script>
    const BASE_URL = "<?php echo BASE_URL; ?>";
</script>
<script src="<?php echo BASE_URL; ?>/js/sidebar-toggle.js"></script>
<script src="<?php echo BASE_URL; ?>/js/vagas-gestao.js"></script>

<div id="modal-backdrop" class="modal-backdrop"></div>

<div id="modal-candidatos" class="modal-container">
    <div class="modal-header">
        <h2 id="modal-titulo-vaga">Carregando...</h2>
        <button id="modal-close-btn" class="modal-close">&times;</button>
    </div>
    <div class="modal-body">
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nome do Candidato</th>
                        <th>Data da Candidatura</th>
                        <th>Score IA</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="modal-tabela-candidatos">
                    <tr><td colspan="4" style="text-align: center;">Carregando candidatos...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div id="modal-edicao-vaga" class="modal-container modal-lg"> <form id="form-editar-vaga" class="modal-form">
        <div class="modal-header">
            <h2 id="modal-edicao-titulo">Editar Vaga</h2>
            <button type="button" id="modal-edicao-close-btn" class="modal-close">&times;</button>
        </div>
        
        <div class="modal-body">
            <div id="api-message-edit"></div>

            <input type="hidden" id="edit-id-vaga" name="id_vaga">

            <section>
                <h3>Dados da Vaga</h3>
                <div class="grid">
                    <div>
                        <label for="edit-titulo">Título da Vaga</label>
                        <input id="edit-titulo" type="text" name="titulo" required>
                    </div>
                    <div>
                        <label for="edit-departamento">Departamento</label>
                        <input id="edit-departamento" type="text" name="departamento" required>
                    </div>
                    <div style="grid-column: span 2;">
                        <label for="edit-descricao">Descrição da Vaga</label>
                        <textarea id="edit-descricao" name="descricao" rows="5" required></textarea>
                    </div>
                    <div>
                        <label for="edit-status">Status da Vaga</label>
                        <select id="edit-status" name="status" required>
                            <option value="aberta">Aberta</option>
                            <option value="rascunho">Rascunho</option>
                            <option value="fechada">Fechada</option>
                        </select>
                    </div>
                </div>
            </section>
            
            <section>
                <h3>Requisitos e Skills</h3>
                <div class="grid">
                    <div style="grid-column: span 2;">
                        <label for="edit-skills-necessarias">Skills Necessárias</label>
                        <textarea id="edit-skills-necessarias" name="skills_necessarias" rows="3"></textarea>
                    </div>
                    <div style="grid-column: span 2;">
                        <label for="edit-skills-recomendadas">Skills Recomendadas</label>
                        <textarea id="edit-skills-recomendadas" name="skills_recomendadas" rows="3"></textarea>
                    </div>
                   <div style="grid-column: span 2;">
                        <label for="edit-skills-desejadas">Skills Desejadas (Opcional)</label>
                        <textarea id="edit-skills-desejadas" name="skills_desejadas" rows="3"></textarea>
                    </div>
                </div>
            </section>
        </div>

        <div class="modal-footer">
            <button type="button" id="modal-edicao-cancel-btn" class="btn-cancelar">Cancelar</button>
            <button type="submit" id="btn-salvar-edicao" class="btn-salvar">Salvar Alterações</button>
        </div>
    </form>
</div>

<div id="modal-criacao-vaga" class="modal-container modal-lg">
    
    <form id="form-criar-vaga" class="modal-form">
        <div class="modal-header">
            <h2>Criar Nova Vaga</h2>
            <button type="button" id="modal-criacao-close-btn" class="modal-close">&times;</button>
        </div>
        
        <div class="modal-body">
            <div id="api-message-create"></div>

            <section>
                <h3>Dados da Vaga</h3>
                <div class="grid">
                    <div>
                        <label for="create-titulo">Título da Vaga</label>
                        <input id="create-titulo" type="text" name="titulo" required>
                    </div>
                    <div>
                        <label for="create-departamento">Departamento</label>
                        <input id="create-departamento" type="text" name="departamento" required>
                    </div>
                    <div style="grid-column: span 2;">
                        <label for="create-descricao">Descrição da Vaga</label>
                        <textarea id="create-descricao" name="descricao" rows="5" required></textarea>
                    </div>
                    <div>
                        <label for="create-status">Status da Vaga</label>
                        <select id="create-status" name="status" required>
                            <option value="aberta">Aberta</option>
                            <option value="rascunho" selected>Rascunho</option>
                            <option value="fechada">Fechada</option>
                        </select>
                    </div>
                </div>
            </section>
            
            <section>
                <h3>Requisitos e Skills</h3>
                <div class="grid">
                    <div style="grid-column: span 2;">
                        <label for="create-skills-necessarias">Skills Necessárias</label>
                        <textarea id="create-skills-necessarias" name="skills_necessarias" rows="3"></textarea>
                    </div>
                    <div style="grid-column: span 2;">
                        <label for="create-skills-recomendadas">Skills Recomendadas</label>
                        <textarea id="create-skills-recomendadas" name="skills_recomendadas" rows="3"></textarea>
                    </div>
                   <div style="grid-column: span 2;">
                        <label for="create-skills-desejadas">Skills Desejadas (Opcional)</label>
                        <textarea id="create-skills-desejadas" name="skills_desejadas" rows="3"></textarea>
                    </div>
                </div>
            </section>
        </div>

        <div class="modal-footer">
            <button type="button" id="modal-criacao-cancel-btn" class="btn-cancelar">Cancelar</button>
            <button type="submit" id="btn-salvar-criacao" class="btn-salvar">Salvar Vaga</button>
        </div>
    </form>
</div>
</body>
</html>



