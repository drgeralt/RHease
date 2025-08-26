<?php require_once BASE_PATH . '/app/Views/templates/header.php'; ?>

<h2>Iniciar Processo de Demissão</h2>
<p>Selecione o funcionário e preencha os dados para calcular a rescisão.</p>

<form action="<?= BASE_URL ?>/demissao" method="POST" class="form-demissao">
    <div class="form-group">
        <label for="funcionario_id">Funcionário:</label>
        <select id="funcionario_id" name="funcionario_id" required>
            <option value="">Selecione um funcionário</option>
            <?php foreach ($data['funcionarios'] as $funcionario) : ?>
                <option value="<?= htmlspecialchars($funcionario['id']) ?>">
                    <?= htmlspecialchars($funcionario['nome_completo']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="data_demissao">Data da Demissão:</label>
        <input type="date" id="data_demissao" name="data_demissao" required>
    </div>
    <div class="form-group">
        <label for="tipo_demissao">Tipo de Demissão:</label>
        <select id="tipo_demissao" name="tipo_demissao" required>
            <option value="pedido_demissao">Pedido de Demissão</option>
            <option value="sem_justa_causa">Demissão Sem Justa Causa</option>
            <option value="com_justa_causa">Demissão Com Justa Causa</option>
        </select>
    </div>
    <div class="form-group">
        <label for="motivo">Motivo (opcional):</label>
        <textarea id="motivo" name="motivo" rows="4"></textarea>
    </div>
    <div class="form-group">
        <input type="checkbox" id="ferias_vencidas" name="ferias_vencidas" value="1">
        <label for="ferias_vencidas">O funcionário possui 1 período de férias vencido e não utilizado?</label>
    </div>
    <button type="submit" class="btn">Processar e Calcular Rescisão</button>
</form>

<?php require_once BASE_PATH . '/app/Views/templates/footer.php'; ?>