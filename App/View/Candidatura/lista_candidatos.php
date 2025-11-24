<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidatos para: <?php echo htmlspecialchars($vaga['titulo_vaga']); ?></title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/vagas.css">
</head>
<body>

<header>
    <i class="menu-toggle fas fa-bars"></i> <div class="logo"><img src="<?= BASE_URL ?>/img/rhease-ease 1.png" alt="RH ease" class="logo"></div>
</header>

<div class="container">
    <div class="sidebar">
        <ul class="menu">
            <li><a href="<?= BASE_URL ?>/inicio"><i class="fas fa-chart-line"></i> Painel</a></li> <li><a href="<?= BASE_URL ?>/colaboradores"><i class="fas fa-users"></i> Colaboradores</a></li>
            <li><a href="<?= BASE_URL ?>/vagas/listar"><i class="fas fa-briefcase"></i> Gestão de Vagas</a></li>
        </ul>
    </div>

    <div class="content">
        <div class="section-header">
            <div>
                <a href="<?= BASE_URL ?>/vagas/listar" class="btn-cancelar" style="margin-bottom: 10px; display: inline-flex;">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
                <h2 class="page-title-content" style="margin-bottom: 5px;">Candidatos</h2>
                <h3 style="color: var(--text-color-light); font-weight: 500;"><?= htmlspecialchars($vaga['titulo_vaga']); ?></h3>
            </div>
        </div>

        <section class="content-section">
            <div class="tabela-container">
                <?php if (empty($candidatos)): ?>
                    <p class="empty-message">Nenhum candidato encontrado para esta vaga ainda.</p>
                <?php else: ?>
                    <table>
                        <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Data</th>
                            <th>Score IA</th>
                            <th>Status</th>
                            <th style="text-align: center;">Ações</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($candidatos as $candidato): ?>
                            <tr>
                                <td><?= htmlspecialchars($candidato['nome_completo']); ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($candidato['data_candidatura'])); ?></td>
                                <td>
                                    <?php if (!is_null($candidato['pontuacao_aderencia'])): ?>
                                        <span class="status-badge status-aberta"><?= htmlspecialchars($candidato['pontuacao_aderencia']); ?>%</span>
                                    <?php else: ?>
                                        <span style="color: #999;">N/A</span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="status-badge"><?= htmlspecialchars($candidato['status_triagem']); ?></span></td>
                                <td class="actions">
                                    <a href="<?= BASE_URL . htmlspecialchars($candidato['curriculo']); ?>" target="_blank" class="icon-btn icon-view" title="Ver Currículo">
                                        <i class="fas fa-download"></i>
                                    </a>

                                    <?php if (is_null($candidato['pontuacao_aderencia'])): ?>
                                        <form action="<?= BASE_URL ?>/candidatura/analisar" method="POST" style="display: inline;">
                                            <input type="hidden" name="id_candidatura" value="<?= $candidato['id_candidatura']; ?>">
                                            <button type="submit" class="icon-btn icon-edit" title="Processar com IA">
                                                <i class="fas fa-robot"></i>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <form action="<?= BASE_URL ?>/candidatura/ver-analise" method="POST" style="display: inline;">
                                            <input type="hidden" name="id_candidatura" value="<?= $candidato['id_candidatura']; ?>">
                                            <button type="submit" class="icon-btn icon-view" title="Ver Análise">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </section>
    </div>
</div>
</body>
</html>