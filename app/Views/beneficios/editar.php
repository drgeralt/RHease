<?php require_once BASE_PATH . '/app/Views/templates/header.php'; ?>

<h2> Editar Benefício: <?= htmlspecialchars($data['beneficio']['nome_beneficio']) ?> </h2>

<form action="<?= BASE_URL ?>/beneficios/editar/<?= $data['beneficio']['id'] ?>" method="POST">
    <div class="form-group">
        <label for="nome_beneficio"> Nome do Benefício </label>
        <input type="text" id="nome_beneficio" name="nome_beneficio" value="<?= htmlspecialchars($data['beneficio']['nome_beneficio']) ?>" required>
    </div>
    <div class="form-group">
        <label for="descricao"> Descrição </label>
        <textarea id="descricao" name="descricao" rows="3"><?= htmlspecialchars($data['beneficio']['descricao']) ?></textarea>
    </div>
    <div class="form-group">
        <label for="valor_mensal"> Valor Mensal (Ex: R$ 150,50) </label>
        <input type="text" id="valor_mensal" name="valor_mensal" value="<?= htmlspecialchars($data['beneficio']['valor_mensal']) ?>" required>
    </div>
    <div class="form-group">
        <label for="tipo"> Tipo </label>
        <select id="tipo" name="tipo" required>
            <option value="desconto" <?= $data['beneficio']['tipo'] == 'desconto' ? 'selected' : '' ?>> Desconto (Ex: Vale Transporte) </option>
            <option value="acréscimo" <?= $data['beneficio']['tipo'] == 'acrescimo' ? 'selected' : '' ?>> Acréscimo (Ex: Custo Plano de Saúde) </option>
        </select>
    </div>
    <button type="submit" class="btn"> Atualizar </button>
</form>

<?php require_once BASE_PATH . '/app/Views/templates/footer.php'; ?>