$(document).ready(function() {
    // VARIÁVEIS DO MODAL DE BENEFÍCIO (Catálogo)
    const modalBeneficio = $('#modalBeneficio');
    const btnAdicionar = $('.btn-adicionar');
    // Adiciona uma classe ao botão de fechar dentro do modal para simplificar
    const btnCancelarBeneficio = modalBeneficio.find('.btnFecharModal'); 
    const btnCloseIconBeneficio = modalBeneficio.find('.fechar-modal-beneficio'); 
    const btnSalvarBeneficioCatalogo = $('#btnSalvarBeneficio');

    const formBeneficio = document.getElementById('formBeneficio');
    const tituloModalBeneficio = $('#tituloModal');
    const tipoValorSelect = $('#tipoValorBeneficio');
    const valorFixoField = $('#valorFixoField');
    const descricaoField = $('#descricaoField');
    const valorFixoInput = $('#valorFixoBeneficio');
    const descricaoTextarea = $('#descricaoBeneficio');
    
    // VARIÁVEIS DA REGRAS DE ATRIBUIÇÃO
    const modalRegras = $('#modalRegras');
    const btnSalvarRegras = $('#btnSalvarRegras');

    // VARIÁVEIS DA TAB EXCEÇÕES (Busca Dinâmica e Painel Inline)
    const inputPesquisar = $('#inputPesquisarColaborador');
    const resultadoPesquisa = $('#resultadoPesquisa');

    const painelEdicaoColaborador = $('#painelEdicaoColaborador');
    const colaboradorIdAtual = $('#colaboradorIdAtual');
    const nomeColaboradorEdicao = $('#nomeColaboradorEdicao');
    const matriculaColaborador = $('#matriculaColaborador');
    const contratoColaborador = $('#contratoColaborador');
    const listaBeneficiosColaborador = $('#listaBeneficiosColaborador');
    const btnSalvarExcecoes = $('#btnSalvarBeneficiosColaborador');
    const btnCancelarExcecoes = $('#btnCancelarEdicao');

    const URL_BASE_BENEFICIO = '/RHease/public/beneficios'; 
    const URL_BASE_COLABORADOR = '/RHease/public/colaborador'; 


    // --- FUNÇÕES DE CONTROLE DE MODAIS ---
    const fecharModalBeneficio = () => {
        modalBeneficio.hide();
        if (formBeneficio) formBeneficio.reset();
        valorFixoField.hide();
        descricaoField.hide();
    };

    const fecharModalRegras = () => {
        modalRegras.hide();
    };
    
    // Evento: Abrir Modal de Novo Benefício
    btnAdicionar.click(() => {
    tituloModalBeneficio.text('Novo Benefício');
    $('#beneficioId').val('');
    formBeneficio.reset();
    valorFixoField.hide();
    descricaoField.hide();
    modalBeneficio.css('display', 'flex');
});


    // Eventos de Fechar Modais
    btnCancelarBeneficio.click(fecharModalBeneficio);
    btnCloseIconBeneficio.click(fecharModalBeneficio);
    $('.fechar-modal-regras').click(fecharModalRegras);
    $('.btnFecharModalRegras').click(fecharModalRegras);

    // Gerencia cliques fora dos modais 
    $(window).click((e) => { 
        if($(e.target).is('#modalBeneficio')) fecharModalBeneficio(); 
        if($(e.target).is('#modalRegras')) fecharModalRegras();
    });

    // --- LÓGICA DO MODAL DE BENEFÍCIO ---
    tipoValorSelect.on('change', () => {
        const valor = tipoValorSelect.val();
        valorFixoField.toggle(valor === 'Fixo');
        descricaoField.toggle(valor === 'Descritivo'); // Manter o campo Descrição na view, mas o Controller/Model não o salva.

        if (valor !== 'Fixo') valorFixoInput.val('');
        // A descrição não é salva no novo Model, mas pode ser limpa para UX
        if (valor !== 'Descritivo') descricaoTextarea.val(''); 
    });


    // Salvar Benefício (Criação e Edição)
    $('#btnSalvarBeneficio').click(function() {
        const id = $('#beneficioId').val();
        
        const nome = $('#nomeBeneficio').val();
        const categoria = $('#categoriaBeneficio').val();
        const tipoValor = tipoValorSelect.val();
        
        // VALIDAÇÕES BÁSICAS
        if (!nome.trim() || !categoria || !tipoValor) {
            alert("Preencha Nome, Categoria e Tipo de Valor.");
            return;
        }
        
        const data = {
            // A ação não é mais necessária, a rota define a ação no Controller
            id: id,
            nome: nome, 
            categoria: categoria, 
            tipo_valor: tipoValor, 
            valor_fixo: valorFixoInput.val(), 
            // 'descricao' foi removida da lógica do Controller/Model, mas mantida no POST se necessário
            descricao: descricaoTextarea.val() 
        };
        
        if (data.tipo_valor === 'Fixo' && (!data.valor_fixo || isNaN(parseFloat(data.valor_fixo)))) {
            alert("Por favor, insira um valor fixo válido.");
            return;
        }

        // << MUDANÇA DE ENDPOINT >>
        $.post(URL_BASE_BENEFICIO + '/salvar', data, function(res) { 
            alert(res.mensagem);
            if(res.success) location.reload();
        }, 'json').fail(function(jqXHR, textStatus, errorThrown) {
            alert('Erro na comunicação com o servidor ao salvar benefício: ' + textStatus);
        });
    });

    // Editar benefício
    $('.editar').click(function(){
        const row = $(this).closest('tr');
        const id = row.data('id');
        const nome = row.find('td:eq(0)').text();
        const categoria = row.find('td:eq(1)').text();
        const tipoValorPuro = row.data('tipo-valor-puro');
        // Usar data-valor-fixo (sem formatação R$)
        const valorFixo = row.data('valor-fixo'); 

        $('#beneficioId').val(id);
        $('#nomeBeneficio').val(nome);
        $('#categoriaBeneficio').val(categoria);
        
        tipoValorSelect.val(tipoValorPuro).trigger('change');
        
        if (tipoValorPuro === 'Fixo') {
            // Valor fixo já está sem formatação R$
            valorFixoInput.val(valorFixo);
        } else {
            valorFixoInput.val('');
            // Descrição é opcional
            descricaoTextarea.val(''); 
        }

        tituloModalBeneficio.text('Editar Benefício');
        modalBeneficio.css('display', 'flex');
    });

    // Implementação do Switch (Toggle) para status
    $('.switch input[type="checkbox"]').change(function() {
        const isChecked = $(this).is(':checked');
        const row = $(this).closest('tr');
        const id = row.data('id');
        
        const data = {
            // A ação 'desativar' agora é a rota /toggleStatus
            id: id
        };
        
        // << MUDANÇA DE ENDPOINT >>
        $.post(URL_BASE_BENEFICIO + '/toggleStatus', data, function(res) {
            if(res.success) {
                row.find('td:eq(3)').text(isChecked ? 'Ativo' : 'Inativo');
                //location.reload(); // Não precisa recarregar se a linha for atualizada
                // No entanto, para fins de demonstração (se a lista de seleção muda), recarregar é mais seguro
                location.reload(); 
            } else {
                alert(res.mensagem);
                $(this).prop('checked', !isChecked); // Reverte o estado do switch
            }
        }.bind(this), 'json').fail(function() {
            alert('Erro ao comunicar com o servidor para mudar status.');
            $(this).prop('checked', !isChecked); 
        }.bind(this));
    });

    // --- LÓGICA DE DELEÇÃO PERMANENTE ---
    $('.deletar').click(function() {
        const row = $(this).closest('tr');
        const id = row.data('id');
        const nome = row.find('td:eq(0)').text();

        const confirmacao = confirm(`ATENÇÃO: Você tem certeza que deseja DELETAR PERMANENTEMENTE o benefício "${nome}"? Esta ação não pode ser desfeita.`);

        if (confirmacao) {
            const data = {
                // A ação 'deletar' agora é a rota /deletar
                id: id
            };

            // << MUDANÇA DE ENDPOINT >>
            $.post(URL_BASE_BENEFICIO + '/deletar', data, function(res) {
                alert(res.mensagem);
                if(res.success) {
                    row.remove();
                    location.reload(); 
                }
            }, 'json').fail(function() {
                alert('Erro na comunicação com o servidor ao deletar.');
            });
        }
    });

    // --- LÓGICA DO NOVO MODAL DE REGRAS ---
    $(document).on('click', '.editar-regra', function() {
        const row = $(this).closest('tr');
        const tipoContrato = row.data('tipo-contrato');

        const idsString = row.attr('data-beneficios-ids');

        let idsAtribuidos = [];
        if (idsString && idsString.length > 0) {
            idsAtribuidos = idsString.split(',')            // Agora é 100% seguro usar .split()
                .map(id => parseInt(id.trim()))             // Converte cada item para número
                .filter(id => !isNaN(id) && id > 0);        // Remove itens que não são números válidos
        }

        // 1. ATUALIZA O MODAL
        $('#tituloRegrasModal').text('Regras para Contrato: ' + (tipoContrato || 'Erro na Leitura')); 
        $('#contratoRegraAtual').val(tipoContrato); // Guarda o tipo de contrato no modal
        
        // 2. Itera e marca/desmarca
        $('#listaBeneficiosRegra input[type="checkbox"]').each(function() {
            const idBeneficio = parseInt($(this).val());
            const isChecked = idsAtribuidos.includes(idBeneficio);
            $(this).prop('checked', isChecked);
        });

        // 3. EXIBE O MODAL
        $('#modalRegras').css('display', 'flex'); 
    });


    // Salvar Regras
    $('#btnSalvarRegras').click(function() {
        const tipoContrato = $('#contratoRegraAtual').val(); 
        const idsSelecionados = [];

        $('#listaBeneficiosRegra input[type="checkbox"]:checked').each(function() {
            idsSelecionados.push($(this).val());
        });

        const data = {
            // A ação 'salvar_regras' agora é a rota /regras/salvar
            tipo_contrato: tipoContrato,
            beneficios_ids: idsSelecionados
        };

        // << MUDANÇA DE ENDPOINT >>
        $.post(URL_BASE_BENEFICIO + '/regras/salvar', data, function(res) {
            alert(res.mensagem);
            if (res.success) {
                location.reload();
            }
        }, 'json').fail(function(jqXHR, textStatus, errorThrown) {
            alert('Erro ao salvar regras: ' + textStatus);
        });
    });


    // --- LÓGICA DE EXCEÇÕES DE COLABORADOR ---

    // Função para abrir o painel de edição
    function abrirPainelEdicao(id) {
        colaboradorIdAtual.val(id); 
        listaBeneficiosColaborador.find('input[type="checkbox"]').prop('checked', false);
        
        // << MUDANÇA DE ENDPOINT >>
        $.post(URL_BASE_COLABORADOR + '/beneficios/carregar', { 
            // A ação 'carregar_beneficios_colaborador' agora é a rota /beneficios/carregar
            id_colaborador: id 
        }, function(res) {
            if (res.success) {
                // Os dados agora vêm aninhados (veja o Controller)
                const dados = res.dados_colaborador; 
                const idsAtuais = res.beneficios_ids; 

                // Preenche o cabeçalho
                colaboradorIdAtual.val(id);
                nomeColaboradorEdicao.text(dados.nome_completo);
                matriculaColaborador.text(dados.matricula);
                contratoColaborador.text(dados.tipo_contrato || 'Não Informado'); 

                // Marca os checkboxes dos benefícios ATRIBUÍDOS MANUALMENTE (exceções)
                idsAtuais.forEach(id_beneficio => {
                    listaBeneficiosColaborador.find(`input[value="${id_beneficio}"]`).prop('checked', true);
                });

                // Exibe o painel e limpa os resultados da pesquisa
                resultadoPesquisa.hide().empty();
                inputPesquisar.val('');
                painelEdicaoColaborador.show();

            } else {
                alert("Erro ao carregar dados: " + res.mensagem);
            }
        }, 'json').fail(function() {
            alert('Erro de comunicação ao carregar o colaborador.');
        });
        $.post(URL_BASE_COLABORADOR + '/beneficios/salvar', data, function(res) {
    // ...
        });
    }


    // Evento: Pesquisa Dinâmica
    inputPesquisar.on('keyup', function() {
        const termo = $(this).val().trim();
        resultadoPesquisa.empty();

        if (termo.length < 3) {
            resultadoPesquisa.hide();
            return;
        }

        // << MUDANÇA DE ENDPOINT >>
        $.post(URL_BASE_COLABORADOR + '/buscar', { 
            // A ação 'buscar_colaborador' agora é a rota /buscar
            termo: termo 
        }, function(res) {
            // O controller retorna colaboradores no corpo do JSON
            if (res.success && res.data && res.data.length > 0) {
                let html = '<ul class="list-group list-group-flush">';
                res.data.forEach(colaborador => {
                    html += `<li class="list-group-item d-flex justify-content-between align-items-center">
                        ${colaborador.nome_completo} (Matrícula: ${colaborador.matricula})
                        <button class="btn btn-sm btn-edit-colaborador" 
                                data-id="${colaborador.id_colaborador}" 
                                data-nome="${colaborador.nome_completo}">
                            <i class="bi bi-pencil"></i>
                        </button>
                    </li>`;
                });
                html += '</ul>';
                resultadoPesquisa.html(html).show();
            }  else {
                resultadoPesquisa.html('<div style="padding: 10px 15px; color: #777;">Nenhum colaborador encontrado.</div>').show();
            }
        }, 'json').fail(function() {
            resultadoPesquisa.html('<div style="padding: 10px 15px; color: #dc3545;">Erro ao buscar.</div>').show();
        });
    });


    // Evento: Clique no Lápis (Botão de Edição)
    $(document).on('click', '.btn-edit-colaborador', function() {
        const id = $(this).data('id');
        $('#resultadoPesquisa').hide(); 
        abrirPainelEdicao(id);
    });


    // Evento: Salvar Exceções Manuais
    btnSalvarExcecoes.click(function() {
        const id_colaborador = colaboradorIdAtual.val();
        const idsSelecionados = [];

        listaBeneficiosColaborador.find('input[type="checkbox"]:checked').each(function() {
            idsSelecionados.push($(this).val());
        });

        const data = {
            // A ação 'salvar_beneficios_colaborador' agora é a rota /beneficios/salvar
            id_colaborador: id_colaborador,
            beneficios_ids: idsSelecionados 
        };
        
        // << MUDANÇA DE ENDPOINT >>
        $.post(URL_BASE_COLABORADOR + '/beneficios/salvar', data, function(res) {
            alert(res.mensagem);
            if(res.success) {
                painelEdicaoColaborador.hide();
            }
        }, 'json').fail(function() {
            alert('Erro na comunicação ao salvar os benefícios.');
        });
    });


    // Evento: Cancelar e Ocultar o Painel
    btnCancelarExcecoes.click(function() {
        painelEdicaoColaborador.hide();
    });

    // Ocultar resultados da pesquisa ao clicar fora
    $(document).click(function(e) {
        if (!inputPesquisar.is(e.target) && !resultadoPesquisa.is(e.target) && resultadoPesquisa.has(e.target).length === 0) {
            resultadoPesquisa.hide();
        }
    });

    $(formBeneficio).submit(function(e) {
    e.preventDefault(); // Impede o envio padrão do formulário

    // Coleta os dados do formulário (incluindo o ID para editar ou null para criar)
    const formData = $(this).serialize(); 

    // Define o endpoint baseado na ação (criação ou edição, assumindo que a rota de edição é POST)
    // Se 'idBeneficio' estiver preenchido (hidden field no form), é edição, senão é criação.
    const idBeneficio = $('#idBeneficio').val(); 
    const endpoint = idBeneficio ? URL_BASE + '/beneficios/editar' : URL_BASE + '/beneficios/criar';

    $.post(endpoint, formData, function(res) {
        alert(res.mensagem);
        if(res.success) {
            // Fechar modal, recarregar lista, etc.
            modalBeneficio.hide(); 
            // Função para recarregar a tabela de benefícios do catálogo
            window.location.reload(); 
        }
    }, 'json').fail(function() {
        alert('Erro na comunicação ao salvar o benefício do catálogo.');
    });
});
});