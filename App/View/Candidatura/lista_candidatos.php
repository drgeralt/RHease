<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidatos para: <?php echo htmlspecialchars($vaga['titulo_vaga']); ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/gestaoVagas.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
                <a href="<?php echo BASE_URL; ?>/vagas/listar" class="btn btn-secondary" style="margin-bottom: 20px; display: inline-block;">
                    <i class="fas fa-arrow-left"></i> Voltar para Gestão de Vagas
                </a>
                <h1 class="page-title">Candidatos para a Vaga</h1>
                <h2 style="font-weight: 500; color: #333;"><?php echo htmlspecialchars($vaga['titulo_vaga']); ?></h2>
            </div>

            <section class="content-section">
                <div class="table-container">
                    <?php if (empty($candidatos)): ?>
                        <p>Nenhum candidato encontrado para esta vaga ainda.</p>
                    <?php else: ?>
                        <table class="data-table">
                            <thead>
                            <tr>
                                <th>Nome do Candidato</th>
                                <th>Data da Candidatura</th>
                                <th>Score IA</th> <!-- NOVA COLUNA -->
                                <th>Status</th>
                                <th>Ações</th> <!-- COLUNA ATUALIZADA -->
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($candidatos as $candidato): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($candidato['nome_completo']); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($candidato['data_candidatura'])); ?></td>
                                    <td>
                                        <!-- Exibe a pontuação ou 'N/A' se não houver -->
                                        <?php if (!is_null($candidato['pontuacao_aderencia'])): ?>
                                            <span class="status-badge status-open"><?php echo htmlspecialchars($candidato['pontuacao_aderencia']); ?>%</span>
                                        <?php else: ?>
                                            <span>N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="status-badge">
                                            <?php echo htmlspecialchars($candidato['status_triagem']); ?>
                                        </span>
                                    </td>
                                    <td class="actions">
                                        <!-- Link para ver o currículo -->
                                        <a href="<?php echo BASE_URL . htmlspecialchars($candidato['curriculo']); ?>" target="_blank" class="btn btn-primary" title="Ver Currículo">
                                            <i class="fas fa-download"></i>
                                        </a>

                                        <!-- LÓGICA DO BOTÃO DE IA -->
                                        <?php if (is_null($candidato['pontuacao_aderencia'])): ?>
                                            <!-- Se NÃO HOUVER score, mostra o botão para processar -->
                                            <form action="<?php echo BASE_URL; ?>/candidatura/analisar" method="POST" style="display: inline;">
                                                <input type="hidden" name="id_candidatura" value="<?php echo $candidato['id_candidatura']; ?>">
                                                <button type="submit" class="btn btn-secondary" title="Processar com IA">
                                                    <i class="fas fa-robot"></i>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <!-- Se JÁ HOUVER score, mostra o botão para ver a análise -->
                                            <form action="<?php echo BASE_URL; ?>/candidatura/ver-analise" method="POST" style="display: inline;">
                                                <input type="hidden" name="id_candidatura" value="<?php echo $candidato['id_candidatura']; ?>">
                                                <button type="submit" class="btn btn-info" title="Ver Análise da IA">
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
        </main>
    </div>
</div>
</body>
</html>