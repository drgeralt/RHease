document.addEventListener('DOMContentLoaded', function () {

    // --- Carregamento Inicial ---
    carregarVagas();

    // --- Elementos do Modal de Candidatos ---
    const modalCandidatos = document.getElementById('modal-candidatos');
    const modalCloseBtn = document.getElementById('modal-close-btn');
    if (modalCloseBtn) {
        modalCloseBtn.addEventListener('click', fecharModalCandidatos);
    }

    // --- Elementos do Modal de Edição ---
    const modalEdicao = document.getElementById('modal-edicao-vaga');
    const modalEdicaoCloseBtn = document.getElementById('modal-edicao-close-btn');
    const modalEdicaoCancelBtn = document.getElementById('modal-edicao-cancel-btn');
    const formEditar = document.getElementById('form-editar-vaga');
    if (modalEdicaoCloseBtn) {
        modalEdicaoCloseBtn.addEventListener('click', fecharModalEdicao);
    }
    if (modalEdicaoCancelBtn) {
        modalEdicaoCancelBtn.addEventListener('click', fecharModalEdicao);
    }
    if (formEditar) {
        formEditar.addEventListener('submit', function (event) {
            event.preventDefault();
            salvarEdicao();
        });
    }

    // --- Elementos do Modal de Criação (NOVO) ---
    const modalCriacao = document.getElementById('modal-criacao-vaga');
    const btnAbrirModalCriacao = document.getElementById('btn-abrir-modal-criacao');
    const modalCriacaoCloseBtn = document.getElementById('modal-criacao-close-btn');
    const modalCriacaoCancelBtn = document.getElementById('modal-criacao-cancel-btn');
    const formCriar = document.getElementById('form-criar-vaga');
    if (btnAbrirModalCriacao) {
        btnAbrirModalCriacao.addEventListener('click', abrirModalCriacao);
    }
    if (modalCriacaoCloseBtn) {
        modalCriacaoCloseBtn.addEventListener('click', fecharModalCriacao);
    }
    if (modalCriacaoCancelBtn) {
        modalCriacaoCancelBtn.addEventListener('click', fecharModalCriacao);
    }
    if (formCriar) {
        formCriar.addEventListener('submit', function (event) {
            event.preventDefault();
            salvarCriacao();
        });
    }

    // --- Evento do Backdrop (Fundo) ---
    const modalBackdrop = document.getElementById('modal-backdrop');
    if (modalBackdrop) {
        modalBackdrop.addEventListener('click', () => {
            fecharModalCandidatos();
            fecharModalEdicao();
            fecharModalCriacao(); // Adiciona o fechamento do modal de criação
        });
    }
});

async function carregarVagas() {
    const tbody = document.getElementById('tabela-vagas-corpo');
    if (!tbody) { console.error("Erro: 'tabela-vagas-corpo' não encontrado."); return; }
    tbody.innerHTML = '<tr><td colspan="4" style="text-align: center;">Carregando...</td></tr>';
    try {
        const response = await fetch(`${BASE_URL}/api/vagas/listar`);
        
        if (!response.ok) {
            throw new Error(`Erro HTTP: ${response.status} ${response.statusText}`);
        }
        
        const text = await response.text();
        console.log("Resposta do servidor:", text);
        
        if (!text || text.trim() === '') {
            throw new Error('Resposta vazia do servidor');
        }
        
        const resultado = JSON.parse(text);
        
        // Verifica se houve erro (formato padronizado)
        if (resultado.success === false) {
            console.error("Erro do servidor:", resultado);
            const errorMsg = resultado.mensagem || 'Erro desconhecido';
            const errorDetails = resultado.data?.detalhes || '';
            throw new Error(errorDetails || errorMsg);
        }
        
        // Pega o array de vagas do campo 'data'
        const vagas = resultado.data || [];
        console.log("Vagas carregadas:", vagas);
        tbody.innerHTML = '';
        
        if (!Array.isArray(vagas)) {
            throw new Error('Resposta inválida: esperado um array de vagas');
        }
        
        if (vagas.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" style="text-align: center;">Nenhuma vaga encontrada.</td></tr>';
            return;
        }

        vagas.forEach(vaga => {
            const titulo = vaga.titulo || '';
            const depto = vaga.departamento || '';
            const situacao = vaga.situacao || '';
            const htmlLinha = `
                <tr id="vaga-${vaga.id_vaga}">
                    <td>${escapeHTML(titulo)}</td>
                    <td>${escapeHTML(depto)}</td>
                    <td><span class="status-badge ${situacao === 'aberta' ? 'status-open' : 'status-draft'}">${escapeHTML(situacao.charAt(0).toUpperCase() + situacao.slice(1))}</span></td>
                    <td class="actions">
                        <div class="action-icons">
                            <button onclick="abrirModalEdicao(${vaga.id_vaga})" class="icon-btn icon-edit" title="Editar Vaga"><i class="fas fa-edit"></i></button>
                            <button onclick="excluirVaga(${vaga.id_vaga}, '${escapeHTML(titulo)}')" class="icon-btn icon-delete" title="Excluir Vaga"><i class="fas fa-trash-alt"></i></button>
                            <button onclick="abrirModalCandidatos(${vaga.id_vaga})" class="icon-btn icon-view" title="Ver Candidatos"><i class="fas fa-search"></i></button>
                        </div>
                    </td>
                </tr>
            `;
            tbody.innerHTML += htmlLinha;
        });
    } catch (error) {
        console.error('Erro ao carregar vagas:', error);
        tbody.innerHTML = `<tr><td colspan="4" style="text-align: center; color: red;">Falha ao carregar dados. ${error.message || error}</td></tr>`;
    }
}

