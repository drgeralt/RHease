<?php

namespace App\Service\Implementations;

use App\Service\Contracts\PontoServiceInterface;
use PDO;
use DateTime;
use DateInterval;
use DatePeriod;



class PontoService implements PontoServiceInterface
{
    private static $JORNADA_DIARIA_PADRAO_EM_HORAS = 8.0;

    private PDO $db;

    public function __construct(PDO $pdo)
    {
        // Armazena a conexão PDO (Conserta o Problema 1)
        $this->db = $pdo;
    }

    /**
     * {@inheritdoc}
     */
    public function calcularHorasExtras(int $idColaborador, int $mes, int $ano): float
    {
        $totais = $this->calcularTotaisDoMes($idColaborador, $mes, $ano);

        $horasExtras = $totais['trabalhadas'] - $totais['esperadas'];
        return max(0, $horasExtras);
    }

    /**
     * {@inheritdoc}
     */
    public function calcularTotalAusenciasEmHoras(int $idColaborador, int $mes, int $ano): float
    {
        $totais = $this->calcularTotaisDoMes($idColaborador, $mes, $ano);

        $horasAusencia = $totais['esperadas'] - $totais['trabalhadas'];

        return max(0, $horasAusencia);
    }

    /**
     * @return array ['trabalhadas' => float, 'esperadas' => float]
     */
    private function calcularTotaisDoMes(int $idColaborador, int $mes, int $ano): array
    {
        // ### CORREÇÃO 4: Lógica de consulta movida para cá ###
        // Em vez de chamar o PontoModel (que não podemos alterar),
        // o PontoService faz a consulta diretamente.
        $registros = $this->getRegistrosCompletosDoMes($idColaborador, $mes, $ano);

        // 2. Calcula o total de horas efetivamente trabalhadas
        $totalHorasTrabalhadas = 0.0;
        foreach ($registros as $registro) {
            $entrada = new DateTime($registro['data_hora_entrada']);
            $saida = new DateTime($registro['data_hora_saida']);

            $diferencaSegundos = $saida->getTimestamp() - $entrada->getTimestamp();
            $totalHorasTrabalhadas += ($diferencaSegundos / 3600.0);
        }

        // ### CORREÇÃO 5: CORREÇÃO DO BUG LÓGICO ###
        // A lógica do seu arquivo original calculava o esperado
        // baseado nos dias *trabalhados*, o que nunca geraria faltas.

        // A lógica CORRETA é calcular o esperado para o MÊS INTEIRO.
        $diasUteisDoMes = $this->getDiasUteisMes($mes, $ano);
        $totalHorasEsperadas = count($diasUteisDoMes) * self::$JORNADA_DIARIA_PADRAO_EM_HORAS;
        // ### FIM DA CORREÇÃO DO BUG ###

        return [
            'trabalhadas' => $totalHorasTrabalhadas,
            'esperadas' => $totalHorasEsperadas
        ];
    }
    /**
     * Este é o método que faltava no PontoModel.
     * Como não podemos alterar o PontoModel, colocamos a lógica dele aqui.
     */
    private function getRegistrosCompletosDoMes(int $idColaborador, int $mes, int $ano): array
    {
        $sql = "SELECT data_hora_entrada, data_hora_saida
                FROM folha_ponto
                WHERE id_colaborador = :id_colaborador
                  AND EXTRACT(MONTH FROM data_hora_entrada) = :mes
                  AND EXTRACT(YEAR FROM data_hora_entrada) = :ano
                  AND data_hora_saida IS NOT NULL";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id_colaborador' => $idColaborador,
            ':mes' => $mes,
            ':ano' => $ano
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    /**
     * Função auxiliar para buscar dias úteis (Seg-Sex).
     * Esta função é necessária para a correção do bug.
     */
    private function getDiasUteisMes(int $mes, int $ano): array
    {
        $diasUteis = [];
        $dataInicio = new DateTime("$ano-$mes-01");
        $dataFim = new DateTime($dataInicio->format('Y-m-t'));
        $intervalo = new DateInterval('P1D');
        $periodo = new DatePeriod($dataInicio, $intervalo, $dataFim->modify('+1 day'));

        foreach ($periodo as $data) {
            $diaDaSemana = $data->format('N'); // 1=Segunda, 7=Domingo
            if ($diaDaSemana <= 5) { // É dia de semana
                // TODO: Adicionar verificação de feriados (lendo do banco)
                $diasUteis[] = $data->format('Y-m-d');
            }
        }
        return $diasUteis;
    }
}
