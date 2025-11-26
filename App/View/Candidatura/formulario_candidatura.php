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
    <title>Candidatura para: <?= htmlspecialchars($vaga['titulo_vaga']); ?></title>

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

<!-- CLASSE PRINCIPAL: .app-container -->
<div class="app-container">

    <!-- SIDEBAR -->
    <?php include BASE_PATH . '/App/View/Common/sidebar.php'; ?>

    <div class="content">
        <div class="section-header">
            <div>
                <a href="<?= BASE_URL ?>/vagas" class="btn-cancelar" style="margin-bottom: 10px; display: inline-flex;">
                    <i class="fas fa-arrow-left"></i> Voltar para Lista
                </a>
                <h2 class="page-title-content" style="margin-bottom: 5px;">Candidatura</h2>
                <h3 style="color: var(--text-color-light); font-weight: 500;"><?= htmlspecialchars($vaga['titulo_vaga']); ?></h3>
            </div>
        </div>

        <section class="content-section" style="max-width: 800px;">
            <p style="margin-bottom: 30px; color: var(--text-color-light);">Preencha os seus dados abaixo para se candidatar.</p>

            <form action="<?= BASE_URL ?>/candidatura/aplicar" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id_vaga" value="<?= htmlspecialchars($vaga['id_vaga']); ?>">

                <div class="form-group mb-4">
                    <h4 style="border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 20px; color: var(--green-dark);">Dados Pessoais</h4>
                    <div class="grid">
                        <div class="form-group">
                            <label class="form-label">Nome Completo</label>
                            <input type="text" name="nome_completo" class="form-control" placeholder="Digite o seu nome completo" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">CPF</label>
                            <input type="text" name="cpf" class="form-control" placeholder="000.000.000-00" required>
                        </div>
                    </div>
                </div>

                <div class="form-group mb-4">
                    <h4 style="border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 20px; color: var(--green-dark);">Currículo</h4>
                    <div class="grid">
                        <div class="form-group" style="grid-column: span 2;">
                            <label class="form-label">Anexe o seu currículo (PDF)</label>
                            <input type="file" name="curriculo_pdf" class="form-control" accept=".pdf" required>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <button type="submit" class="btn-salvar">Enviar Candidatura</button>
                </div>
            </form>
        </section>
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