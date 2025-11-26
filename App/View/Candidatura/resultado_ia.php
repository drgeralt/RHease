<?php
// Verifica permissão para exibir o seletor de empresa
$perfilUsuario = $_SESSION['user_perfil'] ?? 'colaborador';
$isGestor = in_array($perfilUsuario, ['gestor_rh', 'diretor', 'admin']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Análise de IA</title>

    <!-- Bootstrap (Necessário para o layout e modal) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Fontes -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

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
        <div class="header-right">
            <div class="empresa-selector" onclick="abrirModalEmpresas()">
                <i class="bi bi-building"></i>
                <span id="nomeEmpresaAtiva">Carregando...</span>
                <i class="bi bi-chevron-down small"></i>
            </div>
        </div>
    <?php endif; ?>
</header>

<!-- ALTERADO DE .container PARA .app-container -->
<div class="app-container">

    <!-- SIDEBAR -->
    <?php include BASE_PATH . '/App/View/Common/sidebar.php'; ?>

    <div class="content">
        <div class="page-header">
            <a href="<?= BASE_URL ?>/vagas/listar" class="btn-cancelar">
                <i class="fas fa-arrow-left"></i> Voltar para Gestão
            </a>
        </div>

        <section class="content-section analise-container">
            <div class="analise-header">
                <h2>Candidato(a): <span style="color: var(--text-color-dark);"><?= htmlspecialchars($candidatura['nome_completo']); ?></span></h2>
                <h3>Vaga: <?= htmlspecialchars($candidatura['titulo_vaga']); ?></h3>
            </div>

            <div class="score-display">
                <div class="score-circle" style="background: conic-gradient(
                        var(--green-light) <?= ($candidatura['pontuacao_aderencia'] ?? 0) * 3.6; ?>deg,
                        #e9ecef <?= ($candidatura['pontuacao_aderencia'] ?? 0) * 3.6; ?>deg
                        );">
                    <span class="score-text"><?= htmlspecialchars($candidatura['pontuacao_aderencia'] ?? 0); ?>%</span>
                </div>
            </div>

            <div class="justificativa-box">
                <h4 style="color: var(--blue-color); margin-top:0;">Justificativa da IA</h4>
                <p><?= nl2br(htmlspecialchars($candidatura['justificativa_ia'] ?? 'Justificativa não disponível.')); ?></p>
            </div>
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