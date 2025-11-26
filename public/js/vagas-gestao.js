$(document).ready(function() {

    const API_URL = BASE_URL + '/api/vagas';

    // --- 1. INSTÂNCIAS DOS MODAIS ---
    // Usamos bootstrap.Modal.getOrCreateInstance para evitar duplicações
    const modalCriacao = new bootstrap.Modal(document.getElementById('modal-criacao-vaga'));
    const modalEdicao = new bootstrap.Modal(document.getElementById('modal-edicao-vaga'));
    const modalCandidatos = new bootstrap.Modal(document.getElementById('modal-candidatos'));
    const modalAnalise = new bootstrap.Modal(document.getElementById('modal-analise-ia'));

    // --- 2. CARREGAR TABELA DE VAGAS ---
    function carregarVagas() {
        $('#tabela-vagas-corpo').html('<tr><td colspan="4" class="text-center">Carregando...</td></tr>');

        $.get(API_URL + '/listar', function(res) {
            if (typeof res !== 'object') {
                console.error("Resposta inválida:", res);
                return;
            }

            if (res.success) {
                const vagas = res.data;
                let html = '';

                if (vagas.length === 0) {
                    html = '<tr><td colspan="4" class="text-center">Nenhuma vaga encontrada.</td></tr>';
                } else {
                    vagas.forEach(vaga => {
                        let badgeClass = 'status-draft';
                        if (vaga.status === 'aberta') badgeClass = 'status-open';
                        if (vaga.status === 'fechada') badgeClass = 'status-closed';

                        html += `
                            <tr>
                                <td><strong>${vaga.titulo}</strong></td>
                                <td>${vaga.departamento}</td>
                                <td><span class="status-badge ${badgeClass}">${vaga.status.toUpperCase()}</span></td>
                                <td class="actions text-center">
                                    <div class="action-icons">
                                        <button class="icon-btn icon-view btn-ver-candidatos" data-id="${vaga.id_vaga}" title="Ver Candidatos"><i class="fas fa-users"></i></button>
                                        <button class="icon-btn icon-edit btn-editar-vaga" data-id="${vaga.id_vaga}" title="Editar"><i class="fas fa-edit"></i></button>
                                        <button class="icon-btn icon-delete btn-excluir-vaga" data-id="${vaga.id_vaga}" title="Excluir"><i class="fas fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>`;
                    });
                }
                $('#tabela-vagas-corpo').html(html);
            } else {
                alert('Erro ao carregar: ' + res.message);
            }
        }).fail(function(xhr) {
            console.error(xhr.responseText);
            $('#tabela-vagas-corpo').html('<tr><td colspan="4" class="text-center text-danger">Erro no servidor.</td></tr>');
        });
    }

    // Carrega ao iniciar
    carregarVagas();

    // --- 3. AÇÕES: CRIAR VAGA ---
    $('#btn-abrir-modal-criacao').click(function() {
        $('#form-criar-vaga')[0].reset();
        $('#api-message-create').hide();
        modalCriacao.show();
    });

    $('#form-criar-vaga').submit(function(e) {
        e.preventDefault();
        const btn = $('#btn-salvar-criacao');
        btn.prop('disabled', true).text('Salvando...');

        $.post(API_URL + '/salvar', $(this).serialize(), function(res) {
            if (res.success) {
                modalCriacao.hide();
                alert(res.data || res.message);
                carregarVagas();
            } else {
                $('#api-message-create').removeClass('msg-sucesso').addClass('msg-erro').text(res.message).show();
            }
        }, 'json')
            .fail(function(xhr) { alert('Erro ao salvar: ' + xhr.responseText); })
            .always(function() { btn.prop('disabled', false).text('Salvar Vaga'); });
    });

    // --- 4. AÇÕES: EDITAR VAGA ---
    $(document).on('click', '.btn-editar-vaga', function() {
        const id = $(this).data('id');
        $.get(API_URL + '/editar', { id: id }, function(res) {
            if (res.success) {
                const v = res.data;
                $('#edit-id-vaga').val(v.id_vaga);
                $('#edit-titulo').val(v.titulo);
                $('#edit-departamento').val(v.departamento);
                $('#edit-descricao').val(v.descricao);
                $('#edit-status').val(v.status);
                $('#edit-skills-necessarias').val(v.skills_necessarias);
                $('#edit-skills-recomendadas').val(v.skills_recomendadas);

                $('#api-message-edit').hide();
                modalEdicao.show();
            } else {
                alert('Erro ao carregar: ' + res.message);
            }
        });
    });

    $('#form-editar-vaga').submit(function(e) {
        e.preventDefault();
        $.post(API_URL + '/atualizar', $(this).serialize(), function(res) {
            if (res.success) {
                modalEdicao.hide();
                alert(res.message);
                carregarVagas();
            } else {
                $('#api-message-edit').removeClass('msg-sucesso').addClass('msg-erro').text(res.message).show();
            }
        }, 'json');
    });

    // --- 5. AÇÕES: EXCLUIR ---
    $(document).on('click', '.btn-excluir-vaga', function() {
        const id = $(this).data('id');
        if (confirm('Tem certeza que deseja excluir?')) {
            $.get(API_URL + '/excluir', { id: id }, function(res) {
                if (res.success) carregarVagas();
                else alert('Erro: ' + res.message);
            }, 'json');
        }
    });

    // --- 6. AÇÕES: VER CANDIDATOS ---
    $(document).on('click', '.btn-ver-candidatos', function() {
        const id = $(this).data('id');
        const tbody = $('#modal-tabela-candidatos');

        tbody.html('<tr><td colspan="4" class="text-center">Carregando...</td></tr>');
        $('#modal-titulo-vaga').text('Carregando...');
        modalCandidatos.show();

        $.get(API_URL + '/candidatos', { id: id }, function(res) {
            if (res.success) {
                $('#modal-titulo-vaga').text('Candidatos: ' + (res.titulo_vaga || 'Vaga'));

                let html = '';
                if (res.data.length === 0) {
                    html = '<tr><td colspan="4" class="text-center">Nenhum candidato.</td></tr>';
                } else {
                    res.data.forEach(cand => {
                        // Lógica Corrigida para decidir entre Robô e Olho
                        const temAnalise = (cand.pontuacao_aderencia !== null && cand.pontuacao_aderencia !== undefined);
                        const score = temAnalise ? `${cand.pontuacao_aderencia}%` : 'N/A';

                        const linkCurriculo = BASE_URL + (cand.caminho_curriculo || '');

                        // Define qual botão mostrar
                        let btnIA = '';
                        if (temAnalise) {
                            // Já tem análise -> Botão OLHO (Abre modal AJAX)
                            btnIA = `<button type="button" class="icon-btn icon-view btn-ver-analise" 
                                             data-id="${cand.id_candidatura}" title="Ver Análise">
                                        <i class="fas fa-eye"></i>
                                     </button>`;
                        } else {
                            // Não tem análise -> Botão ROBÔ (Processa)
                            btnIA = `<form action="${BASE_URL}/candidatura/analisar" method="POST" style="display:inline;">
                                        <input type="hidden" name="id_candidatura" value="${cand.id_candidatura}">
                                        <button type="submit" class="icon-btn icon-edit" title="Processar IA">
                                            <i class="fas fa-robot"></i>
                                        </button>
                                     </form>`;
                        }

                        html += `
                            <tr>
                                <td>${cand.nome_completo}</td>
                                <td>${new Date(cand.data_candidatura).toLocaleDateString('pt-BR')}</td>
                                <td><span class="status-badge status-open">${score}</span></td>
                                <td class="actions">
                                    <div class="action-icons">
                                        <a href="${linkCurriculo}" target="_blank" class="icon-btn icon-view" title="Ver Currículo">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        ${btnIA}
                                    </div>
                                </td>
                            </tr>`;
                    });
                }
                tbody.html(html);
            } else {
                tbody.html(`<tr><td colspan="4" class="text-center text-danger">${res.message}</td></tr>`);
            }
        }, 'json');
    });

    // --- 7. AÇÕES: VER ANÁLISE (OLHO - MODAL AJAX) ---
    // Essa parte estava faltando ou incompleta
    $(document).on('click', '.btn-ver-analise', function() {
        const id = $(this).data('id');

        // 1. Reseta e Abre o Modal
        $('#analise-loading').show();
        $('#analise-conteudo').hide();
        modalAnalise.show();

        // 2. Busca os dados via AJAX
        $.post(BASE_URL + '/candidatura/ver-analise', { id_candidatura: id }, function(res) {

            if (res.success) {
                const dados = res.data;

                // Preenche os campos
                $('#analise-candidato').text(dados.nome_completo);
                $('#analise-score-val').text(dados.pontuacao + '%');
                $('#analise-texto').text(dados.justificativa);

                // Atualiza a cor do gráfico (conic-gradient via CSS inline)
                const grau = dados.pontuacao * 3.6;
                $('#analise-score-bg').css('background',
                    `conic-gradient(#28a745 ${grau}deg, #e9ecef ${grau}deg)`
                );

                // Mostra o conteúdo
                $('#analise-loading').hide();
                $('#analise-conteudo').fadeIn();
            } else {
                alert('Erro ao carregar análise: ' + res.message);
                modalAnalise.hide();
            }

        }, 'json').fail(function(xhr) {
            console.error(xhr.responseText);
            let msg = "Erro desconhecido";
            if (xhr.status === 404) msg = "Rota não encontrada (404)";
            if (xhr.status === 500) msg = "Erro Interno no PHP (500)";

            alert(`Erro no servidor (${xhr.status}): Verifique o console.`);
            modalAnalise.hide();
        });
    });

    // Botão Fechar Modal Genérico
    $('.btn-cancelar').click(function() {
        const modalEl = $(this).closest('.modal')[0];
        const modalInstance = bootstrap.Modal.getInstance(modalEl);
        if(modalInstance) modalInstance.hide();
    });
});