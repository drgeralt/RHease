<?php
// App/Model/HoleriteModel.php

namespace App\Model;

use PDO;
use PDOException;

class HoleriteModel
{
    private $pdo;

    public function __construct()
    {
        $host = 'localhost';
        $user = 'root';
        $password = '';
        $database = 'rhease';

        try {
            $this->pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $user, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erro ao conectar ao banco de dados: " . $e->getMessage());
        }
    }


    public function findByColaboradorId(int $colaboradorId): array
    {
        $sql = "SELECT id_holerite, mes_referencia, ano_referencia, data_processamento, salario_liquido 
                FROM holerites 
                WHERE id_colaborador = :colaboradorId 
                ORDER BY ano_referencia DESC, mes_referencia DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':colaboradorId', $colaboradorId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Busca os dados principais do holerite e do colaborador
    public function findHoleriteCompletoById(int $holeriteId)
    {
        $sql = "SELECT h.*, c.nome_completo, c.CPF, c.matricula, ca.nome_cargo, s.nome_setor
                FROM holerites h
                JOIN colaborador c ON h.id_colaborador = c.id_colaborador
                LEFT JOIN cargo ca ON c.id_cargo = ca.id_cargo
                LEFT JOIN setor s ON c.id_setor = s.id_setor
                WHERE h.id_holerite = :holeriteId";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':holeriteId', $holeriteId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    // --- NOVO MÉTODO 2 ---
    // Busca a lista de proventos e descontos de um holerite específico
    public function findItensByHoleriteId(int $holeriteId): array
    {
        $sql = "SELECT * FROM holerite_itens WHERE id_holerite = :holeriteId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':holeriteId', $holeriteId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}