<?php

require_once BASE_PATH . '/app/Core/Model.php';

class FuncionarioModel extends Model
{
    

    public function getById($id)
    {
        $sql = "SELECT * FROM funcionarios WHERE id = :id";
        try {
            $stmt = $this->db_connection->prepare($sql);
            $stmt->execute(['id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao buscar funcionário por ID: " . $e->getMessage());
            return null;
        }
    }

    public function getAllAtivos()
    {
        $sql = "SELECT id, nome_completo, cargo FROM funcionarios WHERE status = 'ativo' ORDER BY nome_completo ASC";
        try {
            $stmt = $this->db_connection->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao buscar funcionários ativos: " . $e->getMessage());
            return [];
        }
    }

    public function updateStatus($id, $status)
    {
        $sql = "UPDATE funcionarios SET status = :status WHERE id = :id";
        try {
            $stmt = $this->db_connection->prepare($sql);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao atualizar status do funcionário: " . $e->getMessage());
            return false;
        }
    }

    public function getAllInativos()
    {
        $sql = "SELECT id, nome_completo, cargo, data_admissao FROM funcionarios WHERE status = 'inativo' ORDER BY nome_completo ASC";
        try {
            $stmt = $this->db_connection->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao buscar funcionários inativos: " . $e->getMessage());
            return [];
        }
    }
}