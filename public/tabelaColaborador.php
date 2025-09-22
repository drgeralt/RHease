<?php
// Inclui a classe de conexão com o banco de dados
// Ajuste o caminho se o seu arquivo não estiver na raiz do projeto.
require_once '../app/Core/Database.php';

try {
    // Obtém a instância da conexão PDO
    $pdo = Database::getInstance();

    // Cria a consulta SQL para buscar os dados, unindo as tabelas
    $sql = "
        SELECT 
            c.id_colaborador, 
            c.nome_completo,
            c.data_admissao,
            c.situacao,
            ca.nome_cargo AS cargo,
            s.nome_setor AS departamento
        FROM 
            colaborador AS c
        LEFT JOIN 
            cargo AS ca ON c.id_cargo = ca.id_cargo
        LEFT JOIN 
            setor AS s ON c.id_setor = s.id_setor
        ORDER BY
            c.nome_completo ASC
    ";

    // Executa a consulta
    $stmt = $pdo->query($sql);

// Busca todos os resultados como um array associativo
$colaboradores = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
// Em caso de erro, exibe uma mensagem amigável e interrompe o script
die("Erro ao buscar os dados dos colaboradores: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabela de Colaboradores</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/tabelaColaborador.css">
</head>
<body>
<header class="topbar">
    <div class="logo"><img src="img/rhease-ease 1.png" alt="Logo RH Ease" class="logo"></div>
</header>

<main class="container mt-4">
    <h1 class="mb-4">Lista de Colaboradores</h1>

    <div class="card p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-outline-secondary">
                    <i class="bi bi-funnel-fill"></i> Filtros
                </button>
            </div>
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="search" class="form-control" placeholder="Pesquisar colaborador...">
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered align-middle">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Cargo</th>
                    <th>Departamento</th>
                    <th>Data de Admissão</th>
                    <th>Status</th>
                    <th class="text-center">Ações</th>
                </tr>
                </thead>
                <tbody>
                <?php if (count($colaboradores) > 0): ?>
                <?php foreach ($colaboradores as $colaborador): ?>
                <tr>
                    <td><?php echo htmlspecialchars($colaborador['id_colaborador']); ?></td>
                    <td><?php echo htmlspecialchars($colaborador['nome_completo']); ?></td>
                    <td><?php echo htmlspecialchars($colaborador['cargo']); ?></td>
                    <td><?php echo htmlspecialchars($colaborador['departamento']); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($colaborador['data_admissao'])); ?></td>
                    <td><span class="badge bg-success"><?php echo ucfirst(htmlspecialchars($colaborador['situacao'])); ?></span></td>
                    <td class="text-center">
                        <a href="#" class="btn btn-sm btn-primary" title="Editar"><i class="bi bi-pencil-fill"></i></a>
                        <a href="#" class="btn btn-sm btn-danger" title="Excluir"><i class="bi bi-trash-fill"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">Nenhum colaborador encontrado.</td>
                </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>
                <a href="adicionar_colaborador.php" class="btn btn-success">
                    <i class="bi bi-plus-circle-fill"></i> Adicionar Colaborador
                </a>
            </div>
            <nav aria-label="Navegação da página">
                <ul class="pagination mb-0">
                    <li class="page-item disabled"><a class="page-link" href="#">Anterior</a></li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item"><a class="page-link" href="#">Próximo</a></li>
                </ul>
            </nav>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>