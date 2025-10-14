<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
header('Content-Type: application/json');

// Configurações do banco
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'rhease';

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'mensagem' => 'Erro ao conectar ao banco: '.$conn->connect_error]);
    exit;
}

// Recebe a ação principal
$acao = $_POST['acao'] ?? '';

switch($acao) {
    
    case 'criar':
    case 'editar':
        $id = $_POST['id'] ?? null;
        $nome = $_POST['nome'] ?? '';
        $categoria = $_POST['categoria'] ?? '';
        $tipo_valor_puro = $_POST['tipo_valor'] ?? ''; 
        $valor_fixo = !empty($_POST['valor_fixo']) ? $_POST['valor_fixo'] : null;

        $tipo_valor_a_salvar = $tipo_valor_puro;

        if ($tipo_valor_puro === 'Variável' || $tipo_valor_puro === 'Descritivo') {
            $valor_fixo = null;
        }

        if ($acao === 'criar') {
            // 1. INSERIR em beneficios_catalogo
            // Nota: Removido 'descricao' conforme estrutura do seu banco
            $stmt_catalogo = $conn->prepare("INSERT INTO beneficios_catalogo (nome, categoria, tipo_valor, status) VALUES (?, ?, ?, 'Ativo')");
            $stmt_catalogo->bind_param('sss', $nome, $categoria, $tipo_valor_a_salvar);
            
            if(!$stmt_catalogo->execute()) {
                echo json_encode(['success'=>false,'mensagem'=>'Erro ao criar benefício no catálogo: '.$stmt_catalogo->error]);
                $stmt_catalogo->close();
                break;
            }
            $novo_id = $conn->insert_id;
            $stmt_catalogo->close();

            // 2. INSERIR/ATUALIZAR em beneficio (se for Fixo)
            if ($tipo_valor_puro === 'Fixo') {
                $stmt_beneficio = $conn->prepare("INSERT INTO beneficios_catalogo (id_beneficio, nome, custo_padrao_empresa) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE nome=?, custo_padrao_empresa=?");
                $stmt_beneficio->bind_param('issss', $novo_id, $nome, $valor_fixo, $nome, $valor_fixo);
                
                if(!$stmt_beneficio->execute()) {
                    echo json_encode(['success'=>false,'mensagem'=>'Erro ao definir custo: '.$stmt_beneficio->error]);
                    $stmt_beneficio->close();
                    break;
                }
                $stmt_beneficio->close();
            }

            echo json_encode(['success'=>true,'mensagem'=>'Benefício criado com sucesso!']);
            
        } else { // 'editar'
            if(!$id) {
                echo json_encode(['success'=>false,'mensagem'=>'ID do benefício não informado.']);
                break;
            }

            // 1. ATUALIZAR beneficios_catalogo
            $stmt_catalogo = $conn->prepare("UPDATE beneficios_catalogo SET nome=?, categoria=?, tipo_valor=? WHERE id_beneficio=?");
            $stmt_catalogo->bind_param('sssi', $nome, $categoria, $tipo_valor_a_salvar, $id);
            
            if(!$stmt_catalogo->execute()) {
                echo json_encode(['success'=>false,'mensagem'=>'Erro ao editar benefício no catálogo: '.$stmt_catalogo->error]);
                $stmt_catalogo->close();
                break;
            }
            $stmt_catalogo->close();

            // 2. INSERIR/ATUALIZAR em beneficio (se for Fixo)
            if ($tipo_valor_puro === 'Fixo') {
                $stmt_beneficio = $conn->prepare("INSERT INTO beneficios_catalogo (id_beneficio, nome, custo_padrao_empresa) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE nome=?, custo_padrao_empresa=?");
                $stmt_beneficio->bind_param('issss', $id, $nome, $valor_fixo, $nome, $valor_fixo);
                
                if(!$stmt_beneficio->execute()) {
                    echo json_encode(['success'=>false,'mensagem'=>'Erro ao definir custo: '.$stmt_beneficio->error]);
                    $stmt_beneficio->close();
                    break;
                }
                $stmt_beneficio->close();
            } else {
                 // Limpa o custo se o tipo mudou para Variável ou Descritivo
                 $conn->query("DELETE FROM beneficio WHERE id_beneficio = '{$id}'");
            }

            echo json_encode(['success'=>true,'mensagem'=>'Benefício editado com sucesso!']);
        }
        break;

    case 'desativar':
        $id = $_POST['id'] ?? null;
        if(!$id) {
            echo json_encode(['success'=>false,'mensagem'=>'ID do benefício não informado.']);
            break;
        }
        
        // Alterna status
        $stmt = $conn->prepare("SELECT status FROM beneficios_catalogo WHERE id_beneficio=?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        $novo_status = ($res['status'] === 'Ativo') ? 'Inativo' : 'Ativo';
        
        $stmt2 = $conn->prepare("UPDATE beneficios_catalogo SET status=? WHERE id_beneficio=?");
        $stmt2->bind_param('si', $novo_status, $id);
        if($stmt2->execute()) {
            echo json_encode(['success'=>true,'mensagem'=>'Status atualizado para '.$novo_status.' com sucesso!']);
        } else {
            echo json_encode(['success'=>false,'mensagem'=>'Erro ao atualizar status.']);
        }
        $stmt2->close();
        break;


    // --- NOVA AÇÃO: SALVAR REGRAS DE ATRIBUIÇÃO AUTOMÁTICA ---
    
    case 'salvar_regras':
        $tipo_contrato = $_POST['tipo_contrato'] ?? '';
        $beneficios_ids = $_POST['beneficios_ids'] ?? []; 

        if (empty($tipo_contrato)) {
            echo json_encode(['success' => false, 'mensagem' => 'Tipo de contrato não informado.']);
            $conn->close();
            exit;
        }

        $conn->begin_transaction();
        
        try {
            // 1. DELETAR todas as regras existentes para este tipo de contrato
            $stmt_delete = $conn->prepare("DELETE FROM regras_beneficios WHERE tipo_contrato = ?");
            $stmt_delete->bind_param('s', $tipo_contrato);
            if (!$stmt_delete->execute()) {
                throw new Exception("Erro ao deletar regras antigas: " . $stmt_delete->error);
            }
            $stmt_delete->close();

            // 2. INSERIR as novas regras, uma por uma
            if (!empty($beneficios_ids)) {
                $stmt_insert = $conn->prepare("INSERT INTO regras_beneficios (tipo_contrato, id_beneficio) VALUES (?, ?)");
                
                foreach ($beneficios_ids as $id_beneficio) {
                    $id_beneficio = (int)$id_beneficio;

                    $stmt_insert->bind_param('si', $tipo_contrato, $id_beneficio);
                    if (!$stmt_insert->execute()) {
                        throw new Exception("Erro ao inserir novo benefício para regra: " . $stmt_insert->error);
                    }
                }
                $stmt_insert->close();
            }
            
            $conn->commit();
            echo json_encode(['success' => true, 'mensagem' => 'Regras de Atribuição salvas com sucesso para ' . $tipo_contrato . '!']);

        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['success' => false, 'mensagem' => 'Erro ao salvar regras: ' . $e->getMessage()]);
        }
        break;

            case 'deletar': // <<< NOVO CASE DE DELETAR
        $id = $_POST['id'] ?? null;
        if(!$id) {
            echo json_encode(['success'=>false,'mensagem'=>'ID do benefício não informado para deletar.']);
            break;
        }

        $conn->begin_transaction();

        try {
            // 1. Deletar de regras_beneficios (limpeza de dependência)
            $stmt_regras = $conn->prepare("DELETE FROM regras_beneficios WHERE id_beneficio = ?");
            $stmt_regras->bind_param('i', $id);
            $stmt_regras->execute();
            $stmt_regras->close();

            // 2. Deletar de beneficio (custo)
            $stmt_beneficio = $conn->prepare("DELETE FROM beneficios_catalogo WHERE id_beneficio = ?");
            $stmt_beneficio->bind_param('i', $id);
            $stmt_beneficio->execute();
            $stmt_beneficio->close();

            // 3. Deletar de beneficios_catalogo (o registro principal)
            $stmt_catalogo = $conn->prepare("DELETE FROM beneficios_catalogo WHERE id_beneficio = ?");
            $stmt_catalogo->bind_param('i', $id);
        
            if(!$stmt_catalogo->execute()) {
        throw new Exception("Erro ao deletar benefício do catálogo: " . $stmt_catalogo->error);
            }
            $stmt_catalogo->close();

            $conn->commit();
            echo json_encode(['success'=>true,'mensagem'=>'Benefício deletado permanentemente com sucesso!']);

        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['success'=>false,'mensagem'=>'Erro ao deletar benefício: ' . $e->getMessage()]);
        }
        break;

        case 'buscar_colaborador':
        $termo = $_POST['termo'] ?? '';
        $termo = "%" . $termo . "%";

        $sql = "SELECT id_colaborador, nome_completo, matricula FROM colaborador 
                WHERE nome_completo LIKE ? OR matricula LIKE ? LIMIT 5";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ss', $termo, $termo);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $colaboradores = [];
        while($row = $result->fetch_assoc()) {
            $colaboradores[] = $row;
        }
        $stmt->close();
        
        echo json_encode(['success' => true, 'colaboradores' => $colaboradores]);
        break;
        case 'carregar_beneficios_colaborador':
        $id_colaborador = (int)($_POST['id_colaborador'] ?? 0);

        if ($id_colaborador === 0) {
            echo json_encode(['success' => false, 'mensagem' => 'ID de colaborador inválido.']);
            break;
        }

        // 1. Buscar os dados básicos do colaborador
        $sql_colab = "SELECT id_colaborador, nome_completo, matricula, id_setor, id_cargo FROM colaborador WHERE id_colaborador = ?";
        $stmt_colab = $conn->prepare($sql_colab);
        $stmt_colab->bind_param('i', $id_colaborador);
        $stmt_colab->execute();
        $res_colab = $stmt_colab->get_result();
        
        if ($res_colab->num_rows === 0) {
            echo json_encode(['success' => false, 'mensagem' => 'Colaborador não encontrado.']);
            $stmt_colab->close();
            break;
        }
        $dados_colaborador = $res_colab->fetch_assoc();
        $stmt_colab->close();

        // ADICIONANDO UM PLACEHOLDER para o JavaScript não quebrar no campo 'tipo_contrato'
        $dados_colaborador['tipo_contrato'] = 'VINCULAR TIPO DE CONTRATO';


        // 3. Buscar os IDs dos benefícios salvos como EXCEÇÃO
        $sql_excecoes = "SELECT id_beneficio FROM colaborador_beneficio WHERE id_colaborador = ?";
        $stmt_excecoes = $conn->prepare($sql_excecoes);
        $stmt_excecoes->bind_param('i', $id_colaborador);
        $stmt_excecoes->execute();
        $res_excecoes = $stmt_excecoes->get_result();
        
        $beneficios_ids = [];
        while($row = $res_excecoes->fetch_assoc()) {
            $beneficios_ids[] = $row['id_beneficio']; 
        }
        $stmt_excecoes->close();
        
        // Retorna todos os dados para o JavaScript
        echo json_encode([
            'success' => true, 
            'dados_colaborador' => $dados_colaborador,
            'beneficios_ids' => $beneficios_ids
        ]);
        break;


    case 'salvar_beneficios_colaborador':
        $id_colaborador = $_POST['id_colaborador'] ?? null;
        $beneficios_ids = $_POST['beneficios_ids'] ?? []; 

        if (empty($id_colaborador)) {
            echo json_encode(['success' => false, 'mensagem' => 'ID do colaborador não informado.']);
            break;
        }

        $conn->begin_transaction();
        
        try {
            // 1. DELETAR todas as atribuições manuais existentes (REGISTROS DE EXCEÇÃO)
            $stmt_delete = $conn->prepare("DELETE FROM colaborador_beneficio WHERE id_colaborador = ?");
            $stmt_delete->bind_param('i', $id_colaborador);
            $stmt_delete->execute();
            $stmt_delete->close();

            // 2. INSERIR as novas exceções selecionadas
            if (!empty($beneficios_ids)) {
                // Prepara a inserção. valor_especifico é NULL se não for fornecido.
                $stmt_insert = $conn->prepare("INSERT INTO colaborador_beneficio (id_colaborador, id_beneficio) VALUES (?, ?)");
                
                foreach ($beneficios_ids as $id_beneficio) {
                    $id_beneficio = (int)$id_beneficio;

                    $stmt_insert->bind_param('ii', $id_colaborador, $id_beneficio);
                    if (!$stmt_insert->execute()) {
                        throw new Exception("Erro ao inserir novo benefício: " . $stmt_insert->error);
                    }
                }
                $stmt_insert->close();
            }
            
            $conn->commit();
            echo json_encode(['success' => true, 'mensagem' => 'Benefícios manuais salvos com sucesso!']);

        } catch (Exception $e) {
            $conn->rollback();
            // Lógica para enviar o erro: $e->getMessage()
            echo json_encode(['success' => false, 'mensagem' => 'Erro ao salvar: Contate o suporte.']); 
        }
        break;


    default:
        echo json_encode(['success'=>false,'mensagem'=>'Ação inválida.']);
        break;
}

$conn->close();