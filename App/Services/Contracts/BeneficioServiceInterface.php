<?php
// de vitoria milhomem para gabba

/**
 * Contrato para o serviço de gerenciamento de benefícios.
 * Permite que o Módulo de Remuneração obtenha os valores de desconto
 * consolidados sem precisar conhecer as regras de negócio dos benefícios.
 */
interface BeneficioServiceInterface {

    /**
     * Calcula o valor total de descontos de todos os benefícios
     * aplicáveis a um colaborador em um determinado mês.
     *
     * @param int $idColaborador O ID do colaborador.
     * @param int $mes O mês de referência (pode ser útil para benefícios variáveis).
     * @param int $ano O ano de referência.
     * @return float Retorna o valor total a ser descontado no holerite.
     */
    public function calcularTotalDescontos(int $idColaborador, int $mes, int $ano): float;
}