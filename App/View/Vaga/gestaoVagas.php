<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Gestão de Vagas</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/gestaoVagas.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Adicionado para garantir que os formulários de ação fiquem na mesma linha */
        .actions form {
            display: inline-block;
            margin: 0 2px;
        }
    </style>
</head>
<body>
<div class="app-container">
    <header class="header">
        <div class="header-left">
            <button class="menu-toggle">
                <i class="fas fa-bars"></i>
            </button>
            <div class="logo">
                <img src="<?php echo BASE_URL; ?>/img/rhease-ease 1.png" alt="RH ease" class="logo-img">
            </div>
        </div>
        <div class="header-right">
            <div class="user-info">
                <img src="<?php echo BASE_URL; ?>/img/user.png" alt=" usuario">
                <span class="user-name">Vitoria Leal</span>
            </div>
        </div>

    </header>

    <div class="main-container">
        <!-- sidebar -->

        <div class="sidebar-container">
            <aside class="sidebar">
                <nav class="sidebar-nav">
                    <ul class="nav-list">
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-chart-bar"></i>
                                <span>Painel</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-database"></i>
                                <span>Dados cadastrais</span>
                            </a>
                        </li>
                        <li class="nav-item active">
                            <a href="#" class="nav-link">
                                <i class="fas fa-users"></i>
                                <span>Recrutamento</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-credit-card"></i>
                                <span>Pagamento</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-gift"></i>
                                <span>Benefícios</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-clock"></i>
                                <span>Frequência</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </aside>
        </div>

        <!-- Main Content -->
        <main class="main-content">
            <div class="page-header">
                <h1 class="page-title">Gestão de Vagas</h1>
            </div>

            <section class="content-section">
                <div class="section-header">
                    <h2 class="section-title">Gestão de Vagas</h2>
                    <a href = "<?php echo BASE_URL;?>/vagas/criar" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Criar Nova Vaga
                    </a>
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
                                    <a href="<?php echo BASE_URL; ?>/vagas/editar?id=<?php echo $vaga['id_vaga']; ?>" class="icon-btn icon-edit" title="Editar Vaga">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <a href="<?php echo BASE_URL; ?>/vagas/excluir?id=<?php echo $vaga['id_vaga']; ?>" class="icon-btn icon-delete" title="Excluir Vaga" onclick="return confirm('Tem certeza que deseja excluir esta vaga? Esta ação não pode ser desfeita.');">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>

                                    <form action="<?php echo BASE_URL; ?>/vagas/candidatos" method="POST">
                                        <input type="hidden" name="id" value="<?php echo $vaga['id_vaga']; ?>">
                                        <button type="submit" class="icon-btn icon-view" title="Ver Candidatos">
                                            <i class="fas fa-search"></i> </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
</div>

<script>
    const BASE_URL = "<?php echo BASE_URL; ?>";
</script>
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
</body>
</html>



