<?php
// acoes_beneficio.php (Arquivo Unificado: Benefícios e Regras)
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

    // --- AÇÕES DE GERENCIAMENTO DO CATÁLOGO DE BENEFÍCIOS ---
    
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
                $stmt_beneficio = $conn->prepare("INSERT INTO beneficio (id_beneficio, nome_beneficio, custo_padrao_empresa) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE nome_beneficio=?, custo_padrao_empresa=?");
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
                $stmt_beneficio = $conn->prepare("INSERT INTO beneficio (id_beneficio, nome_beneficio, custo_padrao_empresa) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE nome_beneficio=?, custo_padrao_empresa=?");
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


    default:
        echo json_encode(['success'=>false,'mensagem'=>'Ação inválida.']);
        break;
}

$conn->close();