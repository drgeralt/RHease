<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Vaga</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/novaVaga.css"> <style>
        #api-message {
            margin: 15px 0;
            padding: 15px;
            border-radius: 8px;
            font-weight: 500;
            display: none; /* Começa escondido */
        }
        .msg-sucesso {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .msg-erro {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
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

            <div id="api-message"></div>

            <form id="form-editar-vaga">
                <input type="hidden" id="id_vaga" name="id_vaga">

                <section>
                    <h3>Dados da Vaga</h3>
                    <div class="grid">
                        <div>
                            <label for="titulo">Título da Vaga</label>
                            <input id="titulo" type="text" name="titulo" required>
                        </div>
                        <div>
                            <label for="departamento">Departamento</label>
                            <input id="departamento" type="text" name="departamento" required>
                        </div>
                        <div style="grid-column: span 2;">
                            <label for="descricao">Descrição da Vaga</label>
                            <textarea id="descricao" name="descricao" rows="5" required></textarea>
                        </div>
                        <div>
                            <label for="status">Status da Vaga</label>
                            <select id="status" name="status" required>
                                <option value="aberta">Aberta</option>
                                <option value="rascunho">Rascunho</option>
                                <option value="fechada">Fechada</option>
                            </select>
                        </div>
                    </div>
                </section>
                
                <section>
                    <h3>Requisitos e Skills</h3>
                    <div class="grid">
                        <div style="grid-column: span 2;">
                            <label for="skills_necessarias">Skills Necessárias</label>
                            <textarea id="skills_necessarias" name="skills_necessarias" rows="3"></textarea>
                        </div>
                        <div style="grid-column: span 2;">
                            <label for="skills_recomendadas">Skills Recomendadas</label>
                            <textarea id="skills_recomendadas" name="skills_recomendadas" rows="3"></textarea>
                        </div>
                       <div style="grid-column: span 2;">
                            <label for="skills_desejadas">Skills Desejadas (Opcional)</label>
                            <textarea id="skills_desejadas" name="skills_desejadas" rows="3"></textarea>
                        </div>
                    </div>
                </section>

                <button type="submit" id="btn-salvar-vaga" class="submit-button">Salvar Alterações</button>
            </form>
        </main>
    </div>

<script>
    const BASE_URL = "<?php echo BASE_URL; ?>";
</script>
<script src="<?php echo BASE_URL; ?>/js/vagas-editar.js"></script>

</body>
</html>