async function excluirVaga(id, titulo) {
    if (!confirm(`Tem certeza que deseja excluir a vaga "${titulo}"?`)) {
        return;
    }
    try {
        const response = await fetch(`${BASE_URL}/api/vagas/excluir?id=${id}`);
        const resultado = await response.json();
        
        if (resultado.success) {
            document.getElementById(`vaga-${id}`).remove();
        } else {
            alert('Erro ao excluir: ' + (resultado.mensagem || 'Erro desconhecido'));
        }
    } catch (error) {
        alert('Erro de conexão ao tentar excluir a vaga.');
    }
}

async function abrirModalCandidatos(idVaga) {
    const modalBackdrop = document.getElementById('modal-backdrop');
    const modalCandidatos = document.getElementById('modal-candidatos');
    const tituloEl = document.getElementById('modal-titulo-vaga');
    const tbodyEl = document.getElementById('modal-tabela-candidatos');
    modalBackdrop.style.display = 'block';
    modalCandidatos.style.display = 'block';
    tituloEl.textContent = 'Carregando...';
    tbodyEl.innerHTML = '<tr><td colspan="4" style="text-align: center;">Buscando candidatos...</td></tr>';
    try {
        const response = await fetch(`${BASE_URL}/api/vagas/candidatos?id=${idVaga}`);
        const resultado = await response.json();
        
        if (!response.ok || resultado.success === false) {
            throw new Error(resultado.mensagem || 'Falha ao buscar dados.');
        }
        
        const data = resultado.data;
        tituloEl.textContent = `Candidatos para: ${data.vaga.titulo_vaga}`;
        tbodyEl.innerHTML = '';
        if (data.candidatos.length === 0) {
            tbodyEl.innerHTML = '<tr><td colspan="4" style="text-align: center;">Nenhum candidato encontrado.</td></tr>';
            return;
        }
        data.candidatos.forEach(candidato => {
            const nome = candidato.nome_completo || '';
            const dataCandidatura = new Date(candidato.data_candidatura).toLocaleDateString('pt-BR', { timeZone: 'UTC' });
            const score = candidato.pontuacao_aderencia;
            const htmlLinha = `
                <tr>
                    <td>${escapeHTML(nome)}</td>
                    <td>${dataCandidatura}</td>
                    <td>${score !== null ? `<span class="status-badge status-open">${score}%</span>` : '<span>N/A</span>'}</td>
                    <td class="actions">
                        <div class="action-icons">
                            <a href="${BASE_URL}${candidato.curriculo}" target="_blank" class="icon-btn icon-view" title="Ver Currículo"><i class="fas fa-download"></i></a>
                        </div>
                    </td>
                </tr>
            `;
            tbodyEl.innerHTML += htmlLinha;
        });
    } catch (error) {
        console.error('Erro ao abrir modal:', error);
        tituloEl.textContent = 'Erro ao Carregar';
        tbodyEl.innerHTML = `<tr><td colspan="4" style="text-align: center; color: red;">${error.message}</td></tr>`;
    }
}

function fecharModalCandidatos() {
    const modalCandidatos = document.getElementById('modal-candidatos');
    const modalBackdrop = document.getElementById('modal-backdrop');
    if (modalCandidatos) modalCandidatos.style.display = 'none';
    if (modalBackdrop) modalBackdrop.style.display = 'none';
}

