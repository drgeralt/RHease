<?php
// App/Model/ParametrosFolhaModel.php

namespace App\Model;

use App\Core\Model;
use PDO;
use PDOException;

class ParametrosFolhaModel extends Model
{
    /**
     * ✅ CORRIGIDO: O construtor foi adicionado para seguir o padrão da nossa arquitetura.
     * Ele recebe a conexão e a passa para a classe pai.
     */
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
    }

    /**
     * Busca um parâmetro específico pela sua chave.
     * @param string $chave O nome do parâmetro (ex: 'TABELA_INSS_VIGENTE')
     * @return object|false O registro do parâmetro ou false se não encontrado.
     */
    // ✅ CORRIGIDO: Nome do método ajustado para 'findParametroPorChave' com 'C' maiúsculo.
    public function findParametroPorChave(string $chave)
    {
        // A coluna no banco de dados se chama 'nome', e não 'chave'.
        // A consulta SQL foi ajustada para usar 'WHERE nome = :chave'.
        $sql = "SELECT * FROM parametros_folha WHERE nome = :chave ORDER BY ano_vigencia DESC LIMIT 1";

        $stmt = $this->db_connection->prepare($sql);

        // O bindParam continua correto, pois liga o placeholder ':chave' à variável $chave.
        $stmt->bindParam(':chave', $chave, PDO::PARAM_STR);
        $stmt->execute();

        // Usamos fetch(PDO::FETCH_OBJ) para retornar um objeto, como o service espera.
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
    /**
     * ✅ NOVO MÉTODO ADAPTADO: Busca todos os parâmetros que começam com um prefixo.
     * Usaremos isso para pegar todas as faixas de INSS e IRRF de uma vez.
     *
     * @param string $prefixo O prefixo a ser buscado (ex: 'INSS_FAIXA_')
     * @return array Uma lista de parâmetros encontrados.
     */
    public function findFaixasPorPrefixo(string $prefixo): array
    {
        // a ordenação alfabética funcionará corretamente para as faixas.
        $sql = "SELECT * FROM parametros_folha WHERE nome LIKE :prefixo ORDER BY nome ASC";

        $stmt = $this->db_connection->prepare($sql);
        $stmt->bindValue(':prefixo', $prefixo . '%', PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

}