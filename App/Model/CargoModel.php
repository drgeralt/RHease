<?php
declare(strict_types=1);

namespace App\Model;

use App\Core\Model;
use PDO;

class CargoModel extends Model
{
    public function findOrCreateByName(string $nomeCargo, float $salarioBase): int
    {
        $stmt = $this->db_connection->prepare("SELECT id_cargo FROM cargo WHERE nome_cargo = :nome");
        $stmt->execute([':nome' => $nomeCargo]);
        $result = $stmt->fetch();

        if ($result) {
            return (int)$result['id_cargo'];
        }

        $stmt = $this->db_connection->prepare("INSERT INTO cargo (nome_cargo, salario_base) VALUES (:nome, :salario)");
        $stmt->execute([':nome' => $nomeCargo, ':salario' => $salarioBase]);

        return (int)$this->db_connection->lastInsertId();
    }

    public function findAll(): array
    {
        $stmt = $this->db_connection->prepare("SELECT id_cargo, nome_cargo, salario_base FROM cargo");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $id)
    {
        $sql = "SELECT * FROM cargo WHERE id_cargo = :id";
        $stmt = $this->db_connection->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}