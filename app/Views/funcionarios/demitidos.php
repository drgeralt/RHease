<?php require_once BASE_PATH . '/app/Views/templates/header.php'; ?>

<h2>Funcionários Demitidos</h2>
<p>Esta é a lista de todos os funcionários com status "inativo" no sistema.</p>

<table class="data-table">
    <thead>
        <tr>
            <th>Nome Completo</th>
            <th>Cargo</th>
            <th>Data de Admissão</th>
            <th>Ações</th> </tr>
    </thead>
    <tbody>
        <?php if (empty($data['funcionarios_demitidos'])): ?>
            <tr>
                <td colspan="4" style="text-align: center;">Nenhum funcionário demitido encontrado.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($data['funcionarios_demitidos'] as $funcionario): ?>
                <tr>
                    <td><?= htmlspecialchars($funcionario['nome_completo']) ?></td>
                    <td><?= htmlspecialchars($funcionario['cargo']) ?></td>
                    <td><?= date('d/m/Y', strtotime($funcionario['data_admissao'])) ?></td>
                    <td>
                        <a href="<?= BASE_URL ?>/demissao/resumo/<?= $funcionario['id'] ?>" class="btn-small btn-primary">Ver Resumo</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once BASE_PATH . '/app/Views/templates/footer.php'; ?>