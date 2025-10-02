<?php

namespace App\Model;
use PDO;
use PDOException;
use App\Core\Model;

class NovaVagaModel extends Model
{
    protected string $table = 'vaga'; // Nome da tabela no banco de dados

     public function criarVaga(array $dados)
    {
        // A instrução SQL INSERT com placeholders para segurança
        $query = "INSERT INTO {$this->table} 
                    (titulo_vaga, id_setor, situacao, requisitos) -- Adicione outras colunas conforme necessário
                  VALUES 
                    (:titulo_vaga, :id_setor, :situacao, :requisitos)";
        
        try {
            $stmt = $this->db_connection->prepare($query);

            // Vincula os valores do array aos placeholders da query
            $stmt->bindValue(':titulo_vaga', $dados['titulo_vaga']);
            $stmt->bindValue(':id_setor', $dados['id_setor']);
            $stmt->bindValue(':situacao', $dados['situacao']);
            $stmt->bindValue(':requisitos', $dados['requisitos']);
            // ... vincule outros campos aqui ...

            $stmt->execute();
            
            // Retorna o ID da última linha inserida, muito útil para redirecionamentos
            return $this->db_connection->lastInsertId();

        } catch (PDOException $e) {
            // Em um sistema real, você logaria o erro em vez de exibi-lo
            error_log($e->getMessage());
            return false;
        }
    }
}
