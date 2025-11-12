<?php
declare(strict_types=1);

namespace App\Model;
use PDO;
use App\Core\Model;

class NovaVagaModel extends Model
{
    protected string $table = 'vaga';

    public function criarVaga(array $dados): int
    {
        $query = "INSERT INTO {$this->table} 
                    (titulo_vaga, descricao_vaga, id_setor, situacao, requisitos_necessarios, requisitos_recomendados, requisitos_desejados, id_cargo)
                  VALUES 
                    (:titulo_vaga, :descricao_vaga, :id_setor, :situacao, :requisitos_necessarios, :requisitos_recomendados, :requisitos_desejados, :id_cargo)";

        $stmt = $this->db_connection->prepare($query);

        $stmt->execute([
            ':titulo_vaga' => $dados['titulo_vaga'] ?? null,
            ':descricao_vaga' => $dados['descricao_vaga'] ?? null,
            ':id_setor' => $dados['id_setor'] ?? null,
            ':situacao' => $dados['situacao'] ?? null,
            ':requisitos_necessarios' => $dados['requisitos_necessarios'] ?? null,
            ':requisitos_recomendados' => $dados['requisitos_recomendados'] ?? null,
            ':requisitos_desejados' => $dados['requisitos_desejados'] ?? null,
            ':id_cargo' => $dados['id_cargo'] ?? null,
        ]);

        return (int)$this->db_connection->lastInsertId();
    }
}