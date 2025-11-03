<?php

namespace App;
/**
 * Contrato para o serviço de processamento de ponto.
 * Abstrai a complexidade dos cálculos de horas e fornece totais
 * consolidados para o Módulo de Remuneração.
 */
interface PontoServiceInterface {

    /**
     * Calcula o total de horas extras de um colaborador em um mês específico.
     *
     * @param int $idColaborador O ID do colaborador a ser consultado.
     * @param int $mes O mês de referência (1 a 12).
     * @param int $ano O ano de referência.
     * @return float Retorna o total de horas extras (ex: 10.5 para 10 horas e 30 minutos).
     */
    public function calcularHorasExtras(int $idColaborador, int $mes, int $ano): float;

    /**
     * Calcula o total de horas de ausência (faltas/atrasos) de um colaborador.
     *
     * @param int $idColaborador O ID do colaborador a ser consultado.
     * @param int $mes O mês de referência (1 a 12).
     * @param int $ano O ano de referência.
     * @return float Retorna o total de horas de ausência.
     */
    public function calcularTotalAusenciasEmHoras(int $idColaborador, int $mes, int $ano): float;
}