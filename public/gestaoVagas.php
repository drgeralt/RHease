<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Gestão de Vagas</title>
    <link rel="stylesheet" href="../public/css/gestaoVagas.css">
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
                    <img src="../public/img/rhease-ease 1.png" alt="RH ease" class="logo-img">
                </div>
            </div>
           <div class="header-right">
                <div class="user-info">
                    <img src="../public/img/user.png" alt=" usuario">
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
                        <a href = "../public/novaVaga.php"class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Criar Nova Vaga
                        </a>
                    </div>
                    
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Título <i class="fas fa-sort"></i></th>
                                    <th>Departamento <i class="fas fa-sort"></i></th>
                                    <th>Status <i class="fas fa-sort"></i></th>
                                    <th>Ações <i class="fas fa-sort"></i></th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- <?php foreach ($jobs as $job): ?> 
                                <tr>
                                    <td><?php echo htmlspecialchars($job['titulo']); ?></td>
                                    <td><?php echo htmlspecialchars($job['departamento']); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo strtolower($job['status']); ?>">
                                            <?php echo $job['status']; ?>
                                        </span>
                                    </td>
                                    <td class="actions">
                                        <button class="btn-action btn-edit" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn-action btn-delete" title="Excluir">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <button class="btn-action btn-view" title="Visualizar">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?> -->
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
                                <!-- <?php foreach ($rules as $rule): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($rule['tipo_contrato']); ?></td>
                                    <td><?php echo htmlspecialchars($rule['beneficios_padrao']); ?></td>
                                    <td class="actions">
                                        <button class="btn-action btn-edit" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?> -->
                            </tbody>
                        </table>
                    </div>
                </section>
            </main>
        </div>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>
