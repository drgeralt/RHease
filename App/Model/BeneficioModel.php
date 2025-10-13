<?php
namespace App\Model;
use PDO;
use PDOException;

class BeneficioModel {
    private $pdo;

    public function __construct() {
        $host = 'localhost';
        $user = 'root';
        $password = '';
        $database = 'rhease';

        try {
            $this->pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $user, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erro ao conectar ao banco: " . $e->getMessage());
        }
    }

    // --- Benefícios ---
    public function listarBeneficios() {
        $stmt = $this->pdo->query("SELECT * FROM beneficios_catalogo ORDER BY nome ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBeneficio($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM beneficios_catalogo WHERE id_beneficio = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function criarBeneficio($nome, $categoria, $tipo_valor) {
        $stmt = $this->pdo->prepare("INSERT INTO beneficios_catalogo (nome, categoria, tipo_valor, status) VALUES (:nome, :categoria, :tipo_valor, 'Ativo')");
        $stmt->bindValue(':nome', $nome);
        $stmt->bindValue(':categoria', $categoria);
        $stmt->bindValue(':tipo_valor', $tipo_valor);
        $stmt->execute();
        return $this->pdo->lastInsertId();
    }

    public function editarBeneficio($id, $nome, $categoria, $tipo_valor, $status) {
        $stmt = $this->pdo->prepare("UPDATE beneficios_catalogo SET nome = :nome, categoria = :categoria, tipo_valor = :tipo_valor, status = :status WHERE id_beneficio = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':nome', $nome);
        $stmt->bindValue(':categoria', $categoria);
        $stmt->bindValue(':tipo_valor', $tipo_valor);
        $stmt->bindValue(':status', $status);
        $stmt->execute();
        return true;
    }

    public function desativarBeneficio($id) {
        $stmt = $this->pdo->prepare("UPDATE beneficios_catalogo SET status = 'Inativo' WHERE id_beneficio = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return true;
    }

    // --- Regras Automáticas ---
    public function listarRegras() {
        $stmt = $this->pdo->query("
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
        $stmt = $this->pdo->prepare("DELETE FROM regras_beneficios WHERE tipo_contrato = :tipo");
        $stmt->bindValue(':tipo', $tipoContrato);
        $stmt->execute();

        // Insere novas
        $stmt = $this->pdo->prepare("INSERT INTO regras_beneficios (tipo_contrato, id_beneficio) VALUES (:tipo, :id_beneficio)");
        foreach ($beneficios as $id_beneficio) {
            $stmt->bindValue(':tipo', $tipoContrato);
            $stmt->bindValue(':id_beneficio', $id_beneficio, PDO::PARAM_INT);
            $stmt->execute();
        }
    }

    public function listarTodosBeneficios() {
        $stmt = $this->pdo->query("SELECT * FROM beneficios_catalogo WHERE status = 'Ativo' ORDER BY nome ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
