$(document).ready(function() {

    // ============================================================
    // 1. INICIALIZAÇÃO DOS MODAIS (BOOTSTRAP 5)
    // ============================================================

    // Modal 1: Criar/Editar Benefício do Catálogo
    const modalBeneficioEl = document.getElementById('modalBeneficio');
    const modalBeneficio = new bootstrap.Modal(modalBeneficioEl);

    // Modal 2: Editar Regras Automáticas
    const modalRegrasEl = document.getElementById('modalRegras');
    const modalRegras = new bootstrap.Modal(modalRegrasEl);

    // Modal 3: Gerenciar Exceções (Colaborador Específico)
    const modalExcecoesEl = document.getElementById('modalExcecoes');
    const modalExcecoes = new bootstrap.Modal(modalExcecoesEl);

    // ============================================================
    // 2. VARIÁVEIS E CONSTANTES
    // ============================================================

    // URLs
    const URL_BASE_BENEFICIO = BASE_URL + '/beneficios';
    const URL_BASE_COLABORADOR = BASE_URL + '/colaborador';

    // Elementos do Modal Benefício
    const btnAdicionar = $('.btn-adicionar');
    const formBeneficio = document.getElementById('formBeneficio');
    const tituloModalBeneficio = $('#tituloModal');
    const tipoValorSelect = $('#tipoValorBeneficio');
    const valorFixoField = $('#valorFixoField');
    const descricaoField = $('#descricaoField');
    const valorFixoInput = $('#valorFixoBeneficio');
    const descricaoTextarea = $('#descricaoBeneficio');

    // Elementos da Seção de Exceções
    const inputPesquisar = $('#inputPesquisarColaborador');
    const resultadoPesquisa = $('#resultadoPesquisa');
    const listaBeneficiosColaborador = $('#listaBeneficiosColaborador'); // Dentro do Modal 3

    // ============================================================
    // 3. LÓGICA: CATÁLOGO DE BENEFÍCIOS
    // ============================================================

    // Abrir Modal "Novo"
    btnAdicionar.click(() => {
        tituloModalBeneficio.text('Novo Benefício');
        $('#beneficioId').val('');
        formBeneficio.reset();
        valorFixoField.hide();
        descricaoField.hide();
        modalBeneficio.show();
    });

    // Campos Condicionais (Fixo vs Variável)
    tipoValorSelect.on('change', () => {
        const valor = tipoValorSelect.val();
        valorFixoField.toggle(valor === 'Fixo');
        descricaoField.toggle(valor === 'Descritivo');

        if (valor !== 'Fixo') valorFixoInput.val('');
        if (valor !== 'Descritivo') descricaoTextarea.val('');
    });

    // Salvar Benefício
    $('#btnSalvarBeneficio').click(function() {
        const id = $('#beneficioId').val();
        const nome = $('#nomeBeneficio').val();
        const categoria = $('#categoriaBeneficio').val();
        const tipoValor = tipoValorSelect.val();

        if (!nome.trim() || !categoria || !tipoValor) {
            alert("Preencha Nome, Categoria e Tipo de Valor.");
            return;
        }

        const data = {
            id: id,
            nome: nome,
            categoria: categoria,
            tipo_valor: tipoValor,
            valor_fixo: valorFixoInput.val(),
            descricao: descricaoTextarea.val()
        };

        $.post(URL_BASE_BENEFICIO + '/salvar', data, function(res) {
            alert(res.mensagem);
            if(res.success) {
                modalBeneficio.hide();
                location.reload();
            }
        }, 'json').fail(function() {
            alert('Erro de comunicação ao salvar.');
        });
    });

    // Editar Benefício (Botão na Tabela)
    $('.editar').click(function(){
        const row = $(this).closest('tr');
        const id = row.data('id');
        const nome = row.find('td:eq(0)').text();
        const categoria = row.find('td:eq(1)').text();
        const tipoValorPuro = row.data('tipo-valor-puro');
        const valorFixo = row.data('valor-fixo');

        $('#beneficioId').val(id);
        $('#nomeBeneficio').val(nome);
        $('#categoriaBeneficio').val(categoria);

        tipoValorSelect.val(tipoValorPuro).trigger('change');

        if (tipoValorPuro === 'Fixo') {
            valorFixoInput.val(valorFixo);
        } else {
            valorFixoInput.val('');
            descricaoTextarea.val('');
        }

        tituloModalBeneficio.text('Editar Benefício');
        modalBeneficio.show();
    });

    // Toggle Status (Switch)
    $('.switch input[type="checkbox"]').change(function() {
        const isChecked = $(this).is(':checked');
        const row = $(this).closest('tr');
        const id = row.data('id');

        $.post(URL_BASE_BENEFICIO + '/toggleStatus', { id: id }, function(res) {
            if(res.success) {
                row.find('td:eq(3)').text(isChecked ? 'Ativo' : 'Inativo');
            } else {
                alert(res.mensagem);
                $(this).prop('checked', !isChecked);
            }
        }.bind(this), 'json').fail(function() {
            alert('Erro ao comunicar com o servidor.');
            $(this).prop('checked', !isChecked);
        }.bind(this));
    });

    // Deletar Benefício
    $('.deletar').click(function() {
        const row = $(this).closest('tr');
        const id = row.data('id');
        const nome = row.find('td:eq(0)').text();

        if (confirm(`ATENÇÃO: Deseja deletar "${nome}"?`)) {
            $.post(URL_BASE_BENEFICIO + '/deletar', { id: id }, function(res) {
                alert(res.mensagem);
                if(res.success) {
                    row.remove();
                }
            }, 'json');
        }
    });

    // ============================================================
    // 4. LÓGICA: REGRAS DE ATRIBUIÇÃO
    // ============================================================

    // Abrir Modal de Regras
    $(document).on('click', '.editar-regra', function() {
        const row = $(this).closest('tr');
        const tipoContrato = row.data('tipo-contrato');
        const idsString = row.attr('data-beneficios-ids');

        let idsAtribuidos = [];
        if (idsString && idsString.length > 0) {
            idsAtribuidos = idsString.split(',').map(id => parseInt(id.trim()));
        }

        $('#tituloRegrasModal').text('Regras: ' + tipoContrato);
        $('#contratoRegraAtual').val(tipoContrato);

        $('#listaBeneficiosRegra input[type="checkbox"]').each(function() {
            const idBeneficio = parseInt($(this).val());
            $(this).prop('checked', idsAtribuidos.includes(idBeneficio));
        });

        modalRegras.show();
    });

    // Salvar Regras
    $('#btnSalvarRegras').click(function() {
        const tipoContrato = $('#contratoRegraAtual').val();
        const idsSelecionados = [];

        $('#listaBeneficiosRegra input[type="checkbox"]:checked').each(function() {
            idsSelecionados.push($(this).val());
        });

        $.post(URL_BASE_BENEFICIO + '/regras/salvar', {
            tipo_contrato: tipoContrato,
            beneficios_ids: idsSelecionados
        }, function(res) {
            alert(res.mensagem);
            if (res.success) {
                modalRegras.hide();
                location.reload();
            }
        }, 'json');
    });

    // ============================================================
    // 5. LÓGICA: EXCEÇÕES DE COLABORADOR (NOVO POPUP)
    // ============================================================

    // Busca Dinâmica (Autocomplete)
    inputPesquisar.on('keyup', function() {
        const termo = $(this).val().trim();
        resultadoPesquisa.empty();

        if (termo.length < 3) { resultadoPesquisa.hide(); return; }

        $.post(URL_BASE_COLABORADOR + '/buscar', { termo: termo }, function(res) {
            if (res.success && res.data && res.data.length > 0) {
                let html = '<ul class="list-group list-group-flush">';
                res.data.forEach(colaborador => {
                    html += `<li class="list-group-item d-flex justify-content-between align-items-center" 
                                 style="cursor:pointer;">
                        <span>${colaborador.nome_completo} (${colaborador.matricula})</span>
                        <button class="btn btn-sm btn-outline-primary btn-edit-colaborador" 
                                data-id="${colaborador.id_colaborador}">
                            <i class="bi bi-pencil"></i>
                        </button>
                    </li>`;
                });
                html += '</ul>';
                resultadoPesquisa.html(html).show();
            } else {
                resultadoPesquisa.html('<div class="p-2 text-muted">Nenhum colaborador encontrado.</div>').show();
            }
        }, 'json');
    });

    // Clique no Lápis (Resultado da Busca) -> Abre o Modal
    $(document).on('click', '.btn-edit-colaborador', function() {
        const id = $(this).data('id');
        $('#resultadoPesquisa').hide();

        $.post(URL_BASE_COLABORADOR + '/beneficios/carregar', { id_colaborador: id }, function(res) {
            if (res.success) {
                // Preenche dados do cabeçalho do modal
                $('#colaboradorIdAtual').val(id);
                $('#nomeColaboradorEdicao').text(res.dados_colaborador.nome_completo);
                $('#matriculaColaborador').text(res.dados_colaborador.matricula);
                $('#contratoColaborador').text(res.dados_colaborador.tipo_contrato);

                // Reseta e marca os checkboxes dentro do modal
                listaBeneficiosColaborador.find('input[type="checkbox"]').prop('checked', false);

                if(res.beneficios_ids) {
                    res.beneficios_ids.forEach(id_b => {
                        listaBeneficiosColaborador.find(`input[value="${id_b}"]`).prop('checked', true);
                    });
                }

                inputPesquisar.val(''); // Limpa a busca

                // ABRE O MODAL BOOTSTRAP
                modalExcecoes.show();
            } else {
                alert("Erro ao carregar dados: " + res.mensagem);
            }
        }, 'json').fail(function() {
            alert("Erro de comunicação ao carregar colaborador.");
        });
    });

    // Botão Salvar (Dentro do Modal de Exceções)
    $('#btnSalvarExcecoes').click(function() {
        const id = $('#colaboradorIdAtual').val();
        const ids = [];

        // Coleta IDs marcados no modal
        listaBeneficiosColaborador.find('input:checked').each(function() {
            ids.push($(this).val());
        });

        $.post(URL_BASE_COLABORADOR + '/beneficios/salvar', {
            id_colaborador: id,
            beneficios_ids: ids
        }, function(res) {
            alert(res.mensagem);
            if(res.success) {
                modalExcecoes.hide(); // Fecha o modal
            }
        }, 'json').fail(function() {
            alert('Erro na comunicação ao salvar as exceções.');
        });
    });

    // Ocultar resultados da pesquisa ao clicar fora
    $(document).click(function(e) {
        if (!inputPesquisar.is(e.target) && !resultadoPesquisa.is(e.target) && resultadoPesquisa.has(e.target).length === 0) {
            resultadoPesquisa.hide();
        }
    });

});