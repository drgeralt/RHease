<?php

namespace App\Services;

use App\Services\Contracts\PontoServiceInterface;
use App\Model\PontoModel;
use DateTime;

/**
 * Implementação concreta do serviço de processamento de ponto.
 */
class PontoService implements PontoServiceInterface
{
    /**
     * Define a jornada de trabalho diária padrão em horas.
     * Este valor pode ser movido para um ficheiro de configuração no futuro.
     */
    private static $JORNADA_DIARIA_PADRAO_EM_HORAS = 8.0;

    private $pontoModel;

    public function __construct()
    {
        $this->pontoModel = new PontoModel();
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
     * Método auxiliar privado para calcular os totais de horas trabalhadas e esperadas.
     * Evita a duplicação de código entre os métodos públicos.
     *
     * @return array ['trabalhadas' => float, 'esperadas' => float]
     */
    private function calcularTotaisDoMes(int $idColaborador, int $mes, int $ano): array
    {
        $registros = $this->pontoModel->getRegistrosCompletosDoMes($idColaborador, $mes, $ano);

        if (empty($registros)) {
            return ['trabalhadas' => 0.0, 'esperadas' => 0.0];
        }

        $totalHorasTrabalhadas = 0.0;
        $diasTrabalhados = [];

        foreach ($registros as $registro) {
            $entrada = new DateTime($registro['data_hora_entrada']);
            $saida = new DateTime($registro['data_hora_saida']);

            $intervalo = $saida->diff($entrada);

            $horasDoDia = $intervalo->h + ($intervalo->i / 60) + ($intervalo->s / 3600);
            $totalHorasTrabalhadas += $horasDoDia;
            $diasTrabalhados[$entrada->format('Y-m-d')] = true;
        }

        $numeroDeDiasTrabalhados = count($diasTrabalhados);
        $totalHorasEsperadas = $numeroDeDiasTrabalhados * self::$JORNADA_DIARIA_PADRAO_EM_HORAS;

        return [
            'trabalhadas' => $totalHorasTrabalhadas,
            'esperadas' => $totalHorasEsperadas
        ];
    }
}
