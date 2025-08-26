<?php require_once BASE_PATH . '/app/Views/templates/header.php'; ?>

<h1><?php echo htmlspecialchars($application['name']); ?></h1>
<div class="candidate-card">
    <p><?php echo htmlspecialchars($application['bio']); ?></p>
</div>
<a href="<?= BASE_URL ?>/applications" class="btn btn-secondary">Voltar aos Candidatos</a>

<?php require_once BASE_PATH . '/app/Views/templates/footer.php'; ?>
