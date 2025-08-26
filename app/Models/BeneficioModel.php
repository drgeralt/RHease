<?php

require_once BASE_PATH . '/app/Core/Model.php';

class BeneficioModel extends Model
{
    

    public function listarTodosBeneficios()
    {
        $sql = "SELECT * FROM beneficios ORDER BY nome_beneficio";
        try {
            $stmt = $this->db_connection->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao listar benefícios: " . $e->getMessage());
            return [];
        }
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM beneficios WHERE id = :id";
        try {
            $stmt = $this->db_connection->prepare($sql);
            $stmt->execute(['id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao buscar benefício por ID: " . $e->getMessage());
            return null;
        }
    }

    public function create($dados)
    {
        $sql = "INSERT INTO beneficios (nome_beneficio, descricao, valor_mensal, tipo) VALUES (:nome, :descricao, :valor, :tipo)";
        try {
            $stmt = $this->db_connection->prepare($sql);
            $stmt->bindParam(':nome', $dados['nome_beneficio']);
            $stmt->bindParam(':descricao', $dados['descricao']);
            $stmt->bindParam(':valor', $dados['valor_mensal']);
            $stmt->bindParam(':tipo', $dados['tipo']);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao criar benefício: " . $e->getMessage());
            return false;
        }
    }

    public function update($id, $dados)
    {
        $sql = "UPDATE beneficios SET nome_beneficio = :nome, descricao = :descricao, valor_mensal = :valor, tipo = :tipo WHERE id = :id";
        try {
            $stmt = $this->db_connection->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':nome', $dados['nome_beneficio']);
            $stmt->bindParam(':descricao', $dados['descricao']);
            $stmt->bindParam(':valor', $dados['valor_mensal']);
            $stmt->bindParam(':tipo', $dados['tipo']);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao atualizar benefício: " . $e->getMessage());
            return false;
        }
    }

    public function delete($id)
    {
        $sql = "DELETE FROM beneficios WHERE id = :id";
        try {
            $stmt = $this->db_connection->prepare($sql);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao deletar benefício: " . $e->getMessage());
            return false;
        }
    }
}