<?php require_once BASE_PATH . '/app/Views/templates/header.php'; ?>

<h2> Criar Novo Benefício </h2>

<form action="<?= BASE_URL ?>/beneficios" method="POST">
    <div class="form-group">
        <label for="nome_beneficio"> Nome do Benefício </label>
        <input type="text" id="nome_beneficio" name="nome_beneficio" required>
    </div>
    <div class="form-group">
        <label for="descricao"> Descrição </label>
        <textarea id="descricao" name="descricao" rows="3"></textarea>
    </div>
    <div class="form-group">
        <label for="valor_mensal"> Valor Mensal (Ex: R$150,50) </label>
        <input type="text" id="valor_mensal" name="valor_mensal" required>
    </div>
    <div class="form-group">
        <label for="tipo"> Tipo </label>
        <select id="tipo" name="tipo" required>
            <option value="desconto"> Desconto (Ex: Vale Transporte) </option>
            <option value="acréscimo"> Acréscimo (Ex: Custo Plano de Saúde) </option>
        </select>
    </div>
    <button type="submit" class="btn"> Salvar </button>
</form>

<?php require_once BASE_PATH . '/app/Views/templates/footer.php'; ?>