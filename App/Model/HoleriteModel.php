<?php
// App/Model/HoleriteModel.php

namespace App\Model;

use PDO;
use PDOException;
use App\Core\Model;

class HoleriteModel extends Model
{


    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
    }
    public function buscarDadosParaTela(int $idColaborador): array
    {
        // 1. Busca o nome do colaborador
        $sqlNome = "SELECT nome_completo FROM colaborador WHERE id_colaborador = :id LIMIT 1";
        $stmtNome = $this->db_connection->prepare($sqlNome);
        $stmtNome->bindParam(':id', $idColaborador, PDO::PARAM_INT);
        $stmtNome->execute();
        $colaborador = $stmtNome->fetch(PDO::FETCH_ASSOC);

        // 2. Busca a lista de holerites (lógica do seu método findByColaboradorId original)
        $sqlHolerites = "SELECT id_holerite, mes_referencia, ano_referencia, data_processamento, salario_liquido 
                         FROM holerites 
                         WHERE id_colaborador = :idColaborador 
                         ORDER BY ano_referencia DESC, mes_referencia DESC";
        $stmtHolerites = $this->db_connection->prepare($sqlHolerites);
        $stmtHolerites->bindParam(':idColaborador', $idColaborador, PDO::PARAM_INT);
        $stmtHolerites->execute();
        $holerites = $stmtHolerites->fetchAll(PDO::FETCH_OBJ); // Mantido OBJ para compatibilidade com a sua view

        return [
            'colaborador_nome' => $colaborador ? $colaborador['nome_completo'] : 'Não encontrado',
            'holerites' => $holerites
        ];
    }
    public function findColaboradorById(int $idColaborador)
    {
        $sql = "SELECT id_colaborador, nome_completo FROM colaborador WHERE id_colaborador = :id LIMIT 1";
        $stmt = $this->db_connection->prepare($sql);
        $stmt->bindParam(':id', $idColaborador, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function findByColaboradorId(int $colaboradorId): array
    {
        $sql = "SELECT id_holerite, mes_referencia, ano_referencia, data_processamento, salario_liquido 
                FROM holerites 
                WHERE id_colaborador = :colaboradorId 
                ORDER BY ano_referencia DESC, mes_referencia DESC";

        $stmt = $this->db_connection->prepare($sql);
        $stmt->bindParam(':colaboradorId', $colaboradorId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Busca os dados principais do holerite e do colaborador
    public function findHoleriteCompletoById(int $holeriteId)
    {
        $sql = "SELECT h.*, c.nome_completo, c.CPF, c.matricula, ca.nome_cargo, s.nome_setor
                FROM holerites h
                JOIN colaborador c ON h.id_colaborador = c.id_colaborador
                LEFT JOIN cargo ca ON c.id_cargo = ca.id_cargo
                LEFT JOIN setor s ON c.id_setor = s.id_setor
                WHERE h.id_holerite = :holeriteId";

        $stmt = $this->db_connection->prepare($sql);
        $stmt->bindParam(':holeriteId', $holeriteId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    // --- NOVO MÉTODO 2 ---
    // Busca a lista de proventos e descontos de um holerite específico
    public function findItensByHoleriteId(int $holeriteId): array
    {
        $sql = "SELECT * FROM holerite_itens WHERE id_holerite = :holeriteId";
        $stmt = $this->db_connection->prepare($sql);
        $stmt->bindParam(':holeriteId', $holeriteId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function findHoleritePorColaboradorEMes(int $idColaborador, int $mes, int $ano)
    {
        $sql = "SELECT h.*, c.nome_completo, c.CPF, c.matricula, ca.nome_cargo, s.nome_setor
            FROM holerites h
            JOIN colaborador c ON h.id_colaborador = c.id_colaborador
            LEFT JOIN cargo ca ON c.id_cargo = ca.id_cargo
            LEFT JOIN setor s ON c.id_setor = s.id_setor
            WHERE h.id_colaborador = :idColaborador 
              AND h.mes_referencia = :mes
              AND h.ano_referencia = :ano
            LIMIT 1";

        $stmt = $this->db_connection->prepare($sql);
        $stmt->execute([
            ':idColaborador' => $idColaborador,
            ':mes' => $mes,
            ':ano' => $ano
        ]);

        return $stmt->fetch(PDO::FETCH_OBJ);
    }
}