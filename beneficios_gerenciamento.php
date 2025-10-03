<?php
// beneficios_gerenciamento.php
session_start();

// Inclui as configs do projeto para BASE_URL, se existir
require_once __DIR__ . '/config.php';

// Configurações do banco
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'rhease';

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) die("Erro ao conectar: " . $conn->connect_error);

// Função para formatar o valor (usada na exibição da tabela)
function formatarValor($valor) {
    if (is_numeric($valor) && $valor > 0) {
        return 'R$' . number_format((float)$valor, 2, ',', '.');
    }
    return '';
}

// FUNÇÃO CORRIGIDA: A coluna 'descricao' foi removida da query.
// Lista todos os benefícios para o catálogo (exibição)
function listarBeneficios($conn) {
    $sql = "SELECT 
                bc.id_beneficio, 
                bc.nome, 
                bc.categoria, 
                bc.tipo_valor, 
                b.custo_padrao_empresa AS valor_fixo, 
                bc.status
            FROM 
                beneficios_catalogo bc
            LEFT JOIN 
                beneficio b ON bc.id_beneficio = b.id_beneficio";
    
    $res = $conn->query($sql);
    $beneficios = [];
    if($res && $res->num_rows > 0) {
        while($row = $res->fetch_assoc()) {
            $beneficios[] = $row;
        }
    }
    return $beneficios;
}

// NOVA FUNÇÃO: Lista benefícios com ID e Nome para usar no modal de regras
function listarBeneficiosParaSelecao($conn) {
    $sql = "SELECT id_beneficio, nome FROM beneficios_catalogo WHERE status = 'Ativo' ORDER BY nome";
    $res = $conn->query($sql);
    $lista = [];
    if($res && $res->num_rows > 0) {
        while($row = $res->fetch_assoc()) {
            $lista[] = $row;
        }
    }
    return $lista;
}


// Função para listar regras (Mantida, mas busca o ID do benefício)
function listarRegras($conn) {
    // Buscar o ID e o Nome do benefício
    $sql = "SELECT 
                rb.tipo_contrato, 
                GROUP_CONCAT(bc.nome SEPARATOR ', ') as nomes_beneficios,
                GROUP_CONCAT(bc.id_beneficio SEPARATOR ',') as ids_beneficios
            FROM regras_beneficios rb
            JOIN beneficios_catalogo bc ON rb.id_beneficio = bc.id_beneficio
            GROUP BY rb.tipo_contrato";
    $res = $conn->query($sql);
    $regras = [];
    if($res && $res->num_rows > 0) {
        while($row = $res->fetch_assoc()) {
            // Armazena a lista de IDs para o JavaScript usar na edição
            $regras[$row['tipo_contrato']] = [
                'nomes' => explode(', ', $row['nomes_beneficios']),
                'ids' => explode(',', $row['ids_beneficios'])
            ];
        }
    }
    return $regras;
}

// Carrega dados
$beneficios = listarBeneficios($conn);
$beneficios_selecao = listarBeneficiosParaSelecao($conn); // Novo array de benefícios ativos
$regras = listarRegras($conn);
$tiposContrato = ["CLT", "PJ", "Estágio", "Temporário"];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Benefícios - RH Ease</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= BASE_URL ?>/css/beneficiostyle.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
<header>
    <i class="bi bi-list menu-toggle"></i>
    <img id="logo" src="<?= BASE_URL ?>/img/rhease-ease 1.png" alt="RHease" width="130">
