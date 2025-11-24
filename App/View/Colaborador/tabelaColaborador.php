<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Colaboradores</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">

    <link rel="stylesheet" href="<?= BASE_URL ?>/css/colaboradores.css">
</head>
<body>

<header>
    <i class="bi bi-list menu-toggle"></i>
    <div class="logo"><img src="<?= BASE_URL ?>/img/rhease-ease 1.png" alt="Logo RH Ease" class="logo" style="padding:0;"></div>
</header>

<div class="container"> <div class="sidebar">
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
        <div class="header-tabela">
            <h2>Colaboradores</h2>
            <button type="button" class="btn-adicionar" onclick="abrirModalCriar()">
                <i class="bi bi-plus-lg"></i> Novo Colaborador
            </button>
        </div>

        <div class="tabela-container p-3"> <div class="row mb-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="search" id="searchInput" class="form-control border-start-0" placeholder="Pesquisar por nome, cargo ou matrícula...">
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle" id="tabelaColaboradores">
                    <thead> <tr>
                        <th>Matrícula</th>
                        <th>Nome</th>
                        <th>Cargo</th>
                        <th>Departamento</th>
                        <th>Status</th>
                        <th class="text-end">Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($colaboradores)): ?>
                        <?php foreach ($colaboradores as $c): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($c['id_colaborador']); ?></td>
                                <td class="fw-medium"><?php echo htmlspecialchars($c['nome_completo']); ?></td>
                                <td><?php echo htmlspecialchars($c['cargo'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($c['departamento'] ?? '-'); ?></td>
                                <td>
                                    <?php
                                    $statusClass = ($c['situacao'] === 'ativo') ? 'success' : 'secondary';
                                    echo "<span class='badge bg-{$statusClass}'>" . ucfirst($c['situacao']) . "</span>";
                                    ?>
                                </td>
                                <td class="text-end">
                                    <div class="acoes justify-content-end">
                                        <i class="bi bi-pencil-square editar" onclick="abrirModalEditar(<?php echo $c['id_colaborador']; ?>)" title="Editar"></i>

                                        <i class="bi bi-arrow-repeat text-warning" style="cursor:pointer; font-size:1.1em;"
                                           onclick="abrirModalStatus(<?php echo $c['id_colaborador']; ?>, '<?php echo addslashes($c['nome_completo']); ?>', '<?php echo $c['situacao']; ?>', this)"
                                           title="Alterar Status"></i>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center py-4">Nenhum colaborador encontrado.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalColaborador" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle" style="margin:0; color:var(--green-dark);">Adicionar Colaborador</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="formColaborador" method="POST" action="">
                    <input type="hidden" name="id_colaborador" id="id_colaborador">

                    <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="pessoal-tab" data-bs-toggle="tab" data-bs-target="#tab-pessoal" type="button" role="tab">Dados Pessoais</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="endereco-tab" data-bs-toggle="tab" data-bs-target="#tab-endereco" type="button" role="tab">Endereço</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="profissional-tab" data-bs-toggle="tab" data-bs-target="#tab-profissional" type="button" role="tab">Profissional</button>
                        </li>
                    </ul>

                    <div class="tab-content" id="myTabContent">

                        <div class="tab-pane fade show active" id="tab-pessoal" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-md-6 form-group">
                                    <label class="form-label">Nome Completo *</label>
                                    <input type="text" class="form-control" name="nome_completo" id="nome_completo" required>
                                </div>
                                <div class="col-md-3 form-group">
                                    <label class="form-label">CPF *</label>
                                    <input type="text" class="form-control" name="cpf" id="cpf" required oninput="mascaraCPF(this)">
                                </div>
                                <div class="col-md-3 form-group">
                                    <label class="form-label">RG</label>
                                    <input type="text" class="form-control" name="rg" id="rg">
                                </div>
                                <div class="col-md-3 form-group">
                                    <label class="form-label">Data Nasc. *</label>
                                    <input type="date" class="form-control" name="data_nascimento" id="data_nascimento" required>
                                </div>
                                <div class="col-md-3 form-group">
                                    <label class="form-label">Gênero</label>
                                    <select class="form-select" name="genero" id="genero">
                                        <option value="">Selecione</option>
                                        <option value="Feminino">Feminino</option>
                                        <option value="Masculino">Masculino</option>
                                        <option value="Outro">Outro</option>
                                        <option value="Prefiro não informar">Prefiro não informar</option>
                                    </select>
                                </div>
                                <div class="col-md-3 form-group">
                                    <label class="form-label">Celular</label>
                                    <input type="text" class="form-control" name="telefone" id="telefone" oninput="mascaraTelefone(this)">
                                </div>
                                <div class="col-md-3 form-group">
                                    <label class="form-label">E-mail Pessoal *</label>
                                    <input type="email" class="form-control" name="email_pessoal" id="email_pessoal" required>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="tab-endereco" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-md-3 form-group">
                                    <label class="form-label">CEP</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="CEP" id="CEP" oninput="mascaraCEP(this)" onblur="buscaCEP(this.value)">
                                        <button class="btn btn-outline-secondary" type="button" onclick="buscaCEP(document.getElementById('CEP').value)"><i class="bi bi-search"></i></button>
                                    </div>
                                </div>
                                <div class="col-md-7 form-group">
                                    <label class="form-label">Logradouro</label>
                                    <input type="text" class="form-control" name="logradouro" id="logradouro">
                                </div>
                                <div class="col-md-2 form-group">
                                    <label class="form-label">Número</label>
                                    <input type="text" class="form-control" name="numero" id="numero">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label">Bairro</label>
                                    <input type="text" class="form-control" name="bairro" id="bairro">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label">Cidade</label>
                                    <input type="text" class="form-control" name="cidade" id="cidade">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label">Estado</label>
                                    <select class="form-select" name="estado" id="estado">
                                        <option value="">UF</option>
                                        <option value="AC">AC</option><option value="AL">AL</option><option value="AP">AP</option><option value="AM">AM</option><option value="BA">BA</option><option value="CE">CE</option><option value="DF">DF</option><option value="ES">ES</option><option value="GO">GO</option><option value="MA">MA</option><option value="MT">MT</option><option value="MS">MS</option><option value="MG">MG</option><option value="PA">PA</option><option value="PB">PB</option><option value="PR">PR</option><option value="PE">PE</option><option value="PI">PI</option><option value="RJ">RJ</option><option value="RN">RN</option><option value="RS">RS</option><option value="RO">RO</option><option value="RR">RR</option><option value="SC">SC</option><option value="SP">SP</option><option value="SE">SE</option><option value="TO">TO</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="tab-profissional" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-md-3 form-group">
                                    <label class="form-label">Matrícula</label>
                                    <input type="text" class="form-control" name="matricula" id="matricula">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="form-label">Cargo *</label>
                                    <input type="text" class="form-control" name="cargo" id="cargo" required>
                                </div>
                                <div class="col-md-5 form-group">
                                    <label class="form-label">Departamento *</label>
                                    <input type="text" class="form-control" name="departamento" id="departamento" required>
                                </div>
                                <div class="col-md-3 form-group">
                                    <label class="form-label">Salário (R$) *</label>
                                    <input type="text" class="form-control" name="salario" id="salario" required>
                                </div>
                                <div class="col-md-3 form-group">
                                    <label class="form-label">Admissão *</label>
                                    <input type="date" class="form-control" name="data_admissao" id="data_admissao" required>
                                </div>
                                <div class="col-md-3 form-group">
                                    <label class="form-label">Contrato</label>
                                    <select class="form-select" name="tipo_contrato" id="tipo_contrato">
                                        <option value="CLT">CLT</option>
                                        <option value="PJ">PJ</option>
                                        <option value="Estágio">Estágio</option>
                                        <option value="Temporário">Temporário</option>
                                    </select>
                                </div>
                                <div class="col-md-3 form-group">
                                    <label class="form-label">Situação</label>
                                    <select class="form-select" name="situacao" id="situacao">
                                        <option value="ativo">Ativo</option>
                                        <option value="inativo">Inativo</option>
                                        <option value="ferias">Férias</option>
                                        <option value="licença">Licença</option>
                                    </select>
                                </div>
                                <div class="col-md-12 form-group">
                                    <label class="form-label">E-mail Corporativo</label>
                                    <input type="email" class="form-control" name="email_corporativo" id="email_corporativo">
                                </div>
                            </div>
                        </div>
                    </div> </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-cancelar" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="formColaborador" class="btn-salvar" id="btnSalvar">Salvar Mudanças</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalStatus" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Alterar Status</h3>
                <span class="close-btn" data-bs-dismiss="modal">&times;</span>
            </div>
            <div class="modal-body">
                <p>Você deseja realmente <strong id="acao_status"></strong> o colaborador <strong id="nome_colaborador_status"></strong>?</p>
                <form id="formStatus" method="POST">
                    <input type="hidden" name="id" id="id_status">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancelar" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="formStatus" class="btn-salvar">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<script>const BASE_URL = "<?php echo BASE_URL; ?>";</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo BASE_URL; ?>/js/sidebar-toggle.js"></script>
<script src="<?php echo BASE_URL; ?>/js/colaboradores.js"></script>

</body>
</html>