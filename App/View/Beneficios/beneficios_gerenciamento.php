<?php
// ... (Seu código PHP para formatação de valor e cabeçalho, que deve ser leve)

// Função para formatar o valor
function formatarValor($valor) {
    if (is_numeric($valor) && $valor > 0) {
        return 'R$' . number_format((float)$valor, 2, ',', '.');
    }
    return '';
}
if (!isset($beneficios)) $beneficios = [];
if (!isset($beneficios_selecao)) $beneficios_selecao = [];
if (!isset($regras)) $regras = [];
if (!isset($tiposContrato)) $tiposContrato = ["CLT", "PJ", "Estágio", "Temporário"];

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerenciamento de Benefícios</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <link rel="stylesheet" href="/RHease/public/css/beneficiostyle.css">
    
</head>
<body>
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
                        <td><?= htmlspecialchars($b['nome']) ?></td>
                        <td><?= htmlspecialchars($b['categoria']) ?></td>
                        <td><?= htmlspecialchars($tipo_valor_exibicao) ?></td>
                        <td><?= htmlspecialchars($b['status']) ?></td>
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
                    <span class="fechar-modal-beneficio close-btn">&times;</span>
                </div>
                <form id="formBeneficio">
                    <input type="hidden" id="beneficioId">
                    <div class="form-group">
                        <label for="nomeBeneficio">Nome do Benefício <span class="required">*</span></label>
                        <input type="text" id="nomeBeneficio" required>
                    </div>
                    <div class="form-group">
                        <label for="categoriaBeneficio">Categoria <span class="required">*</span></label>
                        <select id="categoriaBeneficio" required>
                            <option value="">Selecione</option>
                            <option value="Alimentação">Alimentação</option>
                            <option value="Transporte">Transporte</option>
                            <option value="Saúde">Saúde</option>
                            <option value="Educação">Educação</option>
                            <option value="Outros">Outros</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="tipoValorBeneficio">Tipo de Valor <span class="required">*</span></label>
                        <select id="tipoValorBeneficio" required>
                            <option value="">Selecione</option>
                            <option value="Fixo">Fixo</option>
                            <option value="Variável">Variável</option>
                            <option value="Descritivo">Descritivo (Sem Valor)</option>
                        </select>
                    </div>
                    <div id="valorFixoField" class="form-group" style="display: none;">
                        <label for="valorFixoBeneficio">Valor Fixo (R$) <span class="required">*</span></label>
                        <input type="number" id="valorFixoBeneficio" step="0.01" min="0">
                    </div>
                    <div id="descricaoField" class="form-group" style="display: none;">
                        <label for="descricaoBeneficio">Descrição (Opcional)</label>
                        <textarea id="descricaoBeneficio"></textarea>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-salvar" id="btnSalvarBeneficio">Salvar</button>
                        <button type="button" class="btn-cancelar btnFecharModal">Cancelar</button>
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
                        $regra_data = $regras[$tipo] ?? null;
                        $lista_nomes = $regra_data ? implode(", ", $regra_data['nomes']) : 'Nenhum benefício padrão';
                        $lista_ids = $regra_data ? implode(",", $regra_data['ids']) : '';
                        ?>
                        <tr data-tipo-contrato="<?= htmlspecialchars($tipo) ?>" data-beneficios-ids="<?= htmlspecialchars($lista_ids) ?>">
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
            <h2>Gerenciar Exceções de Colaborador</h2>
            <div class="form-group busca-colaborador">
                <label for="inputPesquisarColaborador">Buscar Colaborador (Nome ou Matrícula):</label>
                <input type="text" id="inputPesquisarColaborador" placeholder="Digite no mínimo 3 caracteres...">
            </div>
            <div id="resultadoPesquisa" class="list-group" style="display: none; position: absolute; z-index: 10; width: 300px; background-color: white; border: 1px solid #ccc; max-height: 200px; overflow-y: auto;">
                </div>
            
             <div id="painelEdicaoColaborador" class="tabela-container" style="padding: 20px; margin-top: 30px; display: none;">
                <div class="colaborador-info">
                    <p><strong>Colaborador:</strong> <span id="nomeColaboradorEdicao"></span></p>
                    <p><strong>Matrícula:</strong> <span id="matriculaColaborador"></span></p>
                    <p><strong>Contrato:</strong> <span id="contratoColaborador"></span></p>
                    <hr>
                    <p style="font-style: italic; color: #dc3545;">* Marque os benefícios para ATRIBUIR MANUALMENTE (Exceção) ou desmarque para REMOVER manualmente.</p>
                </div>
                <form id="formBeneficiosColaborador">
                    <input type="hidden" id="colaboradorIdAtual">
                    
                    <div class="form-group">
                        <label>Benefícios Atribuídos Manualmente:</label>
                        <div id="listaBeneficiosColaborador" style="border: 1px solid #ccc; padding: 15px; height: 300px; overflow-y: auto; background-color: #fcfcfc; border-radius: 8px;">
                            <?php foreach($beneficios_selecao as $b): ?>
                                <label style="display: flex; align-items: center; margin-bottom: 5px; cursor: pointer;">
                                    <input type="checkbox" name="beneficios_selecionados_colaborador[]" value="<?= $b['id_beneficio'] ?>" style="margin-right: 8px;">
                                    <?= htmlspecialchars($b['nome']) ?>
                                </label>
                            <?php endforeach; ?>
                            <?php if (empty($beneficios_selecao)): ?>
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
                    <h3 id="tituloRegrasModal">Regras de Atribuição</h3>
                    <span class="fechar-modal-regras close-btn">&times;</span>
                </div>
                <form id="formRegras">
                    <div class="form-group">
                        <label>Tipo de Contrato:</label>
                        <input type="text" id="contratoRegraAtual" readonly style="background-color: #f5f5f5;">
                    </div>
                    <div class="form-group">
                        <label>Selecione os Benefícios Padrão:</label>
                        <div id="listaBeneficiosRegra" style="border: 1px solid #ccc; padding: 10px; max-height: 200px; overflow-y: auto;">
                            <?php foreach($beneficios_selecao as $b): ?>
                                <label style="display: block; margin-bottom: 5px;">
                                    <input type="checkbox" name="beneficios_selecionados[]" value="<?= $b['id_beneficio'] ?>">
                                    <?= htmlspecialchars($b['nome']) ?>
                                </label>
                            <?php endforeach; ?>
                            <?php if (empty($beneficios_selecao)): ?>
                                <p>Nenhum benefício ativo no catálogo.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-salvar" id="btnSalvarRegras">Salvar Regras</button>
                        <button type="button" class="btn-cancelar btnFecharModalRegras">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="/RHease/public/js/beneficios.js"></script>
</body>
</html>