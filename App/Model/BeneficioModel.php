<?php

namespace App\Model;
use PDO;
use PDOException;

class BeneficioModel {
    private $pdo;

    public function __construct() {
        // ... (Seu código de conexão com PDO)
        $host = 'localhost';
        $user = 'root';
        $password = '';
        $database = 'rhease';

        try {
            $this->pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $user, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // Em ambiente de produção, não exibir detalhes do erro
            throw new \Exception("Falha na Conexão com o Banco de Dados: " . $e->getMessage()); 
        }
    }

    // ===================================
    // FUNÇÕES DE CATÁLOGO (CRUD BÁSICO)
    // ===================================

    // Lista todos os benefícios com valor fixo para a tabela de gerenciamento
    public function listarBeneficiosComCusto() {
    $stmt = $this->pdo->query("
        SELECT 
            id_beneficio, 
            nome, 
            categoria, 
            tipo_valor, 
            custo_padrao_empresa AS valor_fixo, 
            status
        FROM beneficios_catalogo
        ORDER BY nome ASC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    // Cria/Edita um benefício (Lógica de UPSET)
    public function salvarBeneficio($id, $nome, $categoria, $tipo_valor, $valor_fixo) {
    $this->pdo->beginTransaction();
    try {
        if ($id) {
            // Edição
            $stmt = $this->pdo->prepare("
                UPDATE beneficios_catalogo 
                SET nome = :nome, categoria = :categoria, tipo_valor = :tipo_valor,
                    custo_padrao_empresa = :custo
                WHERE id_beneficio = :id
            ");
            $stmt->execute([
                ':nome' => $nome,
                ':categoria' => $categoria,
                ':tipo_valor' => $tipo_valor,
                ':custo' => ($tipo_valor === 'Fixo') ? $valor_fixo : null,
                ':id' => $id
            ]);
        } else {
            // Criação
            $stmt = $this->pdo->prepare("
                INSERT INTO beneficios_catalogo 
                (nome, categoria, tipo_valor, custo_padrao_empresa, status)
                VALUES (:nome, :categoria, :tipo_valor, :custo, 'Ativo')
            ");
            $stmt->execute([
                ':nome' => $nome,
                ':categoria' => $categoria,
                ':tipo_valor' => $tipo_valor,
                ':custo' => ($tipo_valor === 'Fixo') ? $valor_fixo : null
            ]);
            $id = $this->pdo->lastInsertId();
        }

        $this->pdo->commit();
        return $id;
    } catch (PDOException $e) {
        $this->pdo->rollBack();
        throw new \Exception("Erro ao salvar benefício: " . $e->getMessage());
    }
}


    // Deleta um benefício (incluindo dependências)
    public function deletarBeneficio($id) {
        $this->pdo->beginTransaction();
        try {
            // 1. Deletar de regras_beneficios
            $stmt_regras = $this->pdo->prepare("DELETE FROM regras_beneficios WHERE id_beneficio = :id");
            $stmt_regras->execute([':id' => $id]);
            
            // 2. Deletar de colaborador_beneficio (Exceções)
            $stmt_colab_beneficio = $this->pdo->prepare("DELETE FROM colaborador_beneficio WHERE id_beneficio = :id");
            $stmt_colab_beneficio->execute([':id' => $id]);
            
            // 3. Deletar de beneficio (Custo)
            // 1. Deletar regras e exceções
            $stmt_regras = $this->pdo->prepare("DELETE FROM regras_beneficios WHERE id_beneficio = :id");
            $stmt_regras->execute([':id' => $id]);

            $stmt_colab = $this->pdo->prepare("DELETE FROM colaborador_beneficio WHERE id_beneficio = :id");
            $stmt_colab->execute([':id' => $id]);

            // 2. Deletar o benefício do catálogo
            $stmt_catalogo = $this->pdo->prepare("DELETE FROM beneficios_catalogo WHERE id_beneficio = :id");
            $stmt_catalogo->execute([':id' => $id]);


            $this->pdo->commit();
            return true;

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw new \Exception("Erro ao deletar benefício: " . $e->getMessage());
        }
    }

    // Altera status (Ativo/Inativo)
    public function toggleStatus($id) {
        $stmt_status = $this->pdo->prepare("SELECT status FROM beneficios_catalogo WHERE id_beneficio = :id");
        $stmt_status->execute([':id' => $id]);
        $status_atual = $stmt_status->fetchColumn();
        
        $novo_status = ($status_atual === 'Ativo') ? 'Inativo' : 'Ativo';

        $stmt_update = $this->pdo->prepare("UPDATE beneficios_catalogo SET status = :status WHERE id_beneficio = :id");
        $stmt_update->execute([':status' => $novo_status, ':id' => $id]);
        
        return $novo_status;
    }
    
    // Lista benefícios com ID e Nome para usar no modal de regras/colaborador
    public function listarBeneficiosAtivosParaSelecao() {
        $stmt = $this->pdo->query("SELECT id_beneficio, nome FROM beneficios_catalogo WHERE status = 'Ativo' ORDER BY nome");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // ===================================
    // FUNÇÕES DE REGRAS DE ATRIBUIÇÃO
    // ===================================

    // Salva/Substitui as regras para um tipo de contrato
    public function salvarRegrasAtribuicao($tipoContrato, $beneficios_ids) {
        $this->pdo->beginTransaction();
        try {
            // 1. DELETAR todas as regras existentes para este tipo de contrato
            $stmt_delete = $this->pdo->prepare("DELETE FROM regras_beneficios WHERE tipo_contrato = :tipo");
            $stmt_delete->execute([':tipo' => $tipoContrato]);

            // 2. INSERIR as novas regras
            if (!empty($beneficios_ids)) {
                $stmt_insert = $this->pdo->prepare("INSERT INTO regras_beneficios (tipo_contrato, id_beneficio) VALUES (:tipo, :id_beneficio)");
                foreach ($beneficios_ids as $id_beneficio) {
                    $stmt_insert->execute([':tipo' => $tipoContrato, ':id_beneficio' => (int)$id_beneficio]);
                }
            }
            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw new \Exception("Erro ao salvar regras: " . $e->getMessage());
        }
    }

    // Lista todas as regras de atribuição para a tabela de gerenciamento
    public function listarRegrasAtribuicao() {
        $sql = "
            SELECT 
                rb.tipo_contrato, 
                GROUP_CONCAT(bc.nome ORDER BY bc.nome SEPARATOR ', ') as nomes_beneficios,
                GROUP_CONCAT(bc.id_beneficio ORDER BY bc.nome SEPARATOR ',') as ids_beneficios
            FROM regras_beneficios rb
            JOIN beneficios_catalogo bc ON rb.id_beneficio = bc.id_beneficio
            WHERE bc.status = 'Ativo' 
            GROUP BY rb.tipo_contrato
        ";
        $stmt = $this->pdo->query($sql);
        $regras = [];
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $regras[$row['tipo_contrato']] = [
                'nomes' => explode(', ', $row['nomes_beneficios']),
                'ids' => explode(',', $row['ids_beneficios'])
            ];
        }
        return $regras;
    }

    // ===================================
    // FUNÇÕES DE COLABORADOR (EXCEÇÕES)
    // ===================================

    // Busca colaboradores por termo (nome ou matrícula)
    public function buscarColaborador($termo) {
        $termo = "%" . $termo . "%";
        $sql = "SELECT id_colaborador, nome_completo, matricula FROM colaborador WHERE nome_completo LIKE :termo1 OR matricula LIKE :termo2 LIMIT 5";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':termo1' => $termo, ':termo2' => $termo]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Carrega dados do colaborador e seus benefícios manuais (exceções)
    public function carregarBeneficiosColaborador($id_colaborador) {
        // 1. Buscar os dados básicos do colaborador
        $sql_colab = "SELECT id_colaborador, nome_completo, matricula, tipo_contrato FROM colaborador WHERE id_colaborador = :id";
        $stmt_colab = $this->pdo->prepare($sql_colab);
        $stmt_colab->execute([':id' => $id_colaborador]);
        $dados_colaborador = $stmt_colab->fetch(PDO::FETCH_ASSOC);
        
        if (!$dados_colaborador) {
            throw new \Exception("Colaborador não encontrado.");
        }

        // 2. Buscar os IDs dos benefícios salvos como EXCEÇÃO
        $sql_excecoes = "SELECT id_beneficio FROM colaborador_beneficio WHERE id_colaborador = :id";
        $stmt_excecoes = $this->pdo->prepare($sql_excecoes);
        $stmt_excecoes->execute([':id' => $id_colaborador]);
        
        $beneficios_ids = $stmt_excecoes->fetchAll(PDO::FETCH_COLUMN, 0);
        
        return [
            'dados_colaborador' => $dados_colaborador,
            'beneficios_ids' => $beneficios_ids
        ];
    }
    
    // Salva as atribuições manuais (exceções) de um colaborador
    public function salvarBeneficiosColaborador($id_colaborador, $beneficios_ids) {
        $this->pdo->beginTransaction();
        try {
            // 1. DELETAR todas as atribuições manuais existentes (REGISTROS DE EXCEÇÃO)
            $stmt_delete = $this->pdo->prepare("DELETE FROM colaborador_beneficio WHERE id_colaborador = :id");
            $stmt_delete->execute([':id' => $id_colaborador]);

            // 2. INSERIR as novas exceções selecionadas
            if (!empty($beneficios_ids)) {
                $stmt_insert = $this->pdo->prepare("INSERT INTO colaborador_beneficio (id_colaborador, id_beneficio) VALUES (:id_colaborador, :id_beneficio)");
                
                foreach ($beneficios_ids as $id_beneficio) {
                    // Nota: O seu acoes_beneficio.php não estava salvando valor_especifico, então mantemos a simplificação.
                    $stmt_insert->execute([':id_colaborador' => $id_colaborador, ':id_beneficio' => (int)$id_beneficio]);
                }
            }
            
            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw new \Exception("Erro ao salvar benefícios manuais: " . $e->getMessage());
        }
    }
    
    // Aplica regras padrão (Usada no cadastro de colaborador, por exemplo)
    public function aplicarRegrasPadrao($idColaborador) {
        // --- 1. BUSCAR O TIPO DE CONTRATO ---
        $sqlTipo = "SELECT tipo_contrato FROM colaborador WHERE id_colaborador = :id";
        $stmtTipo = $this->pdo->prepare($sqlTipo);
        $stmtTipo->execute([':id' => $idColaborador]);
        $tipoContrato = $stmtTipo->fetchColumn();
        
        if (empty($tipoContrato)) {
            return true; // Sem regras para aplicar
        }

        // --- 2. BUSCAR AS REGRAS PADRÃO ---
        $sqlRegras = "SELECT id_beneficio FROM regras_beneficios WHERE tipo_contrato = :tipo";
        $stmtRegras = $this->pdo->prepare($sqlRegras);
        $stmtRegras->execute([':tipo' => $tipoContrato]);
        $regras = $stmtRegras->fetchAll(PDO::FETCH_COLUMN, 0);

        if (empty($regras)) {
            return true; // Sem regras no catálogo para este tipo
        }
        
        // --- 3. INSERIR OS BENEFÍCIOS ---
        $this->pdo->beginTransaction();
        try {
            $sqlInsert = "INSERT INTO colaborador_beneficio (id_colaborador, id_beneficio) VALUES (:id_colaborador, :id_beneficio)";
            $stmtInsert = $this->pdo->prepare($sqlInsert);

            foreach ($regras as $idBeneficio) {
                // valor_especifico é NULL ou tem um padrão, aqui simplificado
                $stmtInsert->execute([':id_colaborador' => $idColaborador, ':id_beneficio' => (int)$idBeneficio]);
            }
            $this->pdo->commit();
            return true;
            
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw new \Exception("Erro ao aplicar regras padrão: " . $e->getMessage());
        }
    }
}
