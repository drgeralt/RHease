<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Vaga</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/editarVaga.css">
</head>
<body>
    <div class="app-container">
        <main class="main-container">

            <div class="header-actions">
                <h1>Editar Vaga</h1>
                <a href="<?php echo BASE_URL; ?>/vagas/listar" class="btn-voltar">
                    <i class="fas fa-arrow-left"></i> Voltar para a Lista
                </a>
            </div>
            
            <p style="margin-top: -15px; margin-bottom: 25px; color: #555;">Alterando a vaga: <strong><?php echo htmlspecialchars($vaga['titulo_vaga']); ?></strong></p>


            <form action="<?php echo BASE_URL; ?>/vagas/atualizar" method="POST">
                <input type="hidden" name="id_vaga" value="<?php echo $vaga['id_vaga']; ?>">

                <section>
                    <h3>Dados da Vaga</h3>
                    <div class="grid">
                        <div>
                            <label for="titulo">Título da Vaga</label>
                            <input id="titulo" type="text" name="titulo" value="<?php echo htmlspecialchars($vaga['titulo_vaga']); ?>" required>
                        </div>
                        <div>
                            <label for="departamento">Departamento</label>
                            <input id="departamento" type="text" name="departamento" value="<?php echo htmlspecialchars($vaga['nome_setor']); ?>" required>
                        </div>
                        <div style="grid-column: span 2;">
                            <label for="descricao">Descrição da Vaga</label>
                            <textarea id="descricao" name="descricao" rows="5" required><?php echo htmlspecialchars($vaga['descricao_vaga']); ?></textarea>
                        </div>
                        <div>
                            <label for="status">Status da Vaga</label>
                            <select id="status" name="status" required>
                                <option value="aberta" <?php echo ($vaga['situacao'] === 'aberta') ? 'selected' : ''; ?>>Aberta</option>
                                <option value="rascunho" <?php echo ($vaga['situacao'] === 'rascunho') ? 'selected' : ''; ?>>Rascunho</option>
                                <option value="fechada" <?php echo ($vaga['situacao'] === 'fechada') ? 'selected' : ''; ?>>Fechada</option>
                            </select>
                        </div>
                    </div>
                </section>
                
                <section>
                    <h3>Requisitos e Skills</h3>
                    <div class="grid">
                        <div style="grid-column: span 2;">
                            <label for="skills_necessarias">Skills Necessárias</label>
                            <textarea id="skills_necessarias" name="skills_necessarias" rows="3" placeholder="Ex.: Trabalho em equipe, Comunicação"><?php echo htmlspecialchars($vaga['requisitos_necessarios']); ?></textarea>
                        </div>
                        <div style="grid-column: span 2;">
                            <label for="skills_recomendadas">Skills Recomendadas</label>
                            <textarea id="skills_recomendadas" name="skills_recomendadas" rows="3" placeholder="Ex.: Criatividade, Proatividade"><?php echo htmlspecialchars($vaga['requisitos_recomendados']); ?></textarea>
                        </div>
                       <div style="grid-column: span 2;">
                            <label for="skills_desejadas">Skills Desejadas (Opcional)</label>
                            <textarea id="skills_desejadas" name="skills_desejadas" rows="3" placeholder="Ex.: Liderança, Gestão de projetos"><?php echo htmlspecialchars($vaga['requisitos_desejados']); ?></textarea>
                        </div>
                    </div>
                </section>

                <button type="submit" class="submit-button">Salvar Alterações</button>
                <a href="<?php echo BASE_URL; ?>/vagas/listar" class="cancel-button">Cancelar</a>
            </form>
        </main>
    </div>
</body>
</html>