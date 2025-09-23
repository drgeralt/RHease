<?php

namespace App\Model;

use App\Core\Model;

class CargoModel extends Model
{
    public function findOrCreateByName(string $nomeCargo): string
    {
        $stmt = $this->db_connection->prepare("SELECT id_cargo FROM cargo WHERE nome_cargo = :nome");
        $stmt->execute([':nome' => $nomeCargo]);
        $result = $stmt->fetch();

        if ($result) {
            return $result['id_cargo'];
        }

        // 3. Se não encontrou, cria um novo
        $stmt = $this->db_connection->prepare("INSERT INTO cargo (nome_cargo, salario_base) VALUES (:nome, 0.00)");
        $stmt->execute([':nome' => $nomeCargo]);

        return $this->db_connection->lastInsertId();
    }

}