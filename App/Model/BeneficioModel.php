<?php
namespace App\Model;

use PDO;
use PDOException;
use InvalidArgumentException;

/**
 * Classe BeneficioModel
 *
 * Responsável por toda a interação de dados e lógica de negócio
 * relacionada à feature de Benefícios.
 */
class BeneficioModel
{
    private $pdo;

    public function __construct()
    {
        $host = 'localhost';
        $user = 'root';
        $password = '';
        $database = 'rhease';

        try {
            $this->pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $user, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // Em um ambiente de produção, logue o erro em vez de exibi-lo.
            throw new \Exception("Falha na Conexão com o Banco de Dados.");
        }
    }

    // ===================================
    // MÉTODOS DE LEITURA (READ)
    // ===================================

    public function listarBeneficiosComCusto(): array
    {
        $stmt = $this->pdo->query("
            SELECT id_beneficio, nome, categoria, tipo_valor, custo_padrao_empresa AS valor_fixo, status
            FROM beneficios_catalogo ORDER BY nome ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarBeneficiosAtivosParaSelecao(): array
    {
        $stmt = $this->pdo->query("SELECT id_beneficio, nome FROM beneficios_catalogo WHERE status = 'Ativo' ORDER BY nome");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarRegrasAtribuicao(): array
    {
        $sql = "
            SELECT rb.tipo_contrato, 
                   GROUP_CONCAT(bc.nome ORDER BY bc.nome SEPARATOR ', ') as nomes_beneficios,
                   GROUP_CONCAT(bc.id_beneficio ORDER BY bc.nome SEPARATOR ',') as ids_beneficios
            FROM regras_beneficios rb
            JOIN beneficios_catalogo bc ON rb.id_beneficio = bc.id_beneficio
            WHERE bc.status = 'Ativo' GROUP BY rb.tipo_contrato
        ";
        $stmt = $this->pdo->query($sql);
        $regras = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $regras[$row['tipo_contrato']] = [
                'nomes' => explode(', ', $row['nomes_beneficios'] ?? ''),
                'ids' => explode(',', $row['ids_beneficios'] ?? '')
            ];
        }
        return $regras;
    }

    public function buscarColaborador(string $termo): array
    {
        $termoLike = "%" . $termo . "%";
        $sql = "SELECT id_colaborador, nome_completo, matricula FROM colaborador WHERE nome_completo LIKE :termo1 OR matricula LIKE :termo2 LIMIT 5";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':termo1' => $termoLike, ':termo2' => $termoLike]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function carregarBeneficiosColaborador(int $id_colaborador): array
    {
        $sql_colab = "SELECT id_colaborador, nome_completo, matricula, tipo_contrato FROM colaborador WHERE id_colaborador = :id";
        $stmt_colab = $this->pdo->prepare($sql_colab);
        $stmt_colab->execute([':id' => $id_colaborador]);
        $dados_colaborador = $stmt_colab->fetch(PDO::FETCH_ASSOC);

        if (!$dados_colaborador) {
            throw new \Exception("Colaborador não encontrado.");
        }

        $sql_excecoes = "SELECT id_beneficio FROM colaborador_beneficio WHERE id_colaborador = :id";
        $stmt_excecoes = $this->pdo->prepare($sql_excecoes);
        $stmt_excecoes->execute([':id' => $id_colaborador]);

        $beneficios_ids = $stmt_excecoes->fetchAll(PDO::FETCH_COLUMN, 0);

        return [
            'dados_colaborador' => $dados_colaborador,
            'beneficios_ids' => $beneficios_ids
        ];
    }

    // ===================================
    // MÉTODOS DE ESCRITA E LÓGICA DE NEGÓCIO
    // ===================================

    public function salvarBeneficio(?int $id, string $nome, string $categoria, string $tipo_valor, ?float $valor_fixo): int
    {
        if (empty(trim($nome)) || empty($categoria) || empty($tipo_valor)) {
            throw new InvalidArgumentException("Nome, categoria e tipo de valor são obrigatórios.");
        }
        if ($tipo_valor === 'Fixo' && $valor_fixo === null) {
            throw new InvalidArgumentException("Para benefícios do tipo 'Fixo', o valor é obrigatório.");
        }

        $custo = ($tipo_valor === 'Fixo') ? $valor_fixo : null;

        if ($id) {
            $stmt = $this->pdo->prepare("UPDATE beneficios_catalogo SET nome = :nome, categoria = :categoria, tipo_valor = :tipo_valor, custo_padrao_empresa = :custo WHERE id_beneficio = :id");
            $stmt->execute([':nome' => $nome, ':categoria' => $categoria, ':tipo_valor' => $tipo_valor, ':custo' => $custo, ':id' => $id]);
        } else {
            $stmt = $this->pdo->prepare("INSERT INTO beneficios_catalogo (nome, categoria, tipo_valor, custo_padrao_empresa, status) VALUES (:nome, :categoria, :tipo_valor, :custo, 'Ativo')");
            $stmt->execute([':nome' => $nome, ':categoria' => $categoria, ':tipo_valor' => $tipo_valor, ':custo' => $custo]);
            $id = $this->pdo->lastInsertId();
        }
        return $id;
    }

    public function deletarBeneficio(int $id): bool
    {
        $this->pdo->beginTransaction();
        try {
            $this->pdo->prepare("DELETE FROM regras_beneficios WHERE id_beneficio = :id")->execute([':id' => $id]);
            $this->pdo->prepare("DELETE FROM colaborador_beneficio WHERE id_beneficio = :id")->execute([':id' => $id]);
            $this->pdo->prepare("DELETE FROM beneficios_catalogo WHERE id_beneficio = :id")->execute([':id' => $id]);
            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw new \Exception("Erro ao deletar benefício: " . $e->getMessage());
        }
    }

    public function toggleStatus(int $id): string
    {
        $stmt_status = $this->pdo->prepare("SELECT status FROM beneficios_catalogo WHERE id_beneficio = :id");
        $stmt_status->execute([':id' => $id]);
        $status_atual = $stmt_status->fetchColumn();

        if (!$status_atual) {
            throw new \Exception("Benefício com ID $id não encontrado.");
        }

        $novo_status = ($status_atual === 'Ativo') ? 'Inativo' : 'Ativo';

        $stmt_update = $this->pdo->prepare("UPDATE beneficios_catalogo SET status = :status WHERE id_beneficio = :id");
        $stmt_update->execute([':status' => $novo_status, ':id' => $id]);

        return $novo_status;
    }

    public function salvarRegrasAtribuicao(string $tipoContrato, array $beneficios_ids): bool
    {
        $this->pdo->beginTransaction();
        try {
            $this->pdo->prepare("DELETE FROM regras_beneficios WHERE tipo_contrato = :tipo")->execute([':tipo' => $tipoContrato]);
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

    public function salvarBeneficiosColaborador(int $id_colaborador, array $beneficios_ids): bool
    {
        $this->pdo->beginTransaction();
        try {
            $this->pdo->prepare("DELETE FROM colaborador_beneficio WHERE id_colaborador = :id")->execute([':id' => $id_colaborador]);

            if (!empty($beneficios_ids)) {
                $stmt_insert = $this->pdo->prepare("INSERT INTO colaborador_beneficio (id_colaborador, id_beneficio) VALUES (:id_colaborador, :id_beneficio)");
                foreach ($beneficios_ids as $id_beneficio) {
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

    public function aplicarRegrasPadrao(int $idColaborador): bool
    {
        $stmtTipo = $this->pdo->prepare("SELECT tipo_contrato FROM colaborador WHERE id_colaborador = :id");
        $stmtTipo->execute([':id' => $idColaborador]);
        $tipoContrato = $stmtTipo->fetchColumn();

        if (empty($tipoContrato)) {
            return true;
        }

        $stmtRegras = $this->pdo->prepare("SELECT id_beneficio FROM regras_beneficios WHERE tipo_contrato = :tipo");
        $stmtRegras->execute([':tipo' => $tipoContrato]);
        $regras_ids = $stmtRegras->fetchAll(PDO::FETCH_COLUMN, 0);

        if (empty($regras_ids)) {
            return true;
        }

        $this->pdo->beginTransaction();
        try {
            // Garante que não haja duplicatas, limpando antes (opcional, mas seguro)
            $this->pdo->prepare("DELETE FROM colaborador_beneficio WHERE id_colaborador = :id")->execute([':id' => $idColaborador]);

            $stmtInsert = $this->pdo->prepare("INSERT INTO colaborador_beneficio (id_colaborador, id_beneficio) VALUES (:id_colaborador, :id_beneficio)");
            foreach ($regras_ids as $idBeneficio) {
                $stmtInsert->execute([':id_colaborador' => $idColaborador, ':id_beneficio' => (int)$idBeneficio]);
            }
            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw new \Exception("Erro ao aplicar regras padrão: " . $e->getMessage());
        }
    }

    public function buscarBeneficiosParaColaborador(int $id_colaborador): array
    {
        // Query Corrigida: Removido o LEFT JOIN redundante e ajustado o alias da coluna 'nome'.
        $sql = "SELECT 
                        c.nome_completo,
                        bc.nome AS nome_beneficio, 
                        bc.categoria, 
                        bc.tipo_valor, 
                        cb.valor_especifico, 
                        bc.custo_padrao_empresa
                    FROM 
                        colaborador_beneficio cb
                    JOIN 
                        beneficios_catalogo bc ON cb.id_beneficio = bc.id_beneficio
                    JOIN
                        colaborador c ON cb.id_colaborador = c.id_colaborador
                    WHERE 
                        cb.id_colaborador = :id AND bc.status = 'Ativo'";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id_colaborador]);
        $beneficios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $nome_colaborador = "Colaborador"; // Valor Padrão
        if (!empty($beneficios)) {
            $nome_colaborador = $beneficios[0]['nome_completo'];
        } else {
            // Se não houver benefícios, busca apenas o nome para exibir na página.
            $stmt_nome = $this->pdo->prepare("SELECT nome_completo FROM colaborador WHERE id_colaborador = :id");
            $stmt_nome->execute([':id' => $id_colaborador]);
            $nome_colaborador = $stmt_nome->fetchColumn();
        }

        return ['nome' => $nome_colaborador, 'beneficios' => $beneficios];
    }
}