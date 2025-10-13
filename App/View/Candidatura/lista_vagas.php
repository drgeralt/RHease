<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Vagas</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/gestaoVagas.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Adicione este estilo para garantir que o botão do formulário se pareça com os outros */
        .actions form {
            margin: 0;
            padding: 0;
            display: inline;
        }
        .actions .btn {
            cursor: pointer;
            border: none;
            font-family: inherit;
            font-size: inherit;
        }
    </style>
</head>
<body>
<div class="app-container">
    <header class="header">
        <div class="header-left">
            <div class="logo">
                <img src="<?php echo BASE_URL; ?>/img/rhease-ease 1.png" alt="RH ease" class="logo-img">
            </div>
        </div>
    </header>

    <div class="main-container">
        <main class="main-content">
            <div class="page-header">
                <h1 class="page-title">Vagas Disponíveis</h1>
            </div>
            <section class="content-section">
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                        <tr>
                            <th>Título <i class="fas fa-sort"></i></th>
                            <th>Departamento <i class="fas fa-sort"></i></th>
                            <th>Status <i class="fas fa-sort"></i></th>
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
                                        <button type="submit" class="btn btn-edit">
                                            <i class="fas fa-edit"></i> Candidatar-se
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
</div>

</body>
</html>