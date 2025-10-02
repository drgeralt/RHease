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
        // Esta query agora reflete os campos do seu novo formulário
        $query = "INSERT INTO {$this->table} 
                    (titulo_vaga, id_setor, situacao, descricao, requisitos, skills_recomendadas, skills_desejadas)
                  VALUES 
                    (:titulo_vaga, :id_setor, :situacao, :descricao, :requisitos, :skills_recomendadas, :skills_desejadas)";
        
        $stmt = $this->db_connection->prepare($query);

        // Passa o array de dados diretamente para o execute()
        // As chaves do array devem corresponder aos placeholders na query
        $stmt->execute([
            ':titulo_vaga' => $dados['titulo_vaga'],
            ':id_setor' => $dados['id_setor'],
            ':situacao' => $dados['status_vaga'], // O campo 'situacao' no banco recebe o valor de 'status_vaga' do form
            ':descricao' => $dados['descricao'],
            ':requisitos' => $dados['skills_necessarias'], // O campo 'requisitos' no banco recebe as skills necessárias
            ':skills_recomendadas' => $dados['skills_recomendadas'],
            ':skills_desejadas' => $dados['skills_desejadas']
        ]);

        return $this->db_connection->lastInsertId();
    }
}