<?php

namespace App\Model;

use App\Core\Model;

class SetorModel extends Model
{
    public function findOrCreateByName(string $nomeSetor): string
    {
        $stmt = $this->db_connection->prepare("SELECT id_setor FROM setor WHERE nome_setor = :nome");
        $stmt->execute([':nome' => $nomeSetor]);
        $result = $stmt->fetch();

        if ($result) {
            return $result['id_setor'];
        }

        // 3. Se não encontrou, cria um novo
        $stmt = $this->db_connection->prepare("INSERT INTO setor (nome_setor) VALUES (:nome)");
        $stmt->execute([':nome' => $nomeSetor]);

        return $this->db_connection->lastInsertId();
    }
}