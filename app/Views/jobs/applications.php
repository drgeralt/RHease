<?php require_once BASE_PATH . '/app/Views/templates/header.php'; ?>

<h1>Candidatos</h1>

<?php if (empty($applications)): ?>
    <p style="text-align: center">Nenhuma candidatura encontrada.</p>
<?php else: ?>
<?php foreach ($applications as $application): ?>
    <div class="candidate-card">
        <h2><?php echo htmlspecialchars($application['name']); ?></h2>

        <p><strong>Email:</strong> <?php echo htmlspecialchars($application['email']); ?></p>
        <p><strong>Telefone:</strong> <?php echo htmlspecialchars($application['phone']); ?></p>
        <p><strong>Anos de Experiência:</strong> <?php echo htmlspecialchars($application['years_experience']); ?></p>
        <p><strong>Salário Esperado:</strong><?php echo htmlspecialchars($application['expected_salary']); ?></p>

        <div class="actions">
            <a href="<?= BASE_URL ?>/application/edit/<?php echo $application['id']; ?>" class="btn btn-primary">Atualizar</a>
            <a href="<?= BASE_URL ?>/view-bio/<?php echo $application['id']; ?>" class="btn btn-secondary">Bio</a>
            <a href="<?= BASE_URL ?>/delete-application/<?php echo $application['id']; ?>" class="btn btn-danger" onclick="return confirm('Tem certeza?')">Deletar</a>
        </div>
    </div>
<?php endforeach; ?>
<?php endif; ?>

<?php require_once BASE_PATH . '/app/Views/templates/footer.php'; ?>
