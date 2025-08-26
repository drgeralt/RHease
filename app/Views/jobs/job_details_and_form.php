<?php require_once BASE_PATH . '/app/Views/templates/header.php'; ?>

<?php
// Variaveis criadas em JobController
echo "<h1>" . htmlspecialchars($title) . "</h1>";
echo "<p>" . htmlspecialchars($description) . "</p>";
echo "<h2>Responsabilidades:</h2>";
echo "<ul>";
foreach ($responsibilities as $responsibility) {
    echo "<li>" . htmlspecialchars($responsibility) . "</li>";
}
echo "</ul>";
?>

<hr>
<h2>Candidate-se para esta vaga</h2>
<form action="<?= BASE_URL ?>/submit-application" method="post">
    <div class="form-group">
        <label for="name">Nome:</label>
        <input type="text" id="name" name="name" required>
    </div>

    <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
    </div>

    <div class="form-group">
        <label for="phone">Telefone:</label>
        <input type="text" id="phone" name="phone" required>
    </div>

    <div class="form-group">
        <label for="years_experience">Anos de Experiência:</label>
        <input type="number" id="years_experience" name="years_experience" required>
    </div>

    <div class="form-group">
        <label for="expected_salary">Salário esperado:</label>
        <input type="number" id="expected_salary" name="expected_salary" required>
    </div>

    <div class="form-group">
        <label for="bio">Apresentação:</label>
        <textarea id="bio" name="bio" rows="4" required></textarea>
    </div>

    <button type="submit" class="btn btn-primary">Enviar Candidatura</button>
</form>

<?php require_once BASE_PATH . '/app/Views/templates/footer.php'; ?>
