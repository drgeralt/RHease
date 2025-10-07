<?php

namespace App\Model;

use App\Core\Database;
use PDO;

class PontoModel
{
    /**
     * NOVO MÉTODO: Procura pelo último registo de ponto em aberto
     * para um colaborador no dia atual.
     *
     * @param int $idColaborador O ID do colaborador.
     * @return array|false Retorna os dados do ponto se encontrado, ou false caso contrário.
     */
    public function getUltimoPontoAberto(int $idColaborador, string $dataAtual)
    {
        $pdo = Database::getInstance();

        // --- MODIFICADO AQUI: Usamos um placeholder para a data atual ---
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
     * (Este método permanece exatamente igual ao que já temos)
     */
    public function registrarPonto(int $idColaborador, string $dataHoraAtual): string
    {
        $pdo = Database::getInstance();
        $dataAtual = date('Y-m-d', strtotime($dataHoraAtual));

        // --- MODIFICADO AQUI: A query de busca também usa o placeholder ---
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

        // Se encontrou um registo, é uma SAÍDA.
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
        // Se não, é uma ENTRADA.
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
}