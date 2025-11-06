<?php

//session_start(); 
//$id_colaborador_logado = $_SESSION['id_colaborador'] ?? null;
//if (!$id_colaborador_logado) {
     //header("Location: /login");
    //exit();
//}
$id_colaborador_logado = 1; 



if ($conn->connect_error) {
    die("Erro ao conectar ao banco de dados."); 
}

// 2. FUNÇÕES AUXILIARES
function formatarValor($valor) {
    if (is_numeric($valor) && $valor > 0) {
        return 'R$ ' . number_format((float)$valor, 2, ',', '.');
    }
    return '';
}

function buscarBeneficiosColaborador($conn, $id_colaborador) {
    // 1. Buscar os benefícios e o nome do colaborador em uma única consulta
    $sql = "SELECT 
                c.nome_completo,
                bc.nome AS nome, 
                bc.categoria, 
                bc.tipo_valor, 
                cb.valor_especifico, 
                b.custo_padrao_empresa
            FROM 
                colaborador_beneficio cb
            JOIN 
                beneficios_catalogo bc ON cb.id_beneficio = bc.id_beneficio
            LEFT JOIN 
                beneficios_catalogo b ON bc.id_beneficio = b.id_beneficio
            JOIN
                colaborador c ON cb.id_colaborador = c.id_colaborador
            WHERE 
                cb.id_colaborador = ?
            AND 
                bc.status = 'Ativo'"; // <-- LINHA CORRIGIDA
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_colaborador);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $beneficios = [];
    $nome_colaborador = "Colaborador"; // Valor padrão

    if($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $beneficios[] = $row;
            // Captura o nome, que será o mesmo para todos os registros
            if ($nome_colaborador === "Colaborador") {
                $nome_colaborador = $row['nome_completo']; 
            }
        }
    } else {
         // Se não tiver benefícios, mas o ID é válido, busca o nome só do colaborador
         $sql_nome = "SELECT nome_completo FROM colaborador WHERE id_colaborador = ?";
         $stmt_nome = $conn->prepare($sql_nome);
         $stmt_nome->bind_param("i", $id_colaborador);
         $stmt_nome->execute();
         $result_nome = $stmt_nome->get_result();
         if ($row_nome = $result_nome->fetch_assoc()) {
             $nome_colaborador = $row_nome['nome_completo'];
         }
         $stmt_nome->close();
    }
    
    $stmt->close();
    return ['nome' => $nome_colaborador, 'beneficios' => $beneficios];
}

// 3. EXECUÇÃO
$dados = buscarBeneficiosColaborador($conn, $id_colaborador_logado);
$lista_beneficios = $dados['beneficios'];
$nome_colaborador = $dados['nome'];

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Benefícios - RH Ease</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/beneficiostyle.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <header>
        <i class="bi bi-list menu-toggle"></i>
        <img id="logo" src="img/rhease-ease 1.png" alt="RHease" width="130">
    </header>

    <div class="container">
        <div class="sidebar">
            <ul class="menu">
            <li><a href="<?= BASE_URL ?>/inicio"><i class="bi bi-clipboard-data-fill"></i> Painel</a></li>
                <li><a href="<?= BASE_URL ?>/dados"><i class="bi bi-person-vcard-fill"></i> Dados cadastrais</a></li>
                <li><a href="<?= BASE_URL ?>/registrarponto"><i class="bi bi-calendar2-check-fill"></i> Frequência</a></li>
                <li><a href="<?= BASE_URL ?>/meus-holerites"><i class="bi bi-wallet-fill"></i> Salário</a></li>
                <li><a href="<?= BASE_URL ?>/meus_beneficios"><i class="bi bi-shield-fill-check"></i> Benefícios</a></li>
                <li><a href="<?= BASE_URL ?>/vagas/listar"><i class="bi bi-briefcase-fill"></i> Gestão de Vagas</a></li>
                <li><a href="<?= BASE_URL ?>/contato"><i class="bi bi-person-lines-fill"></i> Contato</a></li>
            </ul>
        </div>

        <div class="content">
            <h2>Meus Benefícios Ativos</h2>

            <div class="tabela-container">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 20%;">Benefício <i class="bi bi-filter-left"></i></th>
                            <th style="width: 15%;">Categoria <i class="bi bi-filter-left"></i></th>
                            <th style="width: 15%;">Valor Atribuído <i class="bi bi-filter-left"></i></th>
                            <th style="width: 50%;">Descrição / Detalhes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($lista_beneficios)): ?>
                            <tr>
                                <td colspan="4" style="text-align: center;">Nenhum benefício ativo encontrado.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($lista_beneficios as $beneficio): ?>
                                <?php
                                $icone = 'bi-check-circle-fill'; 
                                
                                if (strpos($beneficio['nome_beneficio'], 'Transporte') !== false) $icone = 'bi-bus-front-fill';
                                elseif (strpos($beneficio['nome_beneficio'], 'Saúde') !== false) $icone = 'bi-heart-fill';
                                elseif (strpos($beneficio['nome_beneficio'], 'Refeição') !== false || strpos($beneficio['nome_beneficio'], 'Alimentação') !== false) $icone = 'bi-cup-hot-fill';

                                $valor_final = '';
                                $detalhes_descricao = '';
                                
                                if ($beneficio['tipo_valor'] === 'Fixo') {
                                    $valor_a_usar = !empty($beneficio['valor_especifico']) 
                                                    ? $beneficio['valor_especifico'] 
                                                    : $beneficio['custo_padrao_empresa'];
                                    $valor_formatado = formatarValor($valor_a_usar);
                                    $valor_final = "Fixo ({$valor_formatado})";
                                    $detalhes_descricao = "—";
                                    
                                } elseif ($beneficio['tipo_valor'] === 'Variável') {
                                    $valor_final = "Variável";
                                    $detalhes_descricao = !empty($beneficio['valor_especifico']) 
                                                          ? htmlspecialchars($beneficio['valor_especifico']) 
                                                          : "Consulte a política de bônus ou o RH.";
                                    
                                } elseif ($beneficio['tipo_valor'] === 'Descritivo') {
                                    $valor_final = "Descritivo";
                                    $detalhes_descricao = !empty($beneficio['valor_especifico']) 
                                                          ? htmlspecialchars($beneficio['valor_especifico']) 
                                                          : "Detalhes disponíveis com o RH.";
                                }
                                ?>
                                <tr>
                                    <td><i class="bi <?php echo $icone; ?> me-2" style="color:#489D3B;"></i> <strong><?php echo htmlspecialchars($beneficio['nome_beneficio']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($beneficio['categoria']); ?></td>
                                    <td><?php echo $valor_final; ?></td>
                                    <td><?php echo $detalhes_descricao; ?></td> 
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <p class="aviso mt-4" style="color: #5D6063;"> 
                Para solicitar a inclusão ou alteração de benefícios, por favor, entre em contato com o RH.
            </p>
        </div>
    </div>

<script>
    const BASE_URL = "<?php echo BASE_URL; ?>";
</script>
<script src="<?php echo BASE_URL; ?>/js/sidebar-toggle.js"></script>
</body>
</html>