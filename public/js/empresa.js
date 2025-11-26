const modalEmpresas = new bootstrap.Modal(document.getElementById('modalEmpresas'));

function carregarEmpresas() {
    $.get(BASE_URL + '/api/empresas/listar', function(res) {
        if(res.success) {
            let html = '';
            let nomeAtiva = 'Selecione';

            res.lista.forEach(emp => {
                const isActive = emp.id_empresa == res.ativa_id;
                if(isActive) nomeAtiva = emp.razao_social;

                const activeClass = isActive ? 'active bg-success text-white' : 'list-group-item-action';

                html += `
                <div class="d-flex justify-content-between align-items-center list-group-item ${activeClass}">
                    <div onclick="trocarEmpresa(${emp.id_empresa})" style="cursor:pointer; flex:1;">
                        <strong>${emp.razao_social}</strong><br>
                        <small>${emp.cnpj}</small>
                    </div>
                    <button class="btn btn-sm btn-light text-dark" onclick="editarEmpresa(${emp.id_empresa}, '${emp.razao_social}', '${emp.cnpj}', '${emp.endereco}', '${emp.cidade_uf}')">
                        <i class="bi bi-pencil"></i>
                    </button>
                </div>`;
            });

            $('#listaEmpresas').html(html);
            $('#nomeEmpresaAtiva').text(nomeAtiva); // Atualiza o Header
        }
    }, 'json');
}

function trocarEmpresa(id) {
    $.post(BASE_URL + '/api/empresas/trocar', {id: id}, function() {
        location.reload(); // Recarrega para atualizar o sistema com a nova empresa
    }, 'json');
}

function editarEmpresa(id, rs, cnpj, end, cid) {
    $('#empresaId').val(id);
    $('input[name="razao_social"]').val(rs);
    $('input[name="cnpj"]').val(cnpj);
    $('input[name="endereco"]').val(end);
    $('input[name="cidade_uf"]').val(cid);
}

function limparFormEmpresa() {
    $('#formEmpresa')[0].reset();
    $('#empresaId').val('');
}

function abrirModalEmpresas() {
    carregarEmpresas();
    modalEmpresas.show();
}

$('#formEmpresa').submit(function(e){
    e.preventDefault();
    $.post(BASE_URL + '/api/empresas/salvar', $(this).serialize(), function(res){
        alert(res.message);
        carregarEmpresas();
        limparFormEmpresa();
    }, 'json');
});

// Carrega o nome no header ao iniciar
$(document).ready(function() { carregarEmpresas(); });