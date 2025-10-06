<?php
declare(strict_types=1);

namespace App\Model;

use App\Core\Model;
use PDO;
use App\Core\Database;

class CandidaturaModel extends Model
{
    public function buscarAnaliseCompleta(int $idCandidatura): array|false
    {
        $sql = "SELECT 
                    c.id_candidatura,
                    c.data_candidatura,
                    c.status_triagem,
                    c.pontuacao_aderencia,  -- Novo campo!
                    c.justificativa_ia,     -- Novo campo!
                    cand.nome_completo AS nome_candidato,
                    v.titulo_vaga,
                    v.requisitos AS descricao_vaga
                FROM 
                    candidaturas AS c
                JOIN
                    candidato AS cand ON c.id_candidato = cand.id_candidato
                JOIN
                    vaga AS v ON c.id_vaga = v.id_vaga
                WHERE 
                    c.id_candidatura = :id_candidatura
                LIMIT 1";

        $stmt = $this->db_connection->prepare($sql);
        $stmt->execute([':id_candidatura' => $idCandidatura]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function buscarComVaga(int $idCandidatura): array|false
    {
        $sql = "SELECT 
                    c.id_candidatura,
                    c.id_vaga,
                    cand.curriculo,
                    v.requisitos AS descricao_vaga,
                    v.titulo_vaga
                FROM 
                    candidaturas AS c
                JOIN
                    candidato AS cand ON c.id_candidato = cand.id_candidato
                JOIN
                    vaga AS v ON c.id_vaga = v.id_vaga
                WHERE 
                    c.id_candidatura = :id_candidatura
                LIMIT 1";

        $stmt = $this->db_connection->prepare($sql);
        $stmt->execute([':id_candidatura' => $idCandidatura]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function atualizarResultadoIA(int $idCandidatura, int $nota, string $sumario): bool
    {
        $sql = "
            UPDATE 
                candidaturas
            SET
                pontuacao_aderencia = :nota,
                justificativa_ia = :sumario,
                status_triagem = 'Em Análise' 
            WHERE
                id_candidatura = :id_candidatura
        ";

        try {
            $stmt = $this->db_connection->prepare($sql);

            return $stmt->execute([
                ':nota' => $nota,
                ':sumario' => $sumario,
                ':id_candidatura' => $idCandidatura
            ]);
        } catch (\PDOException $e) {
            // Em ambiente de desenvolvimento, você pode logar o erro
            error_log("Erro ao atualizar resultado IA: " . $e->getMessage());
            return false;
        }
    }
    /**
     * Cria um novo registro de candidatura na tabela 'candidaturas'.
     *
     * @param int $idVaga O ID da vaga.
     * @param int $idCandidato O ID do candidato.
     * @return bool Retorna true em caso de sucesso, false em caso de falha.
     */
    public function criar(int $idVaga, int $idCandidato): bool
    {
        // Usando a tabela 'candidaturas' conforme planejamos
        $sql = "INSERT INTO candidaturas (id_vaga, id_candidato) VALUES (:id_vaga, :id_candidato)";
        $stmt = $this->db_connection->prepare($sql);

        return $stmt->execute([
            ':id_vaga' => $idVaga,
            ':id_candidato' => $idCandidato
        ]);
    }

    /**
     * Verifica se um candidato já se aplicou para uma determinada vaga.
     *
     * @param int $idVaga
     * @param int $idCandidato
     * @return bool Retorna true se a candidatura já existe, false caso contrário.
     */
    public function verificarExistente(int $idVaga, int $idCandidato): bool
    {
        $sql = "SELECT COUNT(*) FROM candidaturas WHERE id_vaga = :id_vaga AND id_candidato = :id_candidato";
        $stmt = $this->db_connection->prepare($sql);
        $stmt->execute([
            ':id_vaga' => $idVaga,
            ':id_candidato' => $idCandidato
        ]);

        return $stmt->fetchColumn() > 0;
    }
    public function buscarPorVaga(int $idVaga): array
    {
        // CORREÇÃO: Adicionados os campos 'id_candidatura' e 'pontuacao_aderencia' à consulta.
        $sql = "SELECT 
                    cand.id_candidatura,
                    cand.pontuacao_aderencia,
                    cand.justificativa_ia,
                    cand.data_candidatura,
                    cand.status_triagem,
                    c.nome_completo,
                    c.curriculo
                FROM 
                    candidaturas AS cand
                JOIN 
                    candidato AS c ON cand.id_candidato = c.id_candidato
                WHERE 
                    cand.id_vaga = :id_vaga
                ORDER BY 
                    cand.pontuacao_aderencia ASC";

        $stmt = $this->db_connection->prepare($sql);
        $stmt->execute([':id_vaga' => $idVaga]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function buscarPorId(int $idCandidatura)
    {
        $sql = "SELECT 
                    c.id_candidatura,
                    c.id_vaga,
                    cand.curriculo
                FROM 
                    candidaturas AS c
                JOIN
                    candidato AS cand ON c.id_candidato = cand.id_candidato
                WHERE 
                    c.id_candidatura = :id_candidatura
                LIMIT 1";

        $stmt = $this->db_connection->prepare($sql);
        $stmt->execute([':id_candidatura' => $idCandidatura]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

}