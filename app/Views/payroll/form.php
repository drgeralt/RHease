<?php require_once BASE_PATH . '/app/Views/templates/header.php'; ?>

<h1>Adicionar Novo Registro de Folha de Pagamento</h1>
<form action="<?= BASE_URL ?>/payroll" method="POST">
    <div class="form-group">
        <label for="name">Nome:</label>
        <input type="text" id="name" name="name" required>
    </div>
    <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
    </div>
    <div class="form-group">
        <label for="position">Cargo:</label>
        <input type="text" id="position" name="position" required>
    </div>
    <div class="form-group">
        <label for="salary">Salário:</label>
        <input type="number" id="salary" name="salary" step="0.01" required>
    </div>
    <button type="submit" class="btn btn-primary">Salvar Registro</button>
</form>

<?php require_once BASE_PATH . '/app/Views/templates/footer.php'; ?>
