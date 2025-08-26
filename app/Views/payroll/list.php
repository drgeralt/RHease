<?php require_once BASE_PATH . '/app/Views/templates/header.php'; ?>

<h1>Registros de Folha de Pagamento</h1>
<a href="<?= BASE_URL ?>/payroll/add" class="btn btn-primary">Adicionar Novo Registro</a>
<a href="<?= BASE_URL ?>/" class="btn btn-secondary">Voltar para o menu</a>

<div class="filter-form">
    <form action="<?= BASE_URL ?>/payrolls" method="GET">
        <label for="search_name">Procurar por Nome:</label>
        <input type="text" id="search_name" name="search_name" value="<?php echo htmlspecialchars($currentNameSearch ?? ''); ?>">

        <label for="position">Cargo:</label>
        <input type="text" id="position" name="position" value="<?php echo htmlspecialchars($currentPositionFilter ?? ''); ?>">

        <label for="order_by">Ordenar por:</label>
        <select id="order_by" name="order_by">
            <option value="id" <?php echo ($currentOrderBy === 'id') ? 'selected' : ''; ?>>ID</option>
            <option value="name" <?php echo ($currentOrderBy === 'name') ? 'selected' : ''; ?>>Nome</option>
            <option value="position" <?php echo ($currentOrderBy === 'position') ? 'selected' : ''; ?>>Cargo</option>
            <option value="salary" <?php echo ($currentOrderBy === 'salary') ? 'selected' : ''; ?>>Salário</option>
        </select>

        <label for="order_direction">Direção:</label>
        <select id="order_direction" name="order_direction">
            <option value="ASC" <?php echo ($currentOrderDirection === 'ASC') ? 'selected' : ''; ?>>Crescente</option>
            <option value="DESC" <?php echo ($currentOrderDirection === 'DESC') ? 'selected' : ''; ?>>Decrescente</option>
        </select>

        <button type="submit" class="btn btn-primary">Aplicar Filtros</button>
    </form>
</div>

<?php if (empty($payrolls)): ?>
    <p>Nenhum registro de folha de pagamento encontrado.</p>
<?php else: ?>
    <table class="data-table">
        <thead>
            <tr>
                <th><a href="?order_by=id&order_direction=<?php echo ($currentOrderBy === 'id' && $currentOrderDirection === 'ASC') ? 'DESC' : 'ASC'; ?>&position=<?php echo htmlspecialchars($currentPositionFilter ?? ''); ?>&search_name=<?php echo htmlspecialchars($currentNameSearch ?? ''); ?>" class="sort-link">ID<?php echo ($currentOrderBy === 'id') ? (($currentOrderDirection === 'ASC') ? ' &#9650;' : ' &#9660;') : ''; ?></a></th>
                <th><a href="?order_by=name&order_direction=<?php echo ($currentOrderBy === 'name' && $currentOrderDirection === 'ASC') ? 'DESC' : 'ASC'; ?>&position=<?php echo htmlspecialchars($currentPositionFilter ?? ''); ?>&search_name=<?php echo htmlspecialchars($currentNameSearch ?? ''); ?>" class="sort-link">Nome<?php echo ($currentOrderBy === 'name') ? (($currentOrderDirection === 'ASC') ? ' &#9650;' : ' &#9660;') : ''; ?></a></th>
                <th>Email</th>
                <th><a href="?order_by=position&order_direction=<?php echo ($currentOrderBy === 'position' && $currentOrderDirection === 'ASC') ? 'DESC' : 'ASC'; ?>&position=<?php echo htmlspecialchars($currentPositionFilter ?? ''); ?>&search_name=<?php echo htmlspecialchars($currentNameSearch ?? ''); ?>" class="sort-link">Cargo<?php echo ($currentOrderBy === 'position') ? (($currentOrderDirection === 'ASC') ? ' &#9650;' : ' &#9660;') : ''; ?></a></th>
                <th><a href="?order_by=salary&order_direction=<?php echo ($currentOrderBy === 'salary' && $currentOrderDirection === 'ASC') ? 'DESC' : 'ASC'; ?>&position=<?php echo htmlspecialchars($currentPositionFilter ?? ''); ?>&search_name=<?php echo htmlspecialchars($currentNameSearch ?? ''); ?>" class="sort-link">Salário<?php echo ($currentOrderBy === 'salary') ? (($currentOrderDirection === 'ASC') ? ' &#9650;' : ' &#9660;') : ''; ?></a></th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($payrolls as $payroll): ?>
                <tr>
                    <td><?php echo htmlspecialchars($payroll['id']); ?></td>
                    <td><?php echo htmlspecialchars($payroll['name']); ?></td>
                    <td><?php echo htmlspecialchars($payroll['email']); ?></td>
                    <td><?php echo htmlspecialchars($payroll['position']); ?></td>
                    <td><?php echo htmlspecialchars(number_format($payroll['salary'], 2, ',', '.')); ?></td>
                    <td>
                        <form action="<?= BASE_URL ?>/payroll/remove" method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $payroll['id']; ?>">
                            <button type="submit" onclick="return confirm('Tem certeza que deseja remover este registro?');" class="btn btn-danger btn-small">Remover</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require_once BASE_PATH . '/app/Views/templates/footer.php'; ?>