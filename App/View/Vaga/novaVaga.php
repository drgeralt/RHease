<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Nova Vaga</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/novaVaga.css">
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
                    <img src="../public/img/user.png" alt=" usuario">
                    <span class="user-name">Vitoria Leal</span>
                </div>
            </div>
        </header>

        <main class="main-container">
            <h1>Criar Nova Vaga</h1>

            <form action="#" method="POST">
                
                <section>
                    <h3>1. Dados da Vaga</h3>
                    <div class="grid">
                        <div>
                            <label for="titulo">Título da Vaga</label>
                            <input id="titulo" type="text" name="titulo" placeholder="Ex.: Analista de Marketing" required>
                        </div>
                        <div>
                            <label for="departamento">Departamento</label>
                            <input id="departamento" type="text" name="departamento" placeholder="Ex.: Marketing" required>
                        </div>
                        <div>
                            <label for="localizacao">Localização</label>
                            <input id="localizacao" type="text" name="localizacao" placeholder="Ex.: São Paulo, SP" required>
                        </div>
                        <div>
                            <label for="tipo_contrato">Tipo de Contrato</label>
                            <select id="tipo_contrato" name="tipo_contrato" required>
                                <option value="" disabled selected>Selecione</option>
                                <option value="CLT">CLT</option>
                                <option value="PJ">PJ</option>
                                <option value="Estágio">Estágio</option>
                                <option value="Temporário">Temporário</option>
                            </select>
                        </div>
                        <div>
                            <label for="salario">Salário (Opcional)</label>
                            <input id="salario" type="text" name="salario" placeholder="Ex.: R$ 3.000,00">
                        </div>
                        <div>
                            <label for="status">Status da Vaga</label>
                            <select id="status" name="status" class="status-select" required>
                                <option value="aberta">Aberta</option>
                                <option value="rascunho">Rascunho</option>
                                <option value="fechada">Fechada</option>
                            </select>
                        </div>
                        <div style="grid-column: span 2;">
                            <label for="descricao">Descrição da Vaga</label>
                            <textarea id="descricao" name="descricao" placeholder="Descreva as responsabilidades e requisitos da vaga..." required></textarea>
                        </div>
                    </div>
                </section>

                <section>
                    <h3>2. Requisitos e Skills</h3>
                    <div class="grid">
                        <div>
                            <div class="skills-container">
                                <label for="skills_necessarias">Skills Necessárias</label>
                                <input id="skills_necessarias" type="text" name="skills_necessarias" placeholder="Ex.: Trabalho em equipe, Comunicação">
                            </div>
                        </div>
                        <div>
                            <div class="skills-container">
                                <label for="skills_recomendadas">Skills Recomendadas</label>
                                <input id="skills_recomendadas" type="text" name="skills_recomendadas" placeholder="Ex.: Criatividade, Proatividade">
                            </div>
                        </div>
                       <div>
                            <div class="skills-container">
                                <label for="skills_desejadas">Skills Desejadas (Opcional)</label>
                                <input id="skills_desejadas" type="text" name="skills_desejadas" placeholder="Ex.: Trabalho em equipe, Comunicação">
                            </div>
                        </div>
                    </div>
                </section>

                <button type="submit" class="submit-button">Salvar Vaga</button>

            </form>
        </main>
    </div>
</body>
</html>