<?php
declare(strict_types=1);

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

    /**
     * Retorna todos os cargos existentes.
     * @return array
     */
    /** Retorna todos os cargos existentes. */
    public function findAll(): array
    {
        $stmt = $this->db_connection->prepare("SELECT id_cargo, nome_cargo, salario_base FROM cargo");
        $stmt->execute();
        return $stmt->fetchAll(
            \PDO::FETCH_ASSOC
        );
    }

}
