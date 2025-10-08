<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabela de Colaboradores</title>

    <!-- 1. Carregando o novo ficheiro CSS unificado -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/novo_estilo_geral.css">

    <!-- 2. Carregando os ícones (link corrigido) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
        <!-- Sidebar -->
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
                        <li class="nav-item active">
                            <a href="#" class="nav-link">
                                <i class="fas fa-database"></i>
                                <span>Dados cadastrais</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <!-- Link de Recrutamento atualizado -->
                            <a href="<?php echo BASE_URL; ?>/vagas/listar" class="nav-link">
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
                            <!-- Link de Benefícios atualizado -->
                            <a href="<?php echo BASE_URL; ?>/beneficios" class="nav-link">
                                <i class="fas fa-gift"></i>
                                <span>Benefícios</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <!-- Link de Frequência atualizado -->
                            <a href="<?php echo BASE_URL; ?>/registrarponto" class="nav-link">
                                <i class="fas fa-clock"></i>
                                <span>Frequência</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </aside>
        </div>

        <!-- Conteúdo Principal com a Tabela de Colaboradores -->
        <main class="main-content">
            <div class="page-header">
                <h1 class="page-title">Lista de Colaboradores</h1>
            </div>

            <section class="content-section">
                <div class="section-header">
                    <h2 class="section-title">Colaboradores</h2>
                    <a href="<?php echo BASE_URL; ?>/colaboradores/adicionar" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Adicionar Colaborador
                    </a>
                </div>

                <div class="table-container">
                    <table class="data-table">
                        <thead>
                        <tr>
                            <th>ID <i class="fas fa-sort"></i></th>
                            <th>Nome <i class="fas fa-sort"></i></th>
                            <th>Cargo <i class="fas fa-sort"></i></th>
                            <th>Departamento <i class="fas fa-sort"></i></th>
                            <th>Data de Admissão <i class="fas fa-sort"></i></th>
                            <th>Status <i class="fas fa-sort"></i></th>
                            <th class="text-center">Ações <i class="fas fa-sort"></i></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (isset($colaboradores) && count($colaboradores) > 0): ?>
                            <?php foreach ($colaboradores as $colaborador): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($colaborador['id_colaborador']); ?></td>
                                    <td><?php echo htmlspecialchars($colaborador['nome_completo']); ?></td>
                                    <td><?php echo htmlspecialchars($colaborador['cargo'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($colaborador['departamento'] ?? 'N/A'); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($colaborador['data_admissao'])); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo $colaborador['situacao'] === 'ativo' ? 'status-open' : 'status-draft'; ?>">
                                            <?php echo ucfirst(htmlspecialchars($colaborador['situacao'])); ?>
                                        </span>
                                    </td>
                                    <td class="actions">
                                        <form action="<?php echo BASE_URL; ?>/colaboradores/editar" method="POST" style="display: inline;">
                                            <input type="hidden" name="id" value="<?php echo $colaborador['id_colaborador']; ?>">
                                            <button type="submit" class="btn btn-edit" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </form>

                                        <!-- LÓGICA DO BOTÃO DE ATIVAR/DESATIVAR -->
                                        <?php if ($colaborador['situacao'] === 'ativo'): ?>
                                            <form action="<?php echo BASE_URL; ?>/colaboradores/toggle-status" method="POST" style="display: inline;">
                                                <input type="hidden" name="id" value="<?php echo $colaborador['id_colaborador']; ?>">
                                                <button type="submit" class="btn btn-delete" title="Desativar">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <form action="<?php echo BASE_URL; ?>/colaboradores/toggle-status" method="POST" style="display: inline;">
                                                <input type="hidden" name="id" value="<?php echo $colaborador['id_colaborador']; ?>">
                                                <button type="submit" class="btn btn-info" title="Ativar">
                                                    <i class="fas fa-check-circle"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">Nenhum colaborador encontrado.</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
</div>

<script src="<?php echo BASE_URL; ?>/assets/js/script.js"></script>
</body>
</html>