</header>
<div class="container">
    <div class="sidebar">
        <ul class="menu">
            <li><a href="index.html"><i class="bi bi-clipboard-data-fill"></i> Painel</a></li>
            <li><a href="dados.html"><i class="bi bi-person-vcard-fill"></i> Dados Cadastrais</a></li>
            <li><a href="frequencia.html"><i class="bi bi-calendar2-check-fill"></i> Frequência</a></li>
            <li><a href="salario.html"><i class="bi bi-wallet-fill"></i> Salário</a></li>
            <li class="active"><a href="#"><i class="bi bi-shield-fill-check"></i> Benefícios</a></li>
            <li><a href="contato.html"><i class="bi bi-person-lines-fill"></i> Contato</a></li>
        </ul>
    </div>

    <div class="content">
        <div class="header-tabela">
            <h2>Catálogo de Benefícios</h2>
            <button class="btn-adicionar"><i class="bi bi-plus-lg"></i> Adicionar Benefício</button>
        </div>

        <div class="tabela-container">
            <table>
                <thead>
                    <tr>
                        <th>Benefício</th>
                        <th>Categoria</th>
                        <th>Tipo de Valor</th>
                        <th>Status</th>
                        <th class="col-acoes">Ações</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($beneficios as $b): 
                    $tipo_valor_exibicao = $b['tipo_valor'];
                    
                    if (!empty($b['valor_fixo']) && $b['tipo_valor'] === 'Fixo') {
                         $tipo_valor_exibicao = "Fixo (" . formatarValor($b['valor_fixo']) . ")";
                    }
                    ?>
                    <tr data-id="<?= $b['id_beneficio'] ?>" 
                        data-tipo-valor-puro="<?= $b['tipo_valor'] ?>"
                        data-valor-fixo="<?= $b['valor_fixo'] ?? '' ?>">
                        <td><?= $b['nome'] ?></td>
                        <td><?= $b['categoria'] ?></td>
                        <td><?= $tipo_valor_exibicao ?></td>
                        <td><?= $b['status'] ?></td>
                        <td class="acoes">
                            <i class="bi bi-pencil-square editar"></i>
                            <i class="bi bi-trash3-fill deletar"></i>
                            <label class="switch">
                                <input type="checkbox" <?= $b['status'] === 'Ativo' ? 'checked' : '' ?>>
                                <span class="slider round"></span>
                            </label>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div id="modalBeneficio" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 id="tituloModal">Novo Benefício</h3>
                    <span class="close-btn fechar-modal-beneficio">&times;</span> 
                </div>
                <form id="formBeneficio">
                    <input type="hidden" id="beneficioId">
                    <div class="form-group">
                        <label>Nome</label>
                        <input type="text" id="nomeBeneficio" required>
                    </div>
                    <div class="form-group">
                        <label>Categoria</label>
                        <select id="categoriaBeneficio" required>
                            <option value="">Selecione</option>
                            <option value="Saúde">Saúde</option>
                            <option value="Alimentação">Alimentação</option>
                            <option value="Mobilidade">Mobilidade</option>
                            <option value="Bem-Estar">Bem-Estar</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tipo de Valor</label>
                        <select id="tipoValorBeneficio" required>
                            <option value="">Selecione</option>
                            <option value="Fixo">Fixo</option>
                            <option value="Variável">Variável</option>
                            <option value="Descritivo">Descritivo</option>
                        </select>
                    </div>
                    <div class="form-group" id="valorFixoField" style="display:none;">
                        <label>Valor (R$)</label>
                        <input type="number" id="valorFixoBeneficio" step="0.01">
                    </div>
                    <div class="form-group" id="descricaoField" style="display:none;">
                        <label>Descrição (Obs: Esta descrição **não será salva** no Catálogo)</label>
                        <textarea id="descricaoBeneficio"></textarea>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-cancelar btnFecharModal">Cancelar</button>
                        <button type="button" class="btn-salvar" id="btnSalvarBeneficio">Salvar</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="secao-regras">
            <h2>Regras de Atribuição Automática</h2>
            <div class="tabela-container regras-container">
                <table class="regras-tabela">
                    <thead>
                        <tr>
                            <th>Tipo de Contrato</th>
                            <th>Benefícios Padrão</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($tiposContrato as $tipo): 
                        $lista_nomes = isset($regras[$tipo]) ? implode(", ", $regras[$tipo]['nomes']) : 'Nenhum benefício padrão';
                        $lista_ids = isset($regras[$tipo]) ? implode(",", $regras[$tipo]['ids']) : '';
                        ?>
                        <tr data-contrato="<?= $tipo ?>" data-beneficios-ids="<?= $lista_ids ?>">
                            <td><?= $tipo ?></td>
                            <td class="beneficios-lista-regra"><?= $lista_nomes ?></td>
                            <td style="text-align:center;"><i class="bi bi-pencil-square editar-regra" data-contrato="<?= $tipo ?>"></i></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="modalRegras" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 id="tituloRegrasModal">Editar Regras para [TIPO CONTRATO]</h3>
                    <span class="close-btn fechar-modal-regras">&times;</span>
                </div>
                <form id="formRegras">
                    <input type="hidden" id="contratoRegraAtual">
                    <div class="form-group">
                        <label>Selecione os Benefícios Padrão:</label>
                        <div id="listaBeneficiosRegra">
                            <?php foreach($beneficios_selecao as $b): ?>
                                <label>
                                    <input type="checkbox" name="beneficios_selecionados[]" value="<?= $b['id_beneficio'] ?>">
                                    <?= htmlspecialchars($b['nome']) ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-cancelar btnFecharModalRegras">Cancelar</button>
                        <button type="button" class="btn-salvar" id="btnSalvarRegras">Salvar Regras</button>
                    </div>
                </form>
            </div>
        </div>


    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// Variáveis do Modal de Benefício
