// Este script é carregado pela página editarVaga.php

// Pega as referências dos elementos do formulário
const form = document.getElementById('form-editar-vaga');
const submitButton = document.getElementById('btn-salvar-vaga');
const messageDiv = document.getElementById('api-message');

/**
 * PASSO 1: Carregar os dados da vaga assim que a página abre
 */
document.addEventListener('DOMContentLoaded', function() {
    // Só executa se o formulário de edição estiver na página
    if (form) {
        carregarDadosVaga();

        /**
         * PASSO 2: Enviar os dados atualizados ao submeter o formulário
         */
        form.addEventListener('submit', async function(event) {
            event.preventDefault(); // Previne o recarregamento da página
            submitButton.disabled = true;
            submitButton.textContent = 'Salvando...';
            messageDiv.style.display = 'none';

            // Pega os dados do formulário (incluindo o ID do campo oculto)
            const formData = new FormData(form);
            const dadosVaga = Object.fromEntries(formData.entries());

            try {
                // A variável 'BASE_URL' é global, definida no HTML
                const response = await fetch(`${BASE_URL}/api/vagas/atualizar`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(dadosVaga)
                });

                const resultado = await response.json();

                if (!response.ok || !resultado.sucesso) {
                    let erroMsg = resultado.erro || 'Ocorreu um erro ao salvar.';
                    if (resultado.erros) {
                        erroMsg = Object.values(resultado.erros).join('<br>');
                    }
                    throw new Error(erroMsg);
                }

                // Sucesso!
                messageDiv.innerHTML = 'Vaga atualizada com sucesso! Redirecionando...';
                messageDiv.className = 'msg-sucesso';
                messageDiv.style.display = 'block';

                setTimeout(() => {
                    window.location.href = `${BASE_URL}/vagas/listar`;
                }, 2000);

            } catch (error) {
                // Falha
                messageDiv.innerHTML = error.message;
                messageDiv.className = 'msg-erro';
                messageDiv.style.display = 'block';
                submitButton.disabled = false;
                submitButton.textContent = 'Salvar Alterações';
            }
        });
    }
});

/**
 * Função para buscar os dados da vaga na API e preencher o formulário.
 */
async function carregarDadosVaga() {
    try {
        // Pega o ID da vaga da URL (ex: ?id=5)
        const urlParams = new URLSearchParams(window.location.search);
        const idVaga = urlParams.get('id');

        if (!idVaga) {
            throw new Error('ID da vaga não encontrado na URL.');
        }

        // Chama a API de "editar" (GET) para buscar os dados
        // A variável 'BASE_URL' é global, definida no HTML
        const response = await fetch(`${BASE_URL}/api/vagas/editar?id=${idVaga}`);
        if (!response.ok) {
            throw new Error('Falha ao buscar dados da vaga. Status: ' + response.status);
        }
        
        const vaga = await response.json();
        
        if (vaga.erro) {
            throw new Error(vaga.erro);
        }

        // Preenche o formulário com os dados recebidos
        document.getElementById('id_vaga').value = vaga.id_vaga;
        document.getElementById('titulo').value = vaga.titulo_vaga;
        document.getElementById('departamento').value = vaga.nome_setor;
        document.getElementById('descricao').value = vaga.descricao_vaga;
        document.getElementById('status').value = vaga.situacao;
        document.getElementById('skills_necessarias').value = vaga.requisitos_necessarios || '';
        document.getElementById('skills_recomendadas').value = vaga.requisitos_recomendados || '';
        document.getElementById('skills_desejadas').value = vaga.requisitos_desejados || '';

    } catch (error) {
        messageDiv.innerHTML = error.message;
        messageDiv.className = 'msg-erro';
        messageDiv.style.display = 'block';
        submitButton.disabled = true; // Desabilita o salvamento se não puder carregar
    }
}