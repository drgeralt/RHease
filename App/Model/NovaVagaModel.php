<?php

declare(strict_types=1);

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
                    (titulo_vaga, descricao_vaga, id_setor, situacao, requisitos_necessarios, requisitos_recomendados, requisitos_desejados, id_cargo)
                  VALUES 
                    (:titulo_vaga, :descricao_vaga, :id_setor, :situacao, :requisitos_necessarios, :requisitos_recomendados, :requisitos_desejados, :id_cargo)";

    $stmt = $this->db_connection->prepare($query);

    // Normaliza valores opcionais
    $idCargo = isset($dados['id_cargo']) ? $dados['id_cargo'] : null;

    // As chaves do array devem corresponder aos placeholders na query
    $stmt->execute([
        ':titulo_vaga' => $dados['titulo_vaga'],
        ':descricao_vaga' => $dados['descricao_vaga'],
        ':id_setor' => $dados['id_setor'],
        ':situacao' => $dados['situacao'],
        ':requisitos_necessarios' => $dados['requisitos_necessarios'],
        ':requisitos_recomendados' => $dados['requisitos_recomendados'],
        ':requisitos_desejados' => $dados['requisitos_desejados'],
        ':id_cargo' => $dados['id_cargo'],
    ]);

    return $this->db_connection->lastInsertId();
  }
}