document.addEventListener('DOMContentLoaded', function() {

    const modalColaboradorElement = document.getElementById('modalColaborador');
    const modalColaborador = new bootstrap.Modal(modalColaboradorElement);

    const modalStatusElement = document.getElementById('modalStatus');
    const modalStatus = new bootstrap.Modal(modalStatusElement);

    let linhaAtual = null;

    // --- FUNÇÃO SEGURA PARA PREENCHER CAMPOS ---
    // Evita o erro "Cannot set properties of null" se o ID não existir
    function setVal(id, valor) {
        const elemento = document.getElementById(id);
        if (elemento) {
            elemento.value = (valor === null || valor === undefined) ? '' : valor;
        } else {
            console.warn(`Atenção: O campo com id '${id}' não foi encontrado no HTML.`);
        }
    }

    // --- BARRA DE PESQUISA ---
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const termo = this.value.toLowerCase();
            const linhas = document.querySelectorAll('#tabelaColaboradores tbody tr');

            linhas.forEach(linha => {
                const textoLinha = linha.textContent.toLowerCase();
                linha.style.display = textoLinha.includes(termo) ? '' : 'none';
            });
        });
    }

    // --- ABRIR MODAL CRIAR ---
    window.abrirModalCriar = function() {
        const form = document.getElementById('formColaborador');
        form.reset();
        setVal('id_colaborador', '0');

        form.action = `${BASE_URL}/colaboradores/criar`;

        const titulo = document.getElementById('modalTitle');
        if(titulo) titulo.innerText = "Adicionar Novo Colaborador";

        const btn = document.getElementById('btnSalvar');
        if(btn) btn.innerText = "Cadastrar";

        modalColaborador.show();
    };

    // --- ABRIR MODAL EDITAR ---
    window.abrirModalEditar = async function(id) {
        const form = document.getElementById('formColaborador');
        form.reset();

        const titulo = document.getElementById('modalTitle');
        if(titulo) titulo.innerText = "Carregando...";

        modalColaborador.show();

        try {
            const response = await fetch(`${BASE_URL}/colaboradores/buscarDados?id=${id}`);

            if (!response.ok) throw new Error(`Erro HTTP: ${response.status}`);

            const dados = await response.json();

            if (dados.erro) {
                alert(dados.msg);
                modalColaborador.hide();
                return;
            }

            // Mapeamento Seguro (Backend -> Frontend)
            const c = dados.colaborador;
            const e = dados.endereco;
            const cg = dados.cargo;
            const s = dados.setor;

            // IDs Críticos
            setVal('id_colaborador', c.id_colaborador);

            // Dados Pessoais
            setVal('nome_completo', c.nome_completo);
            setVal('cpf', c.CPF || c.cpf);
            setVal('rg', c.RG || c.rg);
            setVal('data_nascimento', c.data_nascimento);
            setVal('genero', c.genero);
            setVal('email_pessoal', c.email || c.email_pessoal);
            setVal('telefone', c.telefone);

            // Endereço
            if (e) {
                setVal('CEP', e.CEP);
                setVal('logradouro', e.logradouro);
                setVal('numero', e.numero);
                setVal('bairro', e.bairro);
                setVal('cidade', e.cidade);
                setVal('estado', e.estado);
            }

            // Profissional
            setVal('matricula', c.matricula);
            setVal('data_admissao', c.data_admissao);
            setVal('situacao', c.situacao);
            setVal('tipo_contrato', c.tipo_contrato);

            // Atenção: Verifique se o backend manda 'email_profissional'
            setVal('email_corporativo', c.email_profissional || '');

            if (cg) {
                setVal('cargo', cg.nome_cargo);
                setVal('salario', cg.salario_base);
            }

            if (s) {
                // Tenta preencher 'departamento' (nome mais comum no front) ou 'setor'
                setVal('departamento', s.nome_setor);
                setVal('setor', s.nome_setor);
            }

            // Atualiza UI
            form.action = `${BASE_URL}/colaboradores/atualizar`;
            if(titulo) titulo.innerText = "Editar Colaborador";
            const btn = document.getElementById('btnSalvar');
            if(btn) btn.innerText = "Salvar Alterações";

        } catch (error) {
            console.error("Erro detalhado:", error);
            alert("Erro ao carregar dados. Verifique o console.");
            modalColaborador.hide();
        }
    };

    // --- ABRIR MODAL STATUS (PREPARAR O DELETE) ---
    window.abrirModalStatus = function(id, nomeAtual, statusAtual, btnElement) {
        if (btnElement) {
            linhaAtual = btnElement.closest('tr');
        } else {
            linhaAtual = null;
        }

        setVal('id_status', id);

        const spanNome = document.getElementById('nome_colaborador_status');
        const spanAcao = document.getElementById('acao_status');

        if(spanNome) spanNome.innerText = nomeAtual;

        const statusLimpo = (statusAtual || '').toLowerCase();

        if(spanAcao) {
            if(statusLimpo === 'ativo') {
                spanAcao.innerText = "desativar";
                spanAcao.className = "text-danger fw-bold";
            } else {
                spanAcao.innerText = "ativar";
                spanAcao.className = "text-success fw-bold";
            }
        }

        modalStatus.show();
    };

    // --- CONFIRMAR MUDANÇA DE STATUS (AJAX) ---
    const formStatus = document.getElementById('formStatus');
    if(formStatus){
        formStatus.addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(formStatus);

            try {
                const response = await fetch(`${BASE_URL}/colaboradores/toggleStatus`, {
                    method: 'POST',
                    body: formData
                });

                const json = await response.json();

                if (json.erro) {
                    alert(json.msg);
                } else {
                    atualizarLinhaTabela(linhaAtual);
                    modalStatus.hide();
                }

            } catch (error) {
                console.error(error);
                alert("Erro ao comunicar com o servidor.");
            }
        });
    }

    function atualizarLinhaTabela(tr) {
        if (!tr) return;
        const badge = tr.querySelector('.badge');
        const btnToggle = tr.querySelector('i.bi-arrow-repeat'); // Busca o ícone

        if (badge) {
            if (badge.classList.contains('bg-success')) {
                badge.className = 'badge bg-secondary';
                badge.innerText = 'Inativo';

                if(btnToggle) {
                    let parentBtn = btnToggle.parentElement; // O onclick pode estar no pai ou no i
                    // Tenta atualizar o onclick (apenas visual, ideal é reload ou manipulação DOM melhor)
                    // Simplificação: A cor do badge já mudou, o usuário sabe que mudou.
                }
            } else {
                badge.className = 'badge bg-success';
                badge.innerText = 'Ativo';
            }
        }
    }

    // --- MÁSCARAS ---
    window.mascaraCPF = function(i) { i.value = i.value.replace(/\D/g,'').replace(/(\d{3})(\d{3})(\d{3})(\d{2})/,"$1.$2.$3-$4"); };
    window.mascaraTelefone = function(i) {
        let v = i.value.replace(/\D/g,'');
        i.value = (v.length > 10) ? v.replace(/^(\d{2})(\d{5})(\d{4}).*/,"($1) $2-$3") : v.replace(/^(\d{2})(\d{4})(\d{4}).*/,"($1) $2-$3");
    };
    window.mascaraCEP = function(i) { i.value = i.value.replace(/\D/g,'').replace(/^(\d{5})(\d)/,"$1-$2"); };

    window.buscaCEP = async function(cep) {
        cep = cep.replace(/\D/g,'');
        if(cep.length === 8){
            try{
                const res = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
                const data = await res.json();
                if(!data.erro){
                    setVal('logradouro', data.logradouro);
                    setVal('bairro', data.bairro);
                    setVal('cidade', data.localidade);
                    setVal('estado', data.uf);
                }
            } catch(e){}
        }
    };
});