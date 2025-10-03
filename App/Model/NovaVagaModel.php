<?php

namespace App\Model;
use PDO;
use PDOException;
use App\Core\Model;

class NovaVagaModel extends Model
{
    protected string $table = 'vaga'; // Nome da tabela no banco de dados

      public function criarVaga(array $dados): string
    {
        // query referente aos campos da tabela vaga e do forms
        $query = "INSERT INTO {$this->table} 
                    (titulo_vaga, requisitos, situacao, id_setor, id_cargo)
                  VALUES 
                    (:titulo_vaga, :requisitos, :situacao, :id_setor, :id_cargo)";

        $stmt = $this->db_connection->prepare($query);

        // As chaves do array devem corresponder aos placeholders na query
        $stmt->execute([
            ':titulo' => $dados['titulo_vaga'],
            ':id_setor' => $dados['id_setor'], // O campo 'id_setor' no banco recebe o ID do setor 
            ':id_cargo' => $dados['id_cargo'], 
            ':situacao' => $dados['situacao'], // O campo 'situacao' no banco recebe o valor de 'status do forms
            ':requisitos' => $dados['requisitos'], // O campo 'requisitos' no banco recebe as skills necessárias
            //':skills_recomendadas' => $dados['skills_recomendadas'],
            //':skills_desejadas' => $dados['skills_desejadas'], 
            //':descricao' => $dados['descricao']
        ]);

        return $this->db_connection->lastInsertId();
    }
}