async function abrirModalEdicao(idVaga) {
    const modalBackdrop = document.getElementById('modal-backdrop');
    const modalEdicao = document.getElementById('modal-edicao-vaga');
    const messageDiv = document.getElementById('api-message-edit');
    messageDiv.style.display = 'none';
    modalBackdrop.style.display = 'block';
    modalEdicao.style.display = 'block';
    try {
        const response = await fetch(`${BASE_URL}/api/vagas/editar?id=${idVaga}`);
        const resultado = await response.json();
        
        if (!response.ok || resultado.success === false) {
            throw new Error(resultado.mensagem || 'Falha ao buscar dados da vaga.');
        }
        
        const vaga = resultado.data;
        document.getElementById('edit-id-vaga').value = vaga.id_vaga;
        document.getElementById('modal-edicao-titulo').textContent = `Editar Vaga: ${vaga.titulo_vaga}`;
        document.getElementById('edit-titulo').value = vaga.titulo_vaga;
        document.getElementById('edit-departamento').value = vaga.nome_setor;
        document.getElementById('edit-descricao').value = vaga.descricao_vaga;
        document.getElementById('edit-status').value = vaga.situacao;
        document.getElementById('edit-skills-necessarias').value = vaga.requisitos_necessarios || '';
        document.getElementById('edit-skills-recomendadas').value = vaga.requisitos_recomendados || '';
        document.getElementById('edit-skills-desejadas').value = vaga.requisitos_desejados || '';
    } catch (error) {
        messageDiv.innerHTML = error.message;
        messageDiv.className = 'msg-erro';
        messageDiv.style.display = 'block';
    }
}

function fecharModalEdicao() {
    const modalEdicao = document.getElementById('modal-edicao-vaga');
    const modalBackdrop = document.getElementById('modal-backdrop');
    if (modalEdicao) modalEdicao.style.display = 'none';
    if (modalBackdrop) modalBackdrop.style.display = 'none';
}

async function salvarEdicao() {
    const form = document.getElementById('form-editar-vaga');
    const submitButton = document.getElementById('btn-salvar-edicao');
    const messageDiv = document.getElementById('api-message-edit');
    submitButton.disabled = true;
    submitButton.textContent = 'Salvando...';
    const formData = new FormData(form);
    const dadosVaga = Object.fromEntries(formData.entries());
    try {
        const response = await fetch(`${BASE_URL}/api/vagas/atualizar`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(dadosVaga)
        });
        const resultado = await response.json();
        
        if (!response.ok || resultado.success === false) {
            throw new Error(resultado.mensagem || 'Ocorreu um erro ao salvar.');
        }
        
        fecharModalEdicao();
        carregarVagas();
    } catch (error) {
        messageDiv.innerHTML = error.message;
        messageDiv.className = 'msg-erro';
        messageDiv.style.display = 'block';
    } finally {
        submitButton.disabled = false;
        submitButton.textContent = 'Salvar Alterações';
    }
}

function abrirModalCriacao() {
    // 1. Limpa o formulário de valores antigos
    document.getElementById('form-criar-vaga').reset();

    // 2. Esconde mensagens de erro/sucesso antigas
    document.getElementById('api-message-create').style.display = 'none';

    // 3. Mostra o modal
    document.getElementById('modal-backdrop').style.display = 'block';
    document.getElementById('modal-criacao-vaga').style.display = 'block';
}

function fecharModalCriacao() {
    document.getElementById('modal-criacao-vaga').style.display = 'none';
    document.getElementById('modal-backdrop').style.display = 'none';
}

async function salvarCriacao() {
    const form = document.getElementById('form-criar-vaga');
    const submitButton = document.getElementById('btn-salvar-criacao');
    const messageDiv = document.getElementById('api-message-create');

    submitButton.disabled = true;
    submitButton.textContent = 'Salvando...';

    const formData = new FormData(form);
    const dadosVaga = Object.fromEntries(formData.entries());

    try {
        const response = await fetch(`${BASE_URL}/api/vagas/salvar`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(dadosVaga)
        });

        const resultado = await response.json();
        
        if (!response.ok || resultado.success === false) {
            let erroMsg = resultado.mensagem || 'Ocorreu um erro ao salvar.';
            // Verifica se há erros de validação
            if (resultado.data && resultado.data.erros) {
                erroMsg = Object.values(resultado.data.erros).join('<br>');
            }
            throw new Error(erroMsg);
        }

        // Sucesso! Fecha o modal e recarrega a tabela
        fecharModalCriacao();
        carregarVagas();

    } catch (error) {
        messageDiv.innerHTML = error.message;
        messageDiv.className = 'msg-erro';
        messageDiv.style.display = 'block';
    } finally {
        submitButton.disabled = false;
        submitButton.textContent = 'Salvar Vaga';
    }
}

// Função auxiliar para escapar HTML
function escapeHTML(str) {
    if (!str) return '';
    return str.replace(/[&<>"']/g, function (m) {
        return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m];
    });
}