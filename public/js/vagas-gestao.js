
document.addEventListener('DOMContentLoaded', function() {
    
    // Pega todos os elementos dos modais DEPOIS que a página carregar
    const modalCandidatos = document.getElementById('modal-candidatos');
    const modalEdicao = document.getElementById('modal-edicao-vaga');
    const modalBackdrop = document.getElementById('modal-backdrop');
    
    // 1. Carrega a tabela de vagas principal
    carregarVagas();

    // 2. Adiciona eventos para fechar os modais
    // Verifica se os elementos existem antes de adicionar listeners
    if (modalBackdrop) {
        modalBackdrop.addEventListener('click', () => {
            fecharModalCandidatos();
            fecharModalEdicao();
        });
    }
    
    // Botões de fechar/cancelar do Modal de Candidatos
    const modalCloseBtn = document.getElementById('modal-close-btn');
    if (modalCloseBtn) {
        modalCloseBtn.addEventListener('click', fecharModalCandidatos);
    }

    // Botões de fechar/cancelar do Modal de Edição
    const modalEdicaoCloseBtn = document.getElementById('modal-edicao-close-btn');
    const modalEdicaoCancelBtn = document.getElementById('modal-edicao-cancel-btn');
    if (modalEdicaoCloseBtn) {
        modalEdicaoCloseBtn.addEventListener('click', fecharModalEdicao);
    }
    if (modalEdicaoCancelBtn) {
        modalEdicaoCancelBtn.addEventListener('click', fecharModalEdicao);
    }

    // 3. Adiciona evento de submit para o formulário de edição
    const formEditar = document.getElementById('form-editar-vaga');
    if (formEditar) {
        formEditar.addEventListener('submit', function(event) {
            event.preventDefault(); // Impede o envio tradicional
            salvarEdicao(); // Chama nossa função de API
        });
    }
});

async function carregarVagas() {
    // ... (Esta função estava correta e não precisa de mudanças) ...
    const tbody = document.getElementById('tabela-vagas-corpo');
    if (!tbody) {
        console.error("Erro: Elemento 'tabela-vagas-corpo' não encontrado.");
        return;
    }
    tbody.innerHTML = '<tr><td colspan="4" style="text-align: center;">Carregando...</td></tr>';
    try {
        const response = await fetch(`${BASE_URL}/api/vagas/listar`);
        const vagas = await response.json();
        tbody.innerHTML = ''; 
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
        tbody.innerHTML = `<tr><td colspan="4" style="text-align: center; color: red;">Falha ao carregar dados.</td></tr>`;
    }
}

async function excluirVaga(id, titulo) {
    // ... (Esta função estava correta e não precisa de mudanças) ...
    if (!confirm(`Tem certeza que deseja excluir a vaga "${titulo}"?`)) {
        return;
    }
    try {
        const response = await fetch(`${BASE_URL}/api/vagas/excluir?id=${id}`);
        const resultado = await response.json();
        if (resultado.sucesso) {
            document.getElementById(`vaga-${id}`).remove();
        } else {
            alert('Erro ao excluir: ' (resultado.erro || 'Erro desconhecido'));
        }
    } catch (error) {
        alert('Erro de conexão ao tentar excluir a vaga.');
    }
}

async function abrirModalCandidatos(idVaga) {
    // CORREÇÃO #2: Usa a variável correta `modalBackdrop`
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
        const data = await response.json();
        if (!response.ok) throw new Error(data.erro || 'Falha ao buscar dados.');
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
    document.getElementById('modal-candidatos').style.display = 'none';
    document.getElementById('modal-backdrop').style.display = 'none';
}

async function abrirModalEdicao(idVaga) {
    // CORREÇÃO #2: Usa a variável correta `modalBackdrop`
    const modalBackdrop = document.getElementById('modal-backdrop');
    const modalEdicao = document.getElementById('modal-edicao-vaga');
    const messageDiv = document.getElementById('api-message-edit');
    messageDiv.style.display = 'none';

    modalBackdrop.style.display = 'block';
    modalEdicao.style.display = 'block';

    try {
        const response = await fetch(`${BASE_URL}/api/vagas/editar?id=${idVaga}`);
        if (!response.ok) throw new Error('Falha ao buscar dados da vaga.');
        const vaga = await response.json();
        if (vaga.erro) throw new Error(vaga.erro);

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
    document.getElementById('modal-edicao-vaga').style.display = 'none';
    document.getElementById('modal-backdrop').style.display = 'none';
}

async function salvarEdicao() {
    // ... (Esta função estava correta e não precisa de mudanças) ...
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
            headers: {'Content-Type': 'application/json', 'Accept': 'application/json'},
            body: JSON.stringify(dadosVaga)
        });
        const resultado = await response.json();
        if (!response.ok || !resultado.sucesso) {
            throw new Error(resultado.erro || 'Ocorreu um erro ao salvar.');
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

//funcao utilitaria
function escapeHTML(str) {
    if (!str) return '';
    return str.replace(/[&<>"']/g, function(m) {
        return {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'}[m];
    });
}