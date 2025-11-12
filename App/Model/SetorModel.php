<?php
declare(strict_types=1);

namespace App\Model;

use App\Core\Model;
use PDO;

class SetorModel extends Model
{
    public function findOrCreateByName(string $nomeSetor): int
    {
        $stmt = $this->db_connection->prepare("SELECT id_setor FROM setor WHERE nome_setor = :nome");
        $stmt->execute([':nome' => $nomeSetor]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return (int)$result['id_setor'];
        }

        $stmt = $this->db_connection->prepare("INSERT INTO setor (nome_setor) VALUES (:nome)");
        $stmt->execute([':nome' => $nomeSetor]);

        return (int)$this->db_connection->lastInsertId();
    }
}