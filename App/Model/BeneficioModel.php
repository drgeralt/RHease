<?php

namespace App\Model;
use PDO;
use PDOException;
use App\Core\Model;

class BeneficioModel extends Model{
    
    // --- Benefícios ---
    public function listarBeneficios() {
        $stmt = $this->db_connection->query("SELECT * FROM beneficios_catalogo ORDER BY nome ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBeneficio($id) {
        $stmt = $this->db_connection->prepare("SELECT * FROM beneficios_catalogo WHERE id_beneficio = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function criarBeneficio($nome, $categoria, $tipo_valor) {
        $stmt = $this->db_connection->prepare("INSERT INTO beneficios_catalogo (nome, categoria, tipo_valor, status) VALUES (:nome, :categoria, :tipo_valor, 'Ativo')");
        $stmt->bindValue(':nome', $nome);
        $stmt->bindValue(':categoria', $categoria);
        $stmt->bindValue(':tipo_valor', $tipo_valor);
        $stmt->execute();
        return $this->db_connection->lastInsertId();
    }

    public function editarBeneficio($id, $nome, $categoria, $tipo_valor, $status) {
        $stmt = $this->db_connection->prepare("UPDATE beneficios_catalogo SET nome = :nome, categoria = :categoria, tipo_valor = :tipo_valor, status = :status WHERE id_beneficio = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':nome', $nome);
        $stmt->bindValue(':categoria', $categoria);
        $stmt->bindValue(':tipo_valor', $tipo_valor);
        $stmt->bindValue(':status', $status);
        $stmt->execute();
        return true;
    }

    public function desativarBeneficio($id) {
        $stmt = $this->db_connection->prepare("UPDATE beneficios_catalogo SET status = 'Inativo' WHERE id_beneficio = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return true;
    }

    // --- Regras Automáticas ---
    public function listarRegras() {
        $stmt = $this->db_connection->query("
            SELECT rb.id_regra, rb.tipo_contrato, bc.nome AS beneficio
            FROM regras_beneficios rb
            JOIN beneficios_catalogo bc ON rb.id_beneficio = bc.id_beneficio
        ");
        $regras = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $regras[$row['tipo_contrato']][] = $row['beneficio'];
        }
        return $regras;
    }

    public function salvarRegras($tipoContrato, $beneficios) {
        // Remove regras antigas
        $stmt = $this->db_connection->prepare("DELETE FROM regras_beneficios WHERE tipo_contrato = :tipo");
        $stmt->bindValue(':tipo', $tipoContrato);
        $stmt->execute();

        // Insere novas
        $stmt = $this->db_connection->prepare("INSERT INTO regras_beneficios (tipo_contrato, id_beneficio) VALUES (:tipo, :id_beneficio)");
        foreach ($beneficios as $id_beneficio) {
            $stmt->bindValue(':tipo', $tipoContrato);
            $stmt->bindValue(':id_beneficio', $id_beneficio, PDO::PARAM_INT);
            $stmt->execute();
        }
    }

    public function listarTodosBeneficios() {
        $stmt = $this->db_connection->query("SELECT * FROM beneficios_catalogo WHERE status = 'Ativo' ORDER BY nome ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function atualizarStatus($id, $novo_status)
    {
        // Garante que o status é um valor permitido
        if (!in_array($novo_status, ['Ativo', 'Inativo'])) {
            return false;
        }

        // Prepara a query de UPDATE
        $stmt = $this->db_connection->prepare("UPDATE beneficios_catalogo SET status=? WHERE id_beneficio=?");
        
        // Binda os parâmetros
        // 's' para string (status), 'i' para integer (id)
        $stmt->bind_param('si', $novo_status, $id);
        
        // Executa e verifica o resultado
        $resultado = $stmt->execute();
        
        $stmt->close();
        
        return $resultado;
    }

}
?>
