// 1. Executa a função assim que a página carregar
document.addEventListener('DOMContentLoaded', function() {
    carregarVagas();
});

/**
 * Função para buscar dados da API e construir a tabela.
 */
async function carregarVagas() {
    // A variável 'BASE_URL' é pega do script global na view HTML
    const tbody = document.getElementById('tabela-vagas-corpo');
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
                    <td>
                        <span class="status-badge ${situacao === 'aberta' ? 'status-open' : 'status-draft'}">
                            ${escapeHTML(situacao.charAt(0).toUpperCase() + situacao.slice(1))}
                        </span>
                    </td>
                    <td class="actions">
                        <div class="action-icons">
                            <a href="${BASE_URL}/vagas/editar?id=${vaga.id_vaga}" class="icon-btn icon-edit" title="Editar Vaga">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button onclick="excluirVaga(${vaga.id_vaga}, '${escapeHTML(titulo)}')" class="icon-btn icon-delete" title="Excluir Vaga">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                            <form action="${BASE_URL}/vagas/candidatos" method="POST">
                                <input type="hidden" name="id" value="${vaga.id_vaga}">
                                <button type="submit" class="icon-btn icon-view" title="Ver Candidatos">
                                    <i class="fas fa-search"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            `;
            tbody.innerHTML += htmlLinha;
        });

    } catch (error) {
        console.error('Erro:', error);
        tbody.innerHTML = `<tr><td colspan="4" style="text-align: center; color: red;">Falha ao carregar dados.</td></tr>`;
    }
}

/**
 * Função para excluir uma vaga (usando a API)
 */
async function excluirVaga(id, titulo) {
    if (!confirm(`Tem certeza que deseja excluir a vaga "${titulo}"?`)) {
        return;
    }
    try {
        const response = await fetch(`${BASE_URL}/api/vagas/excluir?id=${id}`);
        const resultado = await response.json();

        if (resultado.sucesso) {
            document.getElementById(`vaga-${id}`).remove();
        } else {
            alert('Erro ao excluir: ' + resultado.erro);
        }
    } catch (error) {
        alert('Erro de conexão ao tentar excluir a vaga.');
    }
}

// Função de segurança contra XSS (equivalente ao htmlspecialchars)
function escapeHTML(str) {
    if (!str) return '';
    return str.replace(/[&<>"']/g, function(m) {
        return {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;'
        }[m];
    });
}