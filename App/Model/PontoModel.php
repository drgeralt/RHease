<?php

namespace App\Model;

use App\Core\Database;
use PDO;

class PontoModel
{
    /**
     * Procura pelo último registo de ponto em aberto
     * para um colaborador no dia atual.
     *
     * @param int $idColaborador O ID do colaborador.
     * @return array|false Retorna os dados do ponto se encontrado, ou false caso contrário.
     */
    public function getUltimoPontoAberto(int $idColaborador, string $dataAtual)
    {
        $pdo = Database::getInstance();

        $sql = "SELECT data_hora_entrada FROM folha_ponto 
                WHERE id_colaborador = :id_colaborador 
                AND DATE(data_hora_entrada) = :data_atual 
                AND data_hora_saida IS NULL 
                ORDER BY data_hora_entrada DESC LIMIT 1";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id_colaborador' => $idColaborador,
            ':data_atual' => $dataAtual
        ]);
        return $stmt->fetch();
    }

    /**
     * Lógica para registar a entrada ou saída.
     */
    public function registrarPonto(int $idColaborador, string $dataHoraAtual): string
    {
        $pdo = Database::getInstance();
        $dataAtual = date('Y-m-d', strtotime($dataHoraAtual));

        $sqlBusca = "SELECT id_registro_ponto FROM folha_ponto 
                     WHERE id_colaborador = :id_colaborador 
                     AND DATE(data_hora_entrada) = :data_atual 
                     AND data_hora_saida IS NULL 
                     ORDER BY data_hora_entrada DESC LIMIT 1";

        $stmtBusca = $pdo->prepare($sqlBusca);
        $stmtBusca->execute([
            ':id_colaborador' => $idColaborador,
            ':data_atual' => $dataAtual
        ]);
        $registroAberto = $stmtBusca->fetch();

        if ($registroAberto) {
            $sql = "UPDATE folha_ponto SET data_hora_saida = :data_hora
                    WHERE id_registro_ponto = :id_registro";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':data_hora' => $dataHoraAtual,
                ':id_registro' => $registroAberto['id_registro_ponto']
            ]);
            return 'saida';
        }
        else {
            $sql = "INSERT INTO folha_ponto (id_colaborador, data_hora_entrada) 
                    VALUES (:id_colaborador, :data_hora)";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':id_colaborador' => $idColaborador,
                ':data_hora' => $dataHoraAtual
            ]);
            return 'entrada';
        }
    }
    /**
     * Busca todos os registos de ponto completos (com entrada e saída)
     * de um colaborador para um mês e ano específicos.
     *
     * @param int $idColaborador O ID do colaborador.
     * @param int $mes O mês de referência (1-12).
     * @param int $ano O ano de referência.
     * @return array Retorna um array com todos os registos encontrados.
     */
    public function getRegistrosCompletosDoMes(int $idColaborador, int $mes, int $ano): array
    {
        $pdo = Database::getInstance();
        $sql = "SELECT data_hora_entrada, data_hora_saida 
                FROM folha_ponto 
                WHERE id_colaborador = :id_colaborador 
                AND MONTH(data_hora_entrada) = :mes 
                AND YEAR(data_hora_entrada) = :ano
                AND data_hora_saida IS NOT NULL";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id_colaborador' => $idColaborador,
            ':mes' => $mes,
            ':ano' => $ano
        ]);
        return $stmt->fetchAll();
    }
}