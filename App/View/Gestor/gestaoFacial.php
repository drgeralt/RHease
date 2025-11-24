<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Biometria</title>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="<?= BASE_URL ?>/css/beneficiostyle.css">
    <style>
        /* Estilos específicos para tabela limpa */
        .table-container { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .badge-success { background-color: #d4edda; color: #155724; padding: 5px 10px; border-radius: 15px; font-size: 0.85em; font-weight: 600; }
        .badge-warning { background-color: #fff3cd; color: #856404; padding: 5px 10px; border-radius: 15px; font-size: 0.85em; font-weight: 600; }
        .btn-reset { border: 1px solid #dc3545; color: #dc3545; background: transparent; padding: 5px 10px; border-radius: 5px; cursor: pointer; transition: 0.3s; }
        .btn-reset:hover { background: #dc3545; color: white; }
    </style>
</head>
<body>

<header>
    <i class="bi bi-list menu-toggle"></i>
    <img id="logo" src="<?= BASE_URL ?>/img/rhease-ease 1.png" alt="RHease" width="130">
</header>

<div class="container">

    <div class="sidebar">
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
        <div class="header-tabela" style="margin-bottom: 20px;">
            <h2>Gestão de Biometria Facial</h2>
            <p>Gerencie quais colaboradores possuem face cadastrada para o registro de ponto.</p>
        </div>

        <main class="main-content">
            <div class="table-container">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                    <tr style="text-align: left; border-bottom: 2px solid #eee;">
                        <th style="padding: 15px;">Colaborador</th>
                        <th style="padding: 15px;">Matrícula</th>
                        <th style="padding: 15px;">Status</th>
                        <th style="padding: 15px;">Ação</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($colaboradores)): ?>
                        <?php foreach ($colaboradores as $c): ?>
                            <tr style="border-bottom: 1px solid #f0f0f0;">
                                <td style="padding: 15px; font-weight: 500;"><?= htmlspecialchars($c['nome_completo']) ?></td>
                                <td style="padding: 15px; color: #666;"><?= htmlspecialchars($c['matricula']) ?></td>
                                <td style="padding: 15px;">
                                    <?php if ($c['face_registered_at']): ?>
                                        <span class="badge-success">Cadastrada</span>
                                    <?php else: ?>
                                        <span class="badge-warning">Pendente</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 15px;">
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
                            <td colspan="4" style="padding: 20px; text-align: center;">Nenhum colaborador encontrado.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="<?= BASE_URL ?>/js/sidebar-toggle.js"></script>

<script>
    const BASE_URL = "<?= BASE_URL ?>";

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