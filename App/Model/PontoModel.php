<?php

namespace App\Model;

use App\Core\Model;
use PDO;

class PontoModel extends Model
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
        $sql = "SELECT data_hora_entrada FROM folha_ponto 
                WHERE id_colaborador = :id_colaborador 
                AND DATE(data_hora_entrada) = :data_atual 
                AND data_hora_saida IS NULL 
                ORDER BY data_hora_entrada DESC LIMIT 1";

        $stmt = $this->db_connection->prepare($sql);
        $stmt->execute([
            ':id_colaborador' => $idColaborador,
            ':data_atual' => $dataAtual
        ]);
        return $stmt->fetch();
    }

    public function registrarPonto(int $idColaborador, string $dataHoraAtual, string $geolocalizacao, string $caminhoFoto, string $ipAddress): string
    {
        $dataAtual = date('Y-m-d', strtotime($dataHoraAtual));

        $sqlBusca = "SELECT id_registro_ponto FROM folha_ponto 
                     WHERE id_colaborador = :id_colaborador 
                     AND DATE(data_hora_entrada) = :data_atual 
                     AND data_hora_saida IS NULL 
                     ORDER BY data_hora_entrada DESC LIMIT 1";

        $stmtBusca = $this->db_connection->prepare($sqlBusca);
        $stmtBusca->execute([
            ':id_colaborador' => $idColaborador,
            ':data_atual' => $dataAtual
        ]);
        $registroAberto = $stmtBusca->fetch();

        if ($registroAberto) {
            $sql = "UPDATE folha_ponto SET data_hora_saida = :data_hora,
                    geolocalizacao = :geolocalizacao,
                    caminho_foto = :caminho_foto,
                    ip_address = :ip_address
                    WHERE id_registro_ponto = :id_registro";

            $stmt = $this->db_connection->prepare($sql);
            $stmt->execute([
                ':data_hora' => $dataHoraAtual,
                ':geolocalizacao' => $geolocalizacao,
                ':caminho_foto' => $caminhoFoto,
                ':ip_address' => $ipAddress,
                ':id_registro' => $registroAberto['id_registro_ponto']
            ]);
            return 'saida';
        }
        else {
            $sql = "INSERT INTO folha_ponto (id_colaborador, data_hora_entrada, geolocalizacao, caminho_foto, ip_address) 
                    VALUES (:id_colaborador, :data_hora, :geolocalizacao, :caminho_foto, :ip_address)";

            $stmt = $this->db_connection->prepare($sql);
            $stmt->execute([
                ':id_colaborador' => $idColaborador,
                ':data_hora' => $dataHoraAtual,
                ':geolocalizacao' => $geolocalizacao,
                ':caminho_foto' => $caminhoFoto,
                ':ip_address' => $ipAddress
            ]);
            return 'entrada';
        }
    }
}