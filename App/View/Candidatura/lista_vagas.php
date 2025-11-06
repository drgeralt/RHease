<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RHease - Vagas Disponíveis</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/gestaoVagas.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        /* Estilo para mostrar texto apenas no hover */
        .btn-candidatar {
            background: none;
            border: none;
            padding: 8px 12px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #28a745;
            font-size: 1.2rem;
            transition: all 0.3s ease;
        }
        
        .btn-candidatar:hover {
            color: #1e7e34;
            background-color: rgba(40, 167, 69, 0.1);
            border-radius: 8px;
        }
        
        .btn-candidatar .btn-text {
            max-width: 0;
            opacity: 0;
            overflow: hidden;
            display: inline-block;
            transition: max-width 0.3s ease, opacity 0.3s ease, margin-left 0.3s ease;
            white-space: nowrap;
            margin-left: 0;
            font-size: 0.9rem;
        }
        
        .btn-candidatar:hover .btn-text {
            max-width: 150px;
            opacity: 1;
            margin-left: 8px;
        }
        
        /* Garantir que o formulário não quebre o layout */
        .actions form {
            margin: 0;
            padding: 0;
            display: inline;
        }
    </style>
</head>
<body>
    <header>
        <img src="<?= BASE_URL ?>/img/rhease-ease 1.png" alt="Logo RH ease" class="logo">
    </header>
    
        <div class="content">
            <h2 class="page-title-content">Vagas Disponíveis</h2>

            <section class="content-section">
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
                        <tbody>
                        <?php foreach ($vagas as $vaga):
                            // Pular vagas que não estão abertas
                            if($vaga['situacao'] !== 'aberta') continue;
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($vaga['titulo']); ?></td>
                                <td><?php echo htmlspecialchars($vaga['departamento']); ?></td>
                                <td>
                                    <span class="status-badge status-open">
                                        <?php echo htmlspecialchars(ucfirst($vaga['situacao'])); ?>
                                    </span>
                                </td>
                                <td class="actions">
                                    <form action="<?php echo BASE_URL; ?>/candidatura/formulario" method="POST">
                                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($vaga['id_vaga']); ?>">
                                        <button type="submit" class="btn-candidatar">
                                            <i class="fas fa-file-export"></i>
                                            <span class="btn-text">Candidatar-se</span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
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
</body>
</html>