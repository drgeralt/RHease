<?php
// De rhyan para vitóra leal

/**
 * Contrato para o serviço de gerenciamento de candidaturas.
 * Expõe os resultados do processamento e da análise da IA para
 * que outros módulos possam exibi-los.
 */
interface CandidaturaServiceInterface {

    /**
     * Obtém uma lista de todas as candidaturas para uma vaga específica,
     * ordenadas pela pontuação de aderência.
     *
     * @param int $idVaga O ID da vaga.
     * @return array Retorna um array de candidaturas. Cada item do array deve ser
     * um outro array contendo dados do candidato, a pontuação e a justificativa da IA.
     * Ex: [['candidato' => [...], 'pontuacao' => 95, 'justificativa' => '...'], ...]
     */
    public function obterCandidaturasPorVaga(int $idVaga): array;
}