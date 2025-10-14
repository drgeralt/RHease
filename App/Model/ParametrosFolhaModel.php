<?php
// App/Model/ParametroFolhaModel.php

namespace App\Model;

use PDO;
use PDOException;

class ParametrosFolhaModel
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

    /**
     * Busca um parâmetro específico pelo seu nome.
     * @param string $nomeParametro O nome do parâmetro (ex: 'TABELA_INSS_VIGENTE')
     * @return object|false O registro do parâmetro ou false se não encontrado.
     */
    public function findParametroPorNome(string $nomeParametro)
    {
        // CORRIGIDO: Usa 'nome_parametro' e 'data_vigencia'
        $sql = "SELECT * FROM parametros_folha WHERE nome = :nomeParametro ORDER BY ano_vigencia DESC LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':nomeParametro', $nomeParametro, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ);
    }
}