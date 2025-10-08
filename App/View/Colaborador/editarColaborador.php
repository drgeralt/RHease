<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Colaborador</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/cadastroColaboradores.css">
</head>
<body>
<header class="topbar">
    <div class="logo"><img src="<?php echo BASE_URL; ?>/img/rhease-ease 1.png" alt="Logo RH Ease" class="logo"></div>
</header>

<main class="form-container">
    <h2 style="color: #25621C; font-weight: 600;">Editar Colaborador</h2>

    <!-- O formulário agora aponta para uma rota de atualização -->
    <form action="<?php echo BASE_URL; ?>/colaboradores/atualizar" method="POST">

        <!-- Campo oculto para enviar o ID do colaborador -->
        <input type="hidden" name="id_colaborador" value="<?php echo htmlspecialchars($colaborador['id_colaborador'] ?? ''); ?>">

        <!-- Dados Pessoais -->
        <section>
            <h3>1. Dados Pessoais</h3>
            <div class="grid">
                <div><label>Nome completo</label><input type="text" name="nome" placeholder="Ex.: Maria Oliveira da Silva" value="<?php echo htmlspecialchars($colaborador['nome_completo'] ?? ''); ?>" required></div>
                <div><label>CPF</label><input type="text" name="cpf" placeholder="999.999.999-99" value="<?php echo htmlspecialchars($colaborador['CPF'] ?? ''); ?>" readonly></div>
                <div><label>RG</label><input type="text" name="rg" value="<?php echo htmlspecialchars($colaborador['RG'] ?? ''); ?>"></div>
                <div><label>Data de nascimento</label><input type="date" name="data_nascimento" value="<?php echo htmlspecialchars($colaborador['data_nascimento'] ?? ''); ?>" required></div>
                <div>
                    <label>Gênero</label>
                    <select name="genero">
                        <option value="">Selecione</option>
                        <option value="Feminino" <?php echo ($colaborador['genero'] ?? '') === 'Feminino' ? 'selected' : ''; ?>>Feminino</option>
                        <option value="Masculino" <?php echo ($colaborador['genero'] ?? '') === 'Masculino' ? 'selected' : ''; ?>>Masculino</option>
                        <option value="Outro" <?php echo ($colaborador['genero'] ?? '') === 'Outro' ? 'selected' : ''; ?>>Outro</option>
                        <option value="Prefiro não informar" <?php echo ($colaborador['genero'] ?? '') === 'Prefiro não informar' ? 'selected' : ''; ?>>Prefiro não informar</option>
                    </select>
                </div>
                <div><label>E-mail</label><input type="email" name="email" placeholder="exemplo@gmail.com" value="<?php echo htmlspecialchars($colaborador['email'] ?? ''); ?>" required></div>
                <div><label>Telefone celular</label><input type="text" name="telefone" placeholder="(99) 99999-9999" value="<?php echo htmlspecialchars($colaborador['telefone'] ?? ''); ?>" oninput="mascaraTelefone(this)"></div>
            </div>
        </section>

        <!-- Endereço -->
        <section>
            <h3>2. Endereço</h3>
            <div class="grid">
                <div><label>CEP</label><input type="text" name="CEP" placeholder="99999-999" value="<?php echo htmlspecialchars($endereco['CEP'] ?? ''); ?>" oninput="mascaraCEP(this)" onblur="buscaCEP(this.value)"></div>
                <div class="col-2"><label>Logradouro</label><input type="text" name="logradouro" placeholder="Rua, Avenida, Travessa etc." value="<?php echo htmlspecialchars($endereco['logradouro'] ?? ''); ?>"></div>
                <div><label>Número</label><input type="text" name="numero" placeholder="Ex.: 123" value="<?php echo htmlspecialchars($endereco['numero'] ?? ''); ?>"></div>
                <div><label>Complemento</label><input type="text" name="complemento" placeholder="Ex.: Apt 202, Bloco B" value="<?php echo htmlspecialchars($endereco['complemento'] ?? ''); ?>"></div>
                <div><label>Bairro</label><input type="text" name="bairro" placeholder="Ex.: Centro" value="<?php echo htmlspecialchars($endereco['bairro'] ?? ''); ?>"></div>
                <div><label>Cidade</label><input type="text" name="cidade" placeholder="Ex.: Palmas" value="<?php echo htmlspecialchars($endereco['cidade'] ?? ''); ?>"></div>
                <div>
                    <label>Estado (UF)</label>
                    <select name="estado">
                        <option value="">Selecione</option>
                        <?php $estados = ["AC", "AL", "AP", "AM", "BA", "CE", "DF", "ES", "GO", "MA", "MT", "MS", "MG", "PA", "PB", "PR", "PE", "PI", "RJ", "RN", "RS", "RO", "RR", "SC", "SP", "SE", "TO"]; ?>
                        <?php foreach ($estados as $estado): ?>
                            <option value="<?php echo $estado; ?>" <?php echo ($endereco['estado'] ?? '') === $estado ? 'selected' : ''; ?>><?php echo $estado; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </section>

        <!-- Dados Profissionais -->
        <section>
            <h3>3. Dados Profissionais</h3>
            <div class="grid">
                <div><label>ID (Matrícula)</label><input type="text" name="matricula" placeholder="99999-999" value="<?php echo htmlspecialchars($colaborador['matricula'] ?? ''); ?>" readonly></div>
                <div><label>Cargo</label><input type="text" name="cargo" placeholder="Ex.: Analista de Sistemas" value="<?php echo htmlspecialchars($cargo['nome_cargo'] ?? ''); ?>" required></div>
                <div><label>Departamento</label><input type="text" name="departamento" placeholder="Ex.: TI, Financeiro" value="<?php echo htmlspecialchars($setor['nome_setor'] ?? ''); ?>" required></div>
                <div><label>Salário (R$)</label><input type="text" name="salario" placeholder="Ex.: 3.500,75" value="<?php echo htmlspecialchars($cargo['salario_base'] ?? ''); ?>" required></div>
                <div><label>Data de Admissão</label><input type="date" name="data_admissao" value="<?php echo htmlspecialchars($colaborador['data_admissao'] ?? ''); ?>" required></div>
                <div>
                    <label>Situação</label>
                    <select name="situacao" required>
                        <option value="">Selecione</option>
                        <option value="ativo" <?php echo ($colaborador['situacao'] ?? '') === 'ativo' ? 'selected' : ''; ?>>Ativo</option>
                        <option value="inativo" <?php echo ($colaborador['situacao'] ?? '') === 'inativo' ? 'selected' : ''; ?>>Inativo</option>
                        <option value="ferias" <?php echo ($colaborador['situacao'] ?? '') === 'ferias' ? 'selected' : ''; ?>>Férias</option>
                        <option value="licenca" <?php echo ($colaborador['situacao'] ?? '') === 'licenca' ? 'selected' : ''; ?>>Licença</option>
                    </select>
                </div>
                <div><label>E-mail Corporativo</label><input type="email" name="email_corporativo" placeholder="usuario@empresa.com" value="<?php echo htmlspecialchars($colaborador['email_corporativo'] ?? ''); ?>"></div>
            </div>
        </section>

        <div class="btn-area">
            <button type="submit">Atualizar Cadastro</button>
        </div>
    </form>
