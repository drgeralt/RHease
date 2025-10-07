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
                    c.pontuacao_aderencia,
                    c.justificativa_ia,
                    cand.nome_completo,
                    v.titulo_vaga,
                    v.descricao_vaga
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
    public function buscarComVaga(int $idCandidatura)
    {
        $sql = "SELECT 
                    c.id_candidatura,
                    cand.curriculo,
                    v.titulo_vaga,
                    v.descricao_vaga,
                    v.requisitos_necessarios,
                    v.requisitos_recomendados,
                    v.requisitos_desejados
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
        $sql = "UPDATE candidaturas 
                SET pontuacao_aderencia = :nota, justificativa_ia = :sumario 
                WHERE id_candidatura = :id_candidatura";

        $stmt = $this->db_connection->prepare($sql);

        return $stmt->execute([
            ':nota' => $nota,
            ':sumario' => $sumario,
            ':id_candidatura' => $idCandidatura
        ]);
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
        // CORREÇÃO: A consulta agora junta 'candidaturas' (alias c) com 'candidato' (alias cand)
        // e seleciona as colunas corretas de cada tabela.
        $sql = "SELECT 
                c.id_candidatura,
                c.pontuacao_aderencia,
                c.justificativa_ia,
                c.data_candidatura,
                c.status_triagem,
                cand.nome_completo,
                cand.curriculo
            FROM 
                candidaturas AS c
            JOIN 
                candidato AS cand ON c.id_candidato = cand.id_candidato
            WHERE 
                c.id_vaga = :id_vaga
            ORDER BY 
                c.pontuacao_aderencia DESC, c.data_candidatura DESC";

        $stmt = $this->db_connection->prepare($sql);
        $stmt->execute([':id_vaga' => $idVaga]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function buscarPorId(int $idCandidatura)
    {
        $sql = "SELECT 
                    c.pontuacao_aderencia,
                    c.justificativa_ia,
                    cand.nome_completo,
                    v.titulo_vaga,
                    v.descricao_vaga
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

}