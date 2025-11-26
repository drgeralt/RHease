<?php

namespace App\Model;

use App\Core\Model;
use PDO;

class GestaoVagasModel extends Model
{
    // Método para o GESTOR (Vê tudo: rascunho, fechada, aberta)
    public function listarTodas(): array
    {
        // CORREÇÃO: Nome da tabela 'vaga' e 'setor'
        $sql = "SELECT 
                    v.id_vaga, 
                    v.titulo_vaga AS titulo, 
                    IFNULL(s.nome_setor, 'Geral') AS departamento, 
                    v.descricao_vaga AS descricao, 
                    v.situacao AS status, 
                    v.requisitos_necessarios AS skills_necessarias
                FROM vaga v
                LEFT JOIN setor s ON v.id_setor = s.id_setor
                ORDER BY v.id_vaga DESC";

        $stmt = $this->db_connection->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- NOVO MÉTODO ADICIONADO PARA O CANDIDATO ---
    public function listarVagasAbertas(): array
    {
        $sql = "SELECT 
                    v.id_vaga, 
                    v.titulo_vaga AS titulo, 
                    IFNULL(s.nome_setor, 'Geral') AS departamento, 
                    v.descricao_vaga AS descricao, 
                    v.situacao,
                    v.requisitos_necessarios
                FROM vaga v
                LEFT JOIN setor s ON v.id_setor = s.id_setor
                WHERE v.situacao = 'aberta'
                ORDER BY v.id_vaga DESC";

        $stmt = $this->db_connection->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // -----------------------------------------------

    public function buscarPorId(int $id)
    {
        $sql = "SELECT 
                    v.id_vaga, 
                    v.titulo_vaga AS titulo_vaga, -- Alias ajustado para compatibilidade
                    v.titulo_vaga AS titulo,      -- Mantido para compatibilidade com gestor
                    IFNULL(s.nome_setor, 'Geral') AS departamento, 
                    v.descricao_vaga, 
                    v.descricao_vaga AS descricao,
                    v.situacao AS status,
                    v.situacao,
                    v.requisitos_necessarios,
                    v.requisitos_necessarios AS skills_necessarias,
                    v.requisitos_recomendados AS skills_recomendadas,
                    v.requisitos_desejados AS skills_desejadas
                FROM vaga v
                LEFT JOIN setor s ON v.id_setor = s.id_setor
                WHERE v.id_vaga = :id";

        $stmt = $this->db_connection->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function criar(array $dados): bool
    {
        $idSetor = $this->obterIdSetor($dados['departamento']);

        $sql = "INSERT INTO vaga (
                    titulo_vaga, id_setor, descricao_vaga, situacao, 
                    requisitos_necessarios, requisitos_recomendados, requisitos_desejados, data_criacao
                ) VALUES (
                    :tit, :dep, :desc, :stat, :req1, :req2, :req3, NOW()
                )";

        $stmt = $this->db_connection->prepare($sql);
        return $stmt->execute([
            ':tit' => $dados['titulo'],
            ':dep' => $idSetor,
            ':desc' => $dados['descricao'],
            ':stat' => $dados['status'],
            ':req1' => $dados['skills_necessarias'],
            ':req2' => $dados['skills_recomendadas'],
            ':req3' => $dados['skills_desejadas']
        ]);
    }

    public function atualizar(int $id, array $dados): bool
    {
        $idSetor = $this->obterIdSetor($dados['departamento']);

        $sql = "UPDATE vaga SET 
                titulo_vaga = :tit, 
                id_setor = :dep, 
                descricao_vaga = :desc, 
                situacao = :stat, 
                requisitos_necessarios = :req1, 
                requisitos_recomendados = :req2, 
                requisitos_desejados = :req3
                WHERE id_vaga = :id";

        $stmt = $this->db_connection->prepare($sql);
        return $stmt->execute([
            ':tit' => $dados['titulo'],
            ':dep' => $idSetor,
            ':desc' => $dados['descricao'],
            ':stat' => $dados['status'],
            ':req1' => $dados['skills_necessarias'],
            ':req2' => $dados['skills_recomendadas'],
            ':req3' => $dados['skills_desejadas'],
            ':id' => $id
        ]);
    }

    public function excluir(int $id): bool
    {
        try {
            $this->db_connection->prepare("DELETE FROM candidaturas WHERE id_vaga = :id")->execute([':id' => $id]);
        } catch (\Exception $e) {}

        $sql = "DELETE FROM vaga WHERE id_vaga = :id";
        $stmt = $this->db_connection->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function listarCandidatos(int $idVaga): array
    {
        $sql = "SELECT 
                    c.id_candidatura, 
                    cand.nome_completo, 
                    c.data_candidatura, 
                    c.pontuacao_aderencia, 
                    c.status_triagem, 
                    cand.curriculo AS caminho_curriculo
                FROM candidaturas c
                JOIN candidato cand ON c.id_candidato = cand.id_candidato
                WHERE c.id_vaga = :id
                ORDER BY c.pontuacao_aderencia DESC";

        $stmt = $this->db_connection->prepare($sql);
        $stmt->execute([':id' => $idVaga]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function obterIdSetor(string $nomeSetor): int
    {
        $nomeSetor = trim($nomeSetor);
        if (empty($nomeSetor)) return 0;

        $stmt = $this->db_connection->prepare("SELECT id_setor FROM setor WHERE nome_setor = :nome");
        $stmt->execute([':nome' => $nomeSetor]);
        $id = $stmt->fetchColumn();

        if ($id) return (int)$id;

        $stmt = $this->db_connection->prepare("INSERT INTO setor (nome_setor) VALUES (:nome)");
        $stmt->execute([':nome' => $nomeSetor]);

        return (int)$this->db_connection->lastInsertId();
    }
}