const modalBeneficio = document.getElementById('modalBeneficio');
const btnAdicionar = document.querySelector('.btn-adicionar');
const btnCancelarBeneficio = document.querySelector('.btnFecharModal');
const btnCloseIconBeneficio = document.querySelector('.fechar-modal-beneficio');

const formBeneficio = document.getElementById('formBeneficio');
const tituloModalBeneficio = document.getElementById('tituloModal');
const tipoValorSelect = document.getElementById('tipoValorBeneficio');
const valorFixoField = document.getElementById('valorFixoField');
const descricaoField = document.getElementById('descricaoField');
const valorFixoInput = document.getElementById('valorFixoBeneficio');
const descricaoTextarea = document.getElementById('descricaoBeneficio');

// Variáveis do NOVO Modal de Regras
const modalRegras = document.getElementById('modalRegras');
const btnCloseIconRegras = document.querySelector('.fechar-modal-regras');
const btnCancelarRegras = document.querySelector('.btnFecharModalRegras');
const tituloRegrasModal = document.getElementById('tituloRegrasModal');
const contratoRegraAtual = document.getElementById('contratoRegraAtual');
const listaBeneficiosRegra = document.getElementById('listaBeneficiosRegra');


// --- FUNÇÕES DE CONTROLE DE MODAIS ---

// Função de Fechar Modal Benefício
const fecharModalBeneficio = () => {
    modalBeneficio.style.display = 'none';
    formBeneficio.reset();
    valorFixoField.style.display = 'none';
    descricaoField.style.display = 'none';
};

btnAdicionar.onclick = () => {
    tituloModalBeneficio.textContent = 'Novo Benefício';
    formBeneficio.reset();
    fecharModalBeneficio(); 
    modalBeneficio.style.display = 'flex';
};

btnCancelarBeneficio.onclick = fecharModalBeneficio;
btnCloseIconBeneficio.onclick = fecharModalBeneficio;


// Função de Fechar Modal Regras
const fecharModalRegras = () => {
    modalRegras.style.display = 'none';
};

btnCancelarRegras.onclick = fecharModalRegras;
btnCloseIconRegras.onclick = fecharModalRegras;


// Gerencia cliques fora dos modais
window.onclick = (e) => { 
    if(e.target == modalBeneficio) {
        fecharModalBeneficio(); 
    }
    if(e.target == modalRegras) {
        fecharModalRegras();
    }
};

// --- LÓGICA DO MODAL DE BENEFÍCIO ---

tipoValorSelect.onchange = () => {
    const valor = tipoValorSelect.value;
    valorFixoField.style.display = valor === 'Fixo' ? 'block' : 'none';
    descricaoField.style.display = valor === 'Descritivo' ? 'block' : 'none';

    if (valor !== 'Fixo') valorFixoInput.value = '';
    if (valor !== 'Descritivo') descricaoTextarea.value = '';
};


