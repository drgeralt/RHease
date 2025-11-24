<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>RHease - Vagas Disponíveis</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/vagas.css">
</head>
<body>
<header>
    <div class="logo"><img src="<?= BASE_URL ?>/img/rhease-ease 1.png" alt="Logo RH ease" class="logo"></div>
</header>

<div class="container" style="justify-content: center;">
    <div class="content" style="max-width: 1200px;">
        <h2 class="page-title-content">Vagas Disponíveis</h2>

        <section class="content-section">
            <div class="tabela-container">
                <table>
                    <thead>
                    <tr>
                        <th>Título</th>
                        <th>Departamento</th>
                        <th>Status</th>
                        <th style="text-align: center;">Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($vagas as $vaga):
                        if($vaga['situacao'] !== 'aberta') continue;
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($vaga['titulo']); ?></td>
                            <td><?= htmlspecialchars($vaga['departamento']); ?></td>
                            <td><span class="status-badge status-aberta">Aberta</span></td>
                            <td class="actions">
                                <form action="<?= BASE_URL ?>/candidatura/formulario" method="POST">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($vaga['id_vaga']); ?>">
                                    <button type="submit" class="btn-action">
                                        <i class="fas fa-file-export"></i> Candidatar-se
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
</body>
</html>