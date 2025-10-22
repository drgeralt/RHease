<?php
// App/Model/FolhaPagamentoModel.php

namespace App\Model;

use App\Core\Model;
use PDO;
use Exception;

class FolhaPagamentoModel extends Model
{
    /**
     * O construtor agora é simples: apenas recebe a conexão PDO
     * e a passa para a classe Model pai, que a armazena em $this->db_connection.
     *
     * @param PDO $pdo A instância da conexão com o banco de dados.
     */
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
    }

    /**
     * Salva o registro principal do holerite na tabela 'holerites'.
     *
     * @param int $colaboradorId ID do colaborador.
     * @param int $ano Ano de referência.
     * @param int $mes Mês de referência.
     * @param array $dadosCalculados Um array contendo todos os totais calculados pelo Service.
     * @return string O ID do holerite recém-inserido.
     */
    public function salvarHolerite(int $colaboradorId, int $ano, int $mes, array $dadosCalculados): string
    {
        $sql = "INSERT INTO holerites (
                    id_colaborador, mes_referencia, ano_referencia, data_processamento, 
                    total_proventos, total_descontos, salario_liquido, 
                    base_calculo_inss, base_calculo_fgts, valor_fgts, base_calculo_irrf
                ) VALUES (?, ?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?)";

        // Usamos $this->db_connection, que foi herdado da classe Model pai
        $stmt = $this->db_connection->prepare($sql);

        $stmt->execute([
            $colaboradorId,
            $mes,
            $ano,
            $dadosCalculados['total_proventos'],
            $dadosCalculados['total_descontos'],
            $dadosCalculados['salario_liquido'],
            $dadosCalculados['base_inss'],
            $dadosCalculados['base_fgts'],
            $dadosCalculados['valor_fgts'],
            $dadosCalculados['base_irrf']
        ]);

        return $this->db_connection->lastInsertId();
    }

    /**
     * Salva os itens individuais (proventos e descontos) na tabela 'holerite_itens'.
     *
     * @param int $holeriteId O ID do holerite ao qual os itens pertencem.
     * @param array $itens Um array de itens, cada um contendo 'codigo', 'descricao', 'tipo', 'valor'.
     */
    public function salvarItens(int $holeriteId, array $itens): void
    {
        $sql = "INSERT INTO holerite_itens (id_holerite, codigo_evento, descricao, tipo, valor) 
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->db_connection->prepare($sql);

        foreach ($itens as $item) {
            $stmt->execute([
                $holeriteId,
                $item['codigo'],
                $item['descricao'],
                $item['tipo'],
                $item['valor']
            ]);
        }
    }

    /**
     * Remove qualquer holerite existente para um colaborador em um determinado período.
     * Isso é crucial para permitir que o RH reprocesse a folha sem criar dados duplicados.
     *
     * @param int $colaboradorId ID do colaborador.
     * @param int $ano Ano de referência.
     * @param int $mes Mês de referência.
     */
    public function limparHoleriteAnterior(int $colaboradorId, int $ano, int $mes): void
    {
        // A tabela holerite_itens tem ON DELETE CASCADE, então apagar o holerite principal
        // já remove os itens associados automaticamente.
        $sql = "DELETE FROM holerites 
                WHERE id_colaborador = ? AND ano_referencia = ? AND mes_referencia = ?";

        $stmt = $this->db_connection->prepare($sql);
        $stmt->execute([$colaboradorId, $ano, $mes]);
    }
}