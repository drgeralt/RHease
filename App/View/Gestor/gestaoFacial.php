<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Biometria</title>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/gestaoFacial.css">
</head>
<body>

<header>
    <div style="display:flex; align-items:center; gap:10px;">
        <i class="menu-toggle bi bi-list "></i>
        <div class="logo"><img src="<?= BASE_URL ?>/img/rhease-ease 1.png" alt="Logo" style="height:40px;"></div>
    </div>

    <!-- 1. SELETOR DE EMPRESA -->
    <div class="header-right">
        <div class="empresa-selector" onclick="abrirModalEmpresas()">
            <i class="bi bi-building"></i>
            <span id="nomeEmpresaAtiva">Carregando...</span>
            <i class="bi bi-chevron-down small"></i>
        </div>
    </div>
</header>

<div class="app-container">

    <!-- 2. SIDEBAR MODULAR -->
    <?php include BASE_PATH . '/App/View/Common/sidebar.php'; ?>

    <div class="content">
        <div class="header-tabela" style="margin-bottom: 20px;">
            <h2>Gestão de Biometria Facial</h2>
            <p style="color: var(--text-color-light);">Gerencie quais colaboradores possuem face cadastrada para o registro de ponto.</p>
        </div>

        <main class="main-content">
            <div class="tabela-container">
                <table>
                    <thead>
                    <tr>
                        <th>Colaborador</th>
                        <th>Matrícula</th>
                        <th style="text-align: center;">Status</th>
                        <th style="text-align: center;">Ação</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($colaboradores)): ?>
                        <?php foreach ($colaboradores as $c): ?>
                            <tr>
                                <td style="font-weight: 500;"><?= htmlspecialchars($c['nome_completo']) ?></td>
                                <td style="color: #666;"><?= htmlspecialchars($c['matricula']) ?></td>
                                <td style="text-align: center;">
                                    <?php if ($c['face_registered_at']): ?>
                                        <span class="badge-success">Cadastrada</span>
                                    <?php else: ?>
                                        <span class="badge-warning">Pendente</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php if ($c['face_registered_at']): ?>
                                        <button class="btn-reset" onclick="resetarFace(<?= $c['id_colaborador'] ?>)">
                                            <i class="bi bi-arrow-counterclockwise"></i> Forçar Recadastro
                                        </button>
                                    <?php else: ?>
                                        <span style="color: #999; font-size: 0.9em;">--</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="padding: 20px; text-align: center; font-style: italic;">Nenhum colaborador encontrado.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>

<!-- 3. MODAL DE EMPRESAS -->
<div id="modalEmpresas" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Perfil da Empresa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label class="form-label">Selecionar Filial/Perfil Ativo:</label>
                <div id="listaEmpresas" class="list-group mb-4">
                    <!-- Preenchido via JS -->
                </div>

                <hr>
                <h6>Editar/Criar Perfil</h6>
                <form id="formEmpresa">
                    <input type="hidden" id="empresaId" name="id">
                    <div class="row g-2">
                        <div class="col-12">
                            <input type="text" name="razao_social" class="form-control" placeholder="Razão Social" required>
                        </div>
                        <div class="col-6">
                            <input type="text" name="cnpj" class="form-control" placeholder="CNPJ" required>
                        </div>
                        <div class="col-6">
                            <input type="text" name="cidade_uf" class="form-control" placeholder="Cidade - UF">
                        </div>
                        <div class="col-12">
                            <input type="text" name="endereco" class="form-control" placeholder="Endereço Completo">
                        </div>
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
    const BASE_URL = "<?= BASE_URL ?>";
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="<?= BASE_URL ?>/js/sidebar-toggle.js"></script>

<!-- 4. SCRIPT DE EMPRESA -->
<script src="<?= BASE_URL ?>/js/empresa.js"></script>

<script>
    async function resetarFace(id) {
        if(!confirm("Tem certeza? O colaborador será obrigado a tirar uma nova foto na próxima vez que tentar bater ponto.")) return;

        const formData = new FormData();
        formData.append('id', id);

        try {
            const res = await fetch(`${BASE_URL}/gestao-facial/resetar`, {
                method: 'POST',
                body: formData
            });
            const data = await res.json();

            if(data.status === 'success') {
                alert(data.message);
                location.reload();
            } else {
                alert("Erro: " + data.message);
            }
        } catch(e) {
            console.error(e);
            alert("Erro de conexão com o servidor.");
        }
    }
</script>

</body>
</html>