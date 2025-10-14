<?php

namespace App\Model;

use App\Core\Model;

class EnderecoModel extends Model
{
    public function create(array $data): string
    {
        $query = "INSERT INTO endereco (logradouro, CEP, numero, bairro, cidade, estado)
                    VALUES (:logradouro, :CEP, :numero, :bairro, :cidade, :estado)";

        $stmt = $this->db_connection->prepare($query);

        $stmt->bindValue(':logradouro', $data['logradouro']);
        $stmt->bindValue(':CEP', $data['CEP']);
        $stmt->bindValue(':numero', $data['numero']);
        $stmt->bindValue(':bairro', $data['bairro']);
        $stmt->bindValue(':cidade', $data['cidade']);
        $stmt->bindValue(':estado', $data['estado']);

        $stmt->execute();

        return $this->db_connection->lastInsertId();
    }
}