</main>
<script>
    // Máscara CPF
    function mascaraCPF(i){
        let v = i.value.replace(/\D/g,'');
        v = v.replace(/(\d{3})(\d)/,'$1.$2');
        v = v.replace(/(\d{3})(\d)/,'$1.$2');
        v = v.replace(/(\d{3})(\d{1,2})$/,'$1-$2');
        i.value = v;
    }

    // Máscara Telefone
    function mascaraTelefone(i){
        let v = i.value.replace(/\D/g,'');
        if(v.length <= 10){
            v = v.replace(/(\d{2})(\d)/,'($1) $2');
            v = v.replace(/(\d{4})(\d)/,'$1-$2');
        } else {
            v = v.replace(/(\d{2})(\d)/,'($1) $2');
            v = v.replace(/(\d{5})(\d)/,'$1-$2');
        }
        i.value = v;
    }

    // Máscara CEP
    function mascaraCEP(i){
        let v = i.value.replace(/\D/g,'');
        v = v.replace(/(\d{5})(\d)/,'$1-$2');
        i.value = v;
    }

    // Preenchimento automático de endereço via CEP
    async function buscaCEP(cep){
        cep = cep.replace(/\D/g,'');
        if(cep.length === 8){
            try{
                const res = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
                const data = await res.json();
                if(!data.erro){
                    document.querySelector('input[name="logradouro"]').value = data.logradouro;
                    document.querySelector('input[name="bairro"]').value = data.bairro;
                    document.querySelector('input[name="cidade"]').value = data.localidade;
                    document.querySelector('select[name="estado"]').value = data.uf;
                } else {
                    alert("CEP não encontrado.");
                }
            } catch(e){
                alert("Erro ao buscar CEP.");
            }
        }
    }
</script>
</body>
</html>