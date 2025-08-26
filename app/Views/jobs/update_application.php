<?php require_once BASE_PATH . '/app/Views/templates/header.php'; ?>

<hr>
<h2>Atualizar Candidatura</h2>
<form action="<?= BASE_URL ?>/application/update/<?php echo htmlspecialchars($application['id']); ?>" method="post">
    <div class="form-group">
        <label for="name">Nome:</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($application['name']); ?>" required>
    </div>

    <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($application['email']); ?>" required>
    </div>

    <div class="form-group">
        <label for="phone">Telefone:</label>
        <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($application['phone']); ?>" required>
    </div>

    <div class="form-group">
        <label for="years_experience">Anos de Experiência:</label>
        <input type="number" id="years_experience" name="years_experience" value="<?php echo htmlspecialchars($application['years_experience']); ?>" required>
    </div>

    <div class="form-group">
        <label for="expected_salary">Salário esperado:</label>
        <input type="number" id="expected_salary" name="expected_salary" value="<?php echo htmlspecialchars($application['expected_salary']); ?>" required>
    </div>

    <div class="form-group">
        <label for="bio">Apresentação:</label>
        <textarea id="bio" name="bio" rows="4" required><?php echo htmlspecialchars($application['bio']); ?></textarea>
    </div>

    <button type="submit" class="btn btn-primary">Atualizar Candidatura</button>
</form>

<?php require_once BASE_PATH . '/app/Views/templates/footer.php'; ?>
