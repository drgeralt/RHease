<?php
declare(strict_types=1);

namespace App\Model;

use App\Core\Model;
use PDO;

class BeneficioModel extends Model
{
    public function __construct(PDO $db)
    {
        parent::__construct($db);
    }
    // --- Funções para o Catálogo de Benefícios ---

    public function listarBeneficiosCatalogo(): array
    {
        // Exemplo de query
        $stmt = $this->db_connection->query("SELECT * FROM beneficios_catalogo");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function criarBeneficio(array $dados): string
    {
        $sql = "INSERT INTO beneficios_catalogo (nome, categoria, tipo_valor, custo_padrao_empresa, status) 
                VALUES (:nome, :categoria, :tipo_valor, :custo, 'Ativo')";
        $stmt = $this->db_connection->prepare($sql);
        $stmt->execute([
            'nome' => $dados['nome'],
            'categoria' => $dados['categoria'],
            'tipo_valor' => $dados['tipo_valor'],
            'custo' => ($dados['tipo_valor'] === 'Fixo') ? $dados['valor_fixo'] : null,
        ]);
        return $this->db_connection->lastInsertId();
    }

    public function editarBeneficio(array $dados): bool
    {
        $sql = "UPDATE beneficios_catalogo SET nome = :nome, categoria = :categoria, tipo_valor = :tipo_valor, custo_padrao_empresa = :custo 
                WHERE id_beneficio = :id";
        $stmt = $this->db_connection->prepare($sql);
        return $stmt->execute([
            'id' => $dados['id'],
            'nome' => $dados['nome'],
            'categoria' => $dados['categoria'],
            'tipo_valor' => $dados['tipo_valor'],
            'custo' => ($dados['tipo_valor'] === 'Fixo') ? $dados['valor_fixo'] : null,
        ]);
    }

    public function atualizarStatus(int $id): bool
    {
        $stmt = $this->db_connection->prepare("SELECT status FROM beneficios_catalogo WHERE id_beneficio = :id");
        $stmt->execute(['id' => $id]);
        $statusAtual = $stmt->fetchColumn();

        $novoStatus = ($statusAtual === 'Ativo') ? 'Inativo' : 'Ativo';

        $stmtUpdate = $this->db_connection->prepare("UPDATE beneficios_catalogo SET status = :status WHERE id_beneficio = :id");
        return $stmtUpdate->execute(['status' => $novoStatus, 'id' => $id]);
    }

    public function deletarBeneficio(int $id): bool
    {
        $this->db_connection->beginTransaction();
        try {
            $this->db_connection->prepare("DELETE FROM regras_beneficios WHERE id_beneficio = :id")->execute(['id' => $id]);
            $this->db_connection->prepare("DELETE FROM colaborador_beneficio WHERE id_beneficio = :id")->execute(['id' => $id]);
            $this->db_connection->prepare("DELETE FROM beneficios_catalogo WHERE id_beneficio = :id")->execute(['id' => $id]);
            $this->db_connection->commit();
            return true;
        } catch (\PDOException $e) {
            $this->db_connection->rollBack();
            return false;
        }
    }

    // --- Funções para Regras de Atribuição ---

    public function listarRegras(): array
    {
        $sql = "SELECT 
                    rb.tipo_contrato, 
                    GROUP_CONCAT(bc.nome SEPARATOR ', ') as nomes_beneficios,
                    GROUP_CONCAT(bc.id_beneficio SEPARATOR ',') as ids_beneficios
                FROM regras_beneficios rb
                JOIN beneficios_catalogo bc ON rb.id_beneficio = bc.id_beneficio
                WHERE bc.status = 'Ativo' 
                GROUP BY rb.tipo_contrato";
        $stmt = $this->db_connection->query($sql);
        $regras = [];
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $regras[$row['tipo_contrato']] = [
                'nomes' => explode(', ', $row['nomes_beneficios']),
                'ids' => explode(',', $row['ids_beneficios'])
            ];
        }
        return $regras;
    }

    public function salvarRegras(string $tipoContrato, array $beneficiosIds): bool
    {
        $this->db_connection->beginTransaction();
        try {
            $this->db_connection->prepare("DELETE FROM regras_beneficios WHERE tipo_contrato = :tipo")->execute(['tipo' => $tipoContrato]);

            if (!empty($beneficiosIds)) {
                $stmt = $this->db_connection->prepare("INSERT INTO regras_beneficios (tipo_contrato, id_beneficio) VALUES (:tipo, :id)");
                foreach ($beneficiosIds as $id) {
                    $stmt->execute(['tipo' => $tipoContrato, 'id' => (int)$id]);
                }
            }
            $this->db_connection->commit();
            return true;
        } catch (\PDOException $e) {
            $this->db_connection->rollBack();
            return false;
        }
    }

    // --- Funções para Gestão de Colaboradores ---

    public function buscarColaborador(string $termo): array
    {
        $sql = "SELECT id_colaborador, nome_completo, matricula FROM colaborador WHERE nome_completo LIKE :termo OR matricula LIKE :termo LIMIT 5";
        $stmt = $this->db_connection->prepare($sql);
        $stmt->execute(['termo' => "%{$termo}%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function carregarBeneficiosColaborador(int $idColaborador): array
    {
        $sqlColab = "SELECT id_colaborador, nome_completo, matricula, tipo_contrato FROM colaborador WHERE id_colaborador = :id";
        $stmtColab = $this->db_connection->prepare($sqlColab);
        $stmtColab->execute(['id' => $idColaborador]);
        $dadosColaborador = $stmtColab->fetch(PDO::FETCH_ASSOC);

        $sqlBeneficios = "SELECT id_beneficio FROM colaborador_beneficio WHERE id_colaborador = :id";
        $stmtBeneficios = $this->db_connection->prepare($sqlBeneficios);
        $stmtBeneficios->execute(['id' => $idColaborador]);
        $beneficiosIds = $stmtBeneficios->fetchAll(PDO::FETCH_COLUMN);

        return ['dados_colaborador' => $dadosColaborador, 'beneficios_ids' => $beneficiosIds];
    }

    public function salvarBeneficiosColaborador(int $idColaborador, array $beneficiosIds): bool
    {
        $this->db_connection->beginTransaction();
        try {
            $this->db_connection->prepare("DELETE FROM colaborador_beneficio WHERE id_colaborador = :id")->execute(['id' => $idColaborador]);

            if (!empty($beneficiosIds)) {
                $stmt = $this->db_connection->prepare("INSERT INTO colaborador_beneficio (id_colaborador, id_beneficio) VALUES (:id_colaborador, :id_beneficio)");
                foreach ($beneficiosIds as $id) {
                    $stmt->execute(['id_colaborador' => $idColaborador, 'id_beneficio' => (int)$id]);
                }
            }
            $this->db_connection->commit();
            return true;
        } catch (\PDOException $e) {
            $this->db_connection->rollBack();
            return false;
        }
    }
}
