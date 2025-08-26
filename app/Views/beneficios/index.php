<?php require_once BASE_PATH . '/app/Views/templates/header.php'; ?>

<h2> Gestão de Tipos de Benefícios </h2>
<a href="<?= BASE_URL ?>/beneficios/criar" class="btn"> Criar Novo Benefício </a>
<br><br>

<table class="data-table">
    <thead>
        <tr>
            <th> Nome do Benefício </th>
            <th> Valor Mensal </th>
            <th> Tipo </th>
            <th> Ações </th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data['beneficios'] as $beneficio) : ?>
            <tr>
                <td><?= htmlspecialchars($beneficio['nome_beneficio']) ?></td>
                <td> R$ <?= number_format($beneficio['valor_mensal'], 2, ',', '.') ?></td>
                <td><?= htmlspecialchars(ucfirst($beneficio['tipo'])) ?></td>
                <td>
                    <a href="<?= BASE_URL ?>/beneficios/editar/<?= $beneficio['id'] ?>" class="btn-small btn-primary"> Editar </a>
                    <form action="<?= BASE_URL ?>/beneficios/deletar/<?= $beneficio['id'] ?>" method="POST" style="display:inline;" onsubmit="return false;">
                        <button type="submit" class="btn-small btn-danger btn-delete"> Excluir </button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require_once BASE_PATH . '/app/Views/templates/footer.php'; ?>