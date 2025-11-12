<?php
declare(strict_types=1);

namespace App\Model;

use App\Core\Model;
use PDO;

class EnderecoModel extends Model
{
    public function create(array $data): int
    {
        $query = "INSERT INTO endereco (logradouro, CEP, numero, bairro, cidade, estado)
                    VALUES (:logradouro, :CEP, :numero, :bairro, :cidade, :estado)";

        $stmt = $this->db_connection->prepare($query);

        $stmt->bindValue(':logradouro', $data['logradouro'] ?? null);
        $stmt->bindValue(':CEP', $data['CEP'] ?? null);
        $stmt->bindValue(':numero', $data['numero'] ?? null);
        $stmt->bindValue(':bairro', $data['bairro'] ?? null);
        $stmt->bindValue(':cidade', $data['cidade'] ?? null);
        $stmt->bindValue(':estado', $data['estado'] ?? null);

        $stmt->execute();

        return (int)$this->db_connection->lastInsertId();
    }
}