// Salvar Benefício via AJAX
$('#btnSalvarBeneficio').click(function() {
    const id = $('#beneficioId').val();
    const data = {
        acao: id ? 'editar' : 'criar',
        id: id,
        nome: $('#nomeBeneficio').val(),
        categoria: $('#categoriaBeneficio').val(),
        tipo_valor: tipoValorSelect.value, 
        valor_fixo: valorFixoInput.value, 
        descricao: descricaoTextarea.value 
    };
    
    // Validação básica
    if (data.tipo_valor === 'Fixo' && (!data.valor_fixo || isNaN(parseFloat(data.valor_fixo)))) {
        alert("Por favor, insira um valor fixo válido.");
        return;
    }

    $.post('acoes_beneficio.php', data, function(res) {
        alert(res.mensagem);
        if(res.success) location.reload();
    }, 'json').fail(function(jqXHR, textStatus, errorThrown) {
        alert('Erro na comunicação com o servidor: ' + textStatus);
    });
});

// Editar benefício
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
    
    tipoValorSelect.value = tipoValorPuro;
    tipoValorSelect.dispatchEvent(new Event('change'));
    
    if (tipoValorPuro === 'Fixo') {
        valorFixoInput.value = valorFixo;
    } else {
        valorFixoInput.value = '';
        descricaoTextarea.value = '';
    }

    tituloModalBeneficio.textContent = 'Editar Benefício';
    modalBeneficio.style.display = 'flex';
});

// Implementação do Switch (Toggle) para status
$('.switch input[type="checkbox"]').change(function() {
    const isChecked = $(this).is(':checked');
    const row = $(this).closest('tr');
    const id = row.data('id');
    
    const data = {
        acao: 'desativar', 
        id: id
    };
    
    $.post('acoes_beneficio.php', data, function(res) {
        if(res.success) {
            row.find('td:eq(3)').text(isChecked ? 'Ativo' : 'Inativo');
        } else {
            alert(res.mensagem);
            $(this).prop('checked', !isChecked);
        }
    }.bind(this), 'json').fail(function() {
        alert('Erro ao comunicar com o servidor para mudar status.');
        $(this).prop('checked', !isChecked); 
    }.bind(this));
});


// --- LÓGICA DO NOVO MODAL DE REGRAS ---

$('.editar-regra').click(function() {
    const tipoContrato = $(this).closest('tr').data('contrato');
    const idsAtuais = $(this).closest('tr').data('beneficios-ids').toString().split(',').map(id => id.trim()).filter(id => id); 
    
    tituloRegrasModal.textContent = `Editar Regras para ${tipoContrato}`;
    contratoRegraAtual.value = tipoContrato;
    
    // Desmarca todos os checkboxes
    $('#listaBeneficiosRegra input[type="checkbox"]').prop('checked', false);
    
    // Marca os checkboxes dos benefícios que já estão associados
    idsAtuais.forEach(id => {
        if(id) {
            $(`#listaBeneficiosRegra input[value="${id}"]`).prop('checked', true);
        }
    });

    modalRegras.style.display = 'flex';
});


// Salvar Regras via AJAX (AGORA APONTA PARA acoes_beneficio.php)
$('#btnSalvarRegras').click(function() {
    const tipoContrato = contratoRegraAtual.value;
    const idsSelecionados = [];

    // Coleta IDs dos benefícios marcados
    $('#listaBeneficiosRegra input[type="checkbox"]:checked').each(function() {
        idsSelecionados.push($(this).val());
    });

    const data = {
        // Nova ação para o arquivo unificado
        acao: 'salvar_regras', 
        tipo_contrato: tipoContrato,
        beneficios_ids: idsSelecionados 
    };
    
    // O POST agora vai para acoes_beneficio.php
    $.post('acoes_beneficio.php', data, function(res) {
        alert(res.mensagem);
        if(res.success) location.reload();
    }, 'json').fail(function(jqXHR, textStatus, errorThrown) {
        alert('Erro na comunicação com o servidor: ' + textStatus);
    });
});

</script>
</body>
</html>