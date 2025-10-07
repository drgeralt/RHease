<?php
session_start();

require_once __DIR__ . '/../../../config.php';

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
                beneficios_catalogo b ON bc.id_beneficio = b.id_beneficio";
    
    $res = $conn->query($sql);
    $beneficios = [];
    if($res && $res->num_rows > 0) {
        while($row = $res->fetch_assoc()) {
            $beneficios[] = $row;
        }
    }
    return $beneficios;
}

// Lista benefícios com ID e Nome para usar no modal de regras
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

// Função para listar regras
function listarRegras($conn) {
    $sql = "SELECT 
                rb.tipo_contrato, 
                GROUP_CONCAT(bc.nome SEPARATOR ', ') as nomes_beneficios,
                GROUP_CONCAT(bc.id_beneficio SEPARATOR ',') as ids_beneficios
            FROM regras_beneficios rb
            JOIN beneficios_catalogo bc ON rb.id_beneficio = bc.id_beneficio
            WHERE bc.status = 'Ativo' 
            GROUP BY rb.tipo_contrato";
            
    $res = $conn->query($sql);
    $regras = [];
    if($res && $res->num_rows > 0) {
        while($row = $res->fetch_assoc()) {
            $regras[$row['tipo_contrato']] = [
                'nomes' => explode(', ', $row['nomes_beneficios']),
                'ids' => explode(',', $row['ids_beneficios'])
            ];
        }
    }
    return $regras;
}

function buscarTiposDeContrato($conn) {
    $sql = "SELECT DISTINCT tipo_contrato FROM colaborador WHERE tipo_contrato IS NOT NULL AND tipo_contrato != '' ORDER BY tipo_contrato";
    $res = $conn->query($sql);
    $tipos = [];
    if($res) {
        while($row = $res->fetch_assoc()) {
            $tipos[] = $row['tipo_contrato'];
        }
    }
    return $tipos;
}
function aplicarRegrasPadrao($conn, $idColaborador) {
    if (empty($idColaborador)) {
        return false;
    }

    // --- 1. BUSCAR O TIPO DE CONTRATO (Consulta direta à tabela 'colaborador') ---
    $sqlTipo = "SELECT tipo_contrato FROM colaborador WHERE id_colaborador = ?";
    $stmtTipo = $conn->prepare($sqlTipo);
    $stmtTipo->bind_param('i', $idColaborador);
    $stmtTipo->execute();
    $resultTipo = $stmtTipo->get_result();
    
    if ($resultTipo->num_rows === 0) {
        // Se o colaborador não existe, lança um erro, pois o cadastro deveria ter ocorrido.
        throw new Exception("Colaborador ID {$idColaborador} não encontrado após cadastro.");
    }

    $tipoContrato = $resultTipo->fetch_assoc()['tipo_contrato'];
    $stmtTipo->close();
    
    if (empty($tipoContrato)) {
        // Se o tipo de contrato não estiver preenchido, não há regras para aplicar.
        return true; 
    }

    // --- 2. BUSCAR AS REGRAS PADRÃO NA TABELA 'regras_beneficios' ---
    $sqlRegras = "
        SELECT id_beneficio
        FROM regras_beneficios
        WHERE tipo_contrato = ?
    ";

    $stmtRegras = $conn->prepare($sqlRegras);
    $stmtRegras->bind_param('s', $tipoContrato);
    $stmtRegras->execute();
    $resultRegras = $stmtRegras->get_result();
    $regras = [];
    while($row = $resultRegras->fetch_assoc()) {
        $regras[] = $row['id_beneficio'];
    }
    $stmtRegras->close();

    // Se não houver regras para esse tipo de contrato no catálogo, encerra.
    if (empty($regras)) {
        return true; 
    }
    
    // --- 3. INSERIR OS BENEFÍCIOS NA TABELA 'colaborador_beneficio' ---
    $sqlInsert = "
        INSERT INTO colaborador_beneficio (id_colaborador, id_beneficio, valor_especifico)
        VALUES (?, ?, 0.0) 
    ";
    $stmtInsert = $conn->prepare($sqlInsert);

    foreach ($regras as $idBeneficio) {
        $idBeneficio = (int)$idBeneficio;
        // valor_especifico é inicializado como 0.0, pois é o valor inicial de desconto.
        $stmtInsert->bind_param('ii', $idColaborador, $idBeneficio);
        
        if (!$stmtInsert->execute()) {
            throw new Exception("Erro ao inserir benefício padrão: " . $stmtInsert->error);
        }
    }
    $stmtInsert->close();
    
    return true;
}

$beneficios = listarBeneficios($conn);
$beneficios_selecao = listarBeneficiosParaSelecao($conn);
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
                            <option value="" disabled hidden>Selecione</option>
                            <option value="Saúde">Saúde</option>
                            <option value="Alimentação">Alimentação</option>
                            <option value="Mobilidade">Mobilidade</option>
                            <option value="Bem-Estar">Bem-Estar</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tipo de Valor</label>
                        <select id="tipoValorBeneficio" required>
                            <option value="" disabled hidden>Selecione</option>
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
                            <th id="acoes_style">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($tiposContrato as $tipo):
                        $lista_nomes = isset($regras[$tipo]) ? implode(", ", $regras[$tipo]['nomes']) : 'Nenhum benefício padrão';
                        $lista_ids = isset($regras[$tipo]) ? implode(",", $regras[$tipo]['ids']) : '';
                        ?>
                        <tr data-tipo-contrato="<?= $tipo ?>" data-beneficios-ids="<?= $lista_ids ?>">
                            <td><?= htmlspecialchars($tipo) ?></td>
                            <td><?= htmlspecialchars($lista_nomes) ?></td>
                            <td style="text-align:center;">
                            <i class="bi bi-pencil-square editar-regra" 
                                 data-tipo-contrato="<?= htmlspecialchars($tipo) ?>" 
                                 data-beneficios-ids="<?= htmlspecialchars($lista_ids) ?>" 
                                 title="Editar Regras">
                            </i>
                             </td>
                        </tr>
         <?php endforeach; ?>
         </tbody>
                </table>
            </div>
        </div>

        <div class="secao-colaborador">
        <div class="header-tabela">
            <h2>Gerenciar Benefícios do Colaborador (Exceções)</h2>
        </div>

        <div class="tabela-container" style="padding: 20px;">
            <div class="form-group">
                <label for="inputPesquisarColaborador"><i class="bi bi-search"></i> Pesquisar colaborador:</label>
                <input type="text" id="inputPesquisarColaborador" placeholder="Digite o nome ou a matrícula do colaborador" style="margin-bottom: 10px;">
                <div id="resultadoPesquisa" style="border: 1px solid #ddd; max-height: 150px; overflow-y: auto; border-radius: 6px; display: none;">
                    </div>
            </div>
        </div>

        <div id="painelEdicaoColaborador" class="tabela-container" style="padding: 20px; margin-top: 30px; display: none;">
            
            <div class="modal-header" style="border-bottom: none; margin-bottom: 10px;">
                <h3 id="nomeColaboradorEdicao" style="font-size: 1.5em; color: #489D3B; margin: 0;"></h3>
            </div>
            
            <p class="text-info">
                Matrícula: <strong id="matriculaColaborador"></strong> | 
                Contrato Padrão: <strong id="contratoColaborador"></strong>
            </p>
            
            <hr>

            <form id="formBeneficiosColaborador">
                <input type="hidden" id="colaboradorIdAtual">
                
                <div class="form-group">
                    <label>Benefícios Atribuídos Manualmente (Marque/Desmarque para alterar):</label>
                    <div id="listaBeneficiosColaborador" style="border: 1px solid #ccc; padding: 15px; height: 300px; overflow-y: auto; background-color: #fcfcfc; border-radius: 8px;">
                        <?php 
                        $sql_catalogo = "SELECT id_beneficio, nome FROM beneficios_catalogo WHERE status = 'ativo'";
                        $result_catalogo = $conn->query($sql_catalogo);
                        
                        if ($result_catalogo && $result_catalogo->num_rows > 0):
                            while($b = $result_catalogo->fetch_assoc()): ?>
                                <label style="display: flex; align-items: center; margin-bottom: 5px; cursor: pointer;">
                                    <input type="checkbox" name="beneficios_selecionados_colaborador[]" value="<?= $b['id_beneficio'] ?>" style="margin-right: 8px;">
                                    <?= htmlspecialchars($b['nome']) ?>
                                </label>
                            <?php endwhile;
                        else: ?>
                            <p>Nenhum benefício ativo no catálogo.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-actions" style="justify-content: flex-start;">
                    <button type="button" class="btn-salvar" id="btnSalvarBeneficiosColaborador">Salvar Atribuição Manual</button>
                    <button type="button" class="btn-cancelar" id="btnCancelarEdicao">Cancelar</button>
                </div>
            </form>
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
<div id="painelEdicao" class="modal-excecoes" style="display: none;">
    <div class="modal-content-excecoes">
        <div class="modal-header-excecoes">
            <h3 id="tituloEdicaoColaborador">Gerenciar Benefícios - [Nome do Colaborador]</h3>
            <span class="close-btn-excecoes">&times;</span>
        </div>
        <div class="modal-body-excecoes">
            <input type="hidden" id="colaboradorIdAtual">
            <p><strong>Matrícula:</strong> <span id="colaboradorMatricula"></span> | <strong>Contrato:</strong> <span id="colaboradorTipoContrato"></span></p>

            <p class="mt-3">Selecione os benefícios que devem ser ativados manualmente (Exceção):</p>
            
            <div id="listaBeneficiosColaborador" class="lista-checkbox-excecoes">
                </div>
            
        </div>
        <div class="modal-footer-excecoes">
            <button class="btn btn-secondary" id="btnCancelarEdicao">Cancelar</button>
            <button class="btn btn-primary" id="btnSalvarExcecoes">Salvar Exceções</button>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Variáveis do MODAL DE BENEFÍCIO (Catálogo)
    const modalBeneficio = $('#modalBeneficio');
    const btnAdicionar = $('.btn-adicionar');
    const btnCancelarBeneficio = modalBeneficio.find('.btnFecharModal'); // Busca dentro do modal
    const btnCloseIconBeneficio = modalBeneficio.find('.fechar-modal-beneficio'); // Busca dentro do modal

    const formBeneficio = document.getElementById('formBeneficio');
    const tituloModalBeneficio = $('#tituloModal');
    const tipoValorSelect = $('#tipoValorBeneficio');
    const valorFixoField = $('#valorFixoField');
    const descricaoField = $('#descricaoField');
    const valorFixoInput = $('#valorFixoBeneficio');
    const descricaoTextarea = $('#descricaoBeneficio');

    // Variáveis da Regras de Atribuição
    const modalRegras = $('#modalRegras');
    const tipoContratoRegraSelect = $('#tipoContratoRegra');
    const regrasContainer = $('#regrasContainer');
    const listaBeneficiosRegra = $('#listaBeneficiosRegra');
    const btnSalvarRegras = $('#btnSalvarRegras');

    // Variáveis da TAB EXCEÇÕES (Busca Dinâmica e Painel Inline
    const inputPesquisar = $('#inputPesquisarColaborador'); // Input de busca
    const resultadoPesquisa = $('#resultadoPesquisa'); // Área de resultados

    const painelEdicaoColaborador = $('#painelEdicaoColaborador'); // Painel INLINE de Edição
    const colaboradorIdAtual = $('#colaboradorIdAtual');
    const nomeColaboradorEdicao = $('#nomeColaboradorEdicao');
    const matriculaColaborador = $('#matriculaColaborador');
    const contratoColaborador = $('#contratoColaborador');
    const listaBeneficiosColaborador = $('#listaBeneficiosColaborador');
    const btnSalvarExcecoes = $('#btnSalvarBeneficiosColaborador');
    const btnCancelarExcecoes = $('#btnCancelarEdicao'); // Botão do painel inline

    // --- FUNÇÕES DE CONTROLE DE MODAIS (MODAL CATÁLOGO)
    // Função de Fechar Modal Benefício
    const fecharModalBeneficio = () => {
        modalBeneficio.hide();
        if (formBeneficio) formBeneficio.reset(); // Verifica se o elemento existe
        valorFixoField.hide();
        descricaoField.hide();
    };

    // Função de Fechar Modal Regras
    const fecharModalRegras = () => {
        modalRegras.hide();
    };

    // Evento: Abrir Modal de Novo Benefício
    btnAdicionar.click(() => {
        tituloModalBeneficio.text('Novo Benefício');
        $('#beneficioId').val('');
        fecharModalBeneficio(); 
        modalBeneficio.css('display', 'flex'); 
    });

    // Eventos: Fechar Modal Benefício (usando .click para segurança)
    btnCancelarBeneficio.click(fecharModalBeneficio);
    btnCloseIconBeneficio.click(fecharModalBeneficio);

    // Eventos: Fechar Modal Regras (usando .click para segurança)
   $('.fechar-modal-regras').click(fecharModalRegras);

    // Fecha ao clicar no botão Cancelar
    $('.btnFecharModalRegras').click(fecharModalRegras);

    // Gerencia cliques fora dos modais 
    $(window).click((e) => { 
        if($(e.target).is('#modalBeneficio')) {
            fecharModalBeneficio(); 
        }
        if($(e.target).is('#modalRegras')) {
            fecharModalRegras();
        }
    });

    // --- LÓGICA DO MODAL DE BENEFÍCIO ---
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
        
        // >> CAPTURA OS VALORES ANTES DA VALIDAÇÃO
        const nome = $('#nomeBeneficio').val();
        const categoria = $('#categoriaBeneficio').val();
        const tipoValor = tipoValorSelect.val();
        
        if (!nome.trim()) {
            alert("O nome do benefício é obrigatório.");
            return;
        }
        if (!categoria) {
            alert("Por favor, selecione uma Categoria.");
            return;
        }
        if (!tipoValor) {
            alert("Por favor, selecione um Tipo de Valor.");
            return;
        }
        
        const data = {
            acao: id ? 'editar' : 'criar',
            id: id,
            nome: nome, 
            categoria: categoria, 
            tipo_valor: tipoValor, 
            valor_fixo: valorFixoInput.val(), 
            descricao: descricaoTextarea.val() 
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
        
        tipoValorSelect.val(tipoValorPuro).trigger('change');
        
        if (tipoValorPuro === 'Fixo') {
            valorFixoInput.val(valorFixo);
        } else {
            valorFixoInput.val('');
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
            acao: 'desativar', 
            id: id
        };
        
        $.post('acoes_beneficio.php', data, function(res) {
            if(res.success) {
                row.find('td:eq(3)').text(isChecked ? 'Ativo' : 'Inativo');
                location.reload(); 
            } else {
                alert(res.mensagem);
                $(this).prop('checked', !isChecked);
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
                acao: 'deletar', 
                id: id
            };

            $.post('acoes_beneficio.php', data, function(res) {
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
    const icon = $(this);
    
    // 1. LER O TIPO DE CONTRATO
    // O JS vai ler 'data-tipo-contrato' do PHP.
    const tipoContrato = icon.data('tipo-contrato'); 
    
    const row = icon.closest('tr');
    
    // 2. LER OS IDs DE BENEFÍCIOS
    const idsAtribuidosString = row.data('beneficios-ids');

// CORREÇÃO: Usa .map() para transformar em inteiro E .filter() para remover IDs que não são números válidos (NaN)
    let idsAtribuidos = [];
    if (idsAtribuidosString) {
        idsAtribuidos = idsAtribuidosString.split(',')
            .map(id => parseInt(id.trim())) // Mapeia e garante que remova espaços de cada ID
            .filter(id => !isNaN(id) && id > 0); // Remove NaN (valores inválidos) e IDs zero/negativos
}

    // 3. ATUALIZA O MODAL
    // Se o valor estiver undefined, o PHP não está enviando 'data-tipo-contrato'
    $('#tituloRegrasModal').text('Regras para Contrato: ' + (tipoContrato || 'Erro na Leitura')); 
    $('#contratoRegraAtual').val(tipoContrato);
    
    // Itera sobre o catálogo de benefícios e marca/desmarca
    $('#listaBeneficiosRegra input[type="checkbox"]').each(function() {
        const idBeneficio = parseInt($(this).val());
        // Marca o checkbox se o ID estiver na lista de IDs atribuídos
        const isChecked = idsAtribuidos.includes(idBeneficio);
        $(this).prop('checked', isChecked);
    });

    // 4. EXIBE O MODAL
    $('#modalRegras').css('display', 'flex'); // Assume que você está usando este método
});

// Evento de Fechar o Modal de Regras
    $('.close-btn-regras').click(function() {
        $('#modalRegras').hide();
    });


    // Salvar Regras
    $('#btnSalvarRegras').click(function() {
    const tipoContrato = $('#contratoRegraAtual').val(); 
    const idsSelecionados = [];

    // Coleta IDs dos benefícios marcados
    $('#listaBeneficiosRegra input[type="checkbox"]:checked').each(function() {
        idsSelecionados.push($(this).val());
    });

    const data = {
        acao: 'salvar_regras',
        tipo_contrato: tipoContrato,
        beneficios_ids: idsSelecionados
    };

    $.post('acoes_beneficio.php', data, function(res) {
        alert(res.mensagem);
        if (res.success) {
            location.reload(); // Atualiza a página para mostrar a regra salva
        }
    }, 'json').fail(function(jqXHR, textStatus, errorThrown) {
        alert('Erro ao salvar regras: ' + textStatus);
    });
});


    // Função para abrir o painel de edição
    function abrirPainelEdicao(id) {
        // 1. Limpa os checks antes de carregar
        listaBeneficiosColaborador.find('input[type="checkbox"]').prop('checked', false);
        
        // 2. Chama o AJAX para carregar dados e exceções
        $.post('acoes_beneficio.php', { 
            acao: 'carregar_beneficios_colaborador', 
            id_colaborador: id 
        }, function(res) {
            if (res.success) {
                const dados = res.dados_colaborador;
                const idsAtuais = res.beneficios_ids;

                // Preenche o cabeçalho
                colaboradorIdAtual.val(id);
                nomeColaboradorEdicao.text(dados.nome_completo);
                matriculaColaborador.text(dados.matricula);
                contratoColaborador.text(dados.tipo_contrato);

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
    }


    // Evento: Pesquisa Dinâmica
    inputPesquisar.on('keyup', function() {
        const termo = $(this).val().trim();
        resultadoPesquisa.empty();

        if (termo.length < 3) {
            resultadoPesquisa.hide();
            return;
        }

        $.post('acoes_beneficio.php', { acao: 'buscar_colaborador', termo: termo }, function(res) {
            if (res.colaboradores && res.colaboradores.length > 0) {
                let html = '<ul class="list-group list-group-flush">';
                res.colaboradores.forEach(colaborador => {
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
        
        // Oculta a lista de resultados da pesquisa
        $('#resultadoPesquisa').hide(); 
        
        // Chama a função para carregar os dados
        abrirPainelEdicao(id);
    });


    // Evento: Salvar Exceções Manuais
    btnSalvarExcecoes.click(function() {
        const id_colaborador = colaboradorIdAtual.val();
        const idsSelecionados = [];

        // Coleta IDs dos benefícios marcados (o que será salvo como exceção)
        listaBeneficiosColaborador.find('input[type="checkbox"]:checked').each(function() {
            idsSelecionados.push($(this).val());
        });

        const data = {
            acao: 'salvar_beneficios_colaborador', 
            id_colaborador: id_colaborador,
            beneficios_ids: idsSelecionados 
        };
        
        $.post('acoes_beneficio.php', data, function(res) {
            alert(res.mensagem);
            if(res.success) {
                painelEdicaoColaborador.hide(); // Oculta após salvar
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
    $(document).on('click', '.editar-regra', function() {
    const icon = $(this);
    const tipoContrato = icon.data('tipo-contrato');
    const row = icon.closest('tr');
    
    // Pega a lista de IDs de benefícios associados àquela regra, separada por vírgula
    const idsAtribuidos = idsAtribuidosString ? idsAtribuidosString.split(',').map(id => parseInt(id)) : [];

    // 1. Preenche o cabeçalho do modal e campo de contrato
    $('#tituloRegrasModal').text('Regras para Contrato: ' + tipoContrato);
    $('#tipoContratoRegra').val(tipoContrato); // Campo que guarda o tipo de contrato no modal
    
    // 2. Itera sobre o catálogo de benefícios e marca/desmarca
    $('#listaBeneficiosRegra input[type="checkbox"]').each(function() {
        const idBeneficio = parseInt($(this).val());
        // Marca o checkbox se o ID estiver na lista de IDs atribuídos
        const isChecked = idsAtribuidos.includes(idBeneficio);
        $(this).prop('checked', isChecked);
    });

    // 3. Exibe o modal
    $('#modalRegras').show();
});

// Evento de Fechar o Modal de Regras
    $('.fechar-modal-regras').click(function() {
        $('#modalRegras').hide();
    });
});
</script>
</body>
</html>