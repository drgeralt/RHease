$(document).ready(function () {
    const modal = $('#modalBeneficio');
    const form = $('#formBeneficio');
    const titulo = $('#tituloModal');
    const campoValor = $('#valorFixoBeneficio');
    const tipoValor = $('#tipoValorBeneficio');

    // === Abrir Modal de Criação ===
    $('#btnNovoBeneficio').click(() => {
        form[0].reset();
        $('#beneficioId').val('');
        titulo.text('Cadastrar Benefício');
        modal.fadeIn(200);
    });

    // === Fechar Modal ===
    $('.fecharModal').click(() => modal.fadeOut(200));

    // === Exibir campo de valor fixo ===
    tipoValor.on('change', function () {
        if ($(this).val() === 'Fixo') campoValor.parent().show();
        else campoValor.parent().hide();
    }).trigger('change');

    // === Criar / Editar Benefício ===
    form.submit(function (e) {
        e.preventDefault();
        const dados = form.serializeArray();
        const id = $('#beneficioId').val();
        dados.push({ name: 'acao', value: id ? 'editar' : 'criar' });

        $.post('/beneficios/api', dados, function (res) {
            alert(res.mensagem);
            if (res.success) location.reload();
        }, 'json').fail(() => alert('Erro no servidor.'));
    });

    // === Editar Benefício ===
    $('.editar').click(function () {
        const row = $(this).closest('tr');
        $('#beneficioId').val(row.data('id'));
        $('#nomeBeneficio').val(row.find('td:eq(0)').text());
        $('#categoriaBeneficio').val(row.find('td:eq(1)').text());
        $('#tipoValorBeneficio').val(row.find('td:eq(2)').text()).trigger('change');
        $('#valorFixoBeneficio').val(row.data('valor'));
        titulo.text('Editar Benefício');
        modal.fadeIn(200);
    });

    // === Deletar Benefício ===
    $('.deletar').click(function () {
        if (!confirm('Deseja realmente excluir este benefício?')) return;
        const id = $(this).closest('tr').data('id');

        $.post('/beneficios/api', { acao: 'deletar', id }, function (res) {
            alert(res.mensagem);
            if (res.success) location.reload();
        }, 'json');
    });

    // === Alternar Status ===
    $('.switch input').change(function () {
        const id = $(this).closest('tr').data('id');
        $.post('/beneficios/api', { acao: 'status', id }, function (res) {
            console.log(res.mensagem);
        }, 'json');
    });

    // === Salvar Regras Automáticas ===
    $('#formRegras').submit(function (e) {
        e.preventDefault();
        const tipo = $('#tipoContrato').val();
        const ids = [];
        $('input[name="regras[]"]:checked').each(function () {
            ids.push($(this).val());
        });

        $.post('/beneficios/api', { acao: 'salvarRegras', tipo_contrato: tipo, ids }, function (res) {
            alert(res.mensagem);
            if (res.success) location.reload();
        }, 'json');
    });
});
