<?php
// App/Service/Implementations/FolhaPagamentoService.php

namespace App\Service\Implementations;

use App\Model\ColaboradorModel;
use App\Model\FolhaPagamentoModel;
use App\Model\ParametrosFolhaModel;
use App\Service\Contracts\PontoServiceInterface;
use PDO;
use Exception;

class FolhaPagamentoService
{
    private FolhaPagamentoModel $folhaPagamentoModel;
    private ColaboradorModel $colaboradorModel;
    private ParametrosFolhaModel $parametrosModel;
    private PontoServiceInterface $pontoService;
    private PDO $db; // ✅ ADICIONADO

    private array $tabelaInss = [];
    private array $tabelaIrrf = [];

    public function __construct(
        FolhaPagamentoModel $folhaPagamentoModel,
        ColaboradorModel $colaboradorModel,
        ParametrosFolhaModel $parametrosModel,
        PontoServiceInterface $pontoService
    ) {
        $this->folhaPagamentoModel = $folhaPagamentoModel;
        $this->colaboradorModel = $colaboradorModel;
        $this->parametrosModel = $parametrosModel;
        $this->pontoService = $pontoService;

        // ✅ CORRIGIDO: Obter a conexão PDO do Model
        $this->db = $this->folhaPagamentoModel->getConnection();

        $this->carregarParametros();
    }

    private function carregarParametros(): void
    {
        $faixasInss = $this->parametrosModel->findFaixasPorPrefixo('INSS_FAIXA_');
        foreach ($faixasInss as $faixa) {
            $dadosJson = json_decode($faixa->valor, true);
            $this->tabelaInss[] = [
                'aliquota' => (float) ($dadosJson['aliquota'] ?? 0),
                'de' => (float) ($dadosJson['de'] ?? 0),
                'ate' => (float) ($dadosJson['ate'] ?? 0),
                'deduzir' => (float) ($dadosJson['deduzir'] ?? 0)
            ];
        }

        $faixasIrrf = $this->parametrosModel->findFaixasPorPrefixo('IRRF_FAIXA_');
        foreach ($faixasIrrf as $faixa) {
            $dadosJson = json_decode($faixa->valor, true);
            $this->tabelaIrrf[] = [
                'aliquota' => (float) ($dadosJson['aliquota'] ?? 0),
                'de' => (float) ($dadosJson['de'] ?? 0),
                'ate' => (float) ($dadosJson['ate'] ?? 0),
                'deduzir' => (float) ($dadosJson['deduzir'] ?? 0)
            ];
        }

        if (empty($this->tabelaInss)) {
            throw new Exception("Parâmetros de cálculo do INSS (INSS_FAIXA_...) não foram encontrados na tabela 'parametros_folha'.");
        }
        if (empty($this->tabelaIrrf)) {
            throw new Exception("Parâmetros de cálculo do IRRF (IRRF_FAIXA_...) não foram encontrados na tabela 'parametros_folha'.");
        }
    }

    public function processarFolha(int $ano, int $mes): array
    {
        $colaboradores = $this->colaboradorModel->getAll();

        if (empty($colaboradores)) {
            throw new Exception("Nenhum colaborador ativo encontrado para processamento.");
        }

        $resultados = ['sucesso' => [], 'falha' => []];

        $this->db->beginTransaction();

        try {
            foreach ($colaboradores as $colaborador) {
                $colaboradorId = (int) ($colaborador['id_colaborador'] ?? 0);
                $nomeColaborador = $colaborador['nome_completo'] ?? 'Colaborador #' . $colaboradorId;

                // --- VALIDAÇÃO DE SALÁRIO ---
                if (!isset($colaborador['salario_base']) || $colaborador['salario_base'] <= 0) {
                    error_log("Colaborador {$nomeColaborador} sem salário definido");

                    // CORREÇÃO 1: Enviando array estruturado para a Falha
                    $resultados['falha'][] = [
                        'id_colaborador' => $colaboradorId,
                        'nome' => $nomeColaborador,
                        'erro' => "Salário base não definido ou inválido (R$ 0,00)."
                    ];
                    continue;
                }


                try {

                    $totalHorasAusencia = $this->pontoService->calcularTotalAusenciasEmHoras(
                        $colaboradorId, $mes, $ano
                    );


                    $this->folhaPagamentoModel->limparHoleriteAnterior($colaboradorId, $ano, $mes);


                    $dadosCalculados = $this->calcularValores($colaborador, $totalHorasAusencia);


                    $holeriteId = $this->folhaPagamentoModel->salvarHolerite(
                        $colaboradorId, $ano, $mes, $dadosCalculados
                    );

                    $this->folhaPagamentoModel->salvarItens(
                        (int)$holeriteId,
                        $dadosCalculados['itens_holerite']
                    );
                    $resultados['sucesso'][] = [
                        'id_colaborador' => $colaboradorId,
                        'nome' => $nomeColaborador,
                        'salario_liquido' => $dadosCalculados['salario_liquido']
                    ];

                } catch (Exception $eInt) {
                    $resultados['falha'][] = [
                        'id_colaborador' => $colaboradorId,
                        'nome' => $nomeColaborador,
                        'erro' => "Erro interno de cálculo: " . $eInt->getMessage()
                    ];
                }
            }

            $this->db->commit();
            error_log("Folha processada. Sucessos: " . count($resultados['sucesso']));

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("ERRO CRÍTICO: " . $e->getMessage());
            throw new Exception("Falha geral ao processar a folha: " . $e->getMessage());
        }

        return $resultados;
    }

    private function calcularValores(array $colaborador, float $totalHorasAusencia): array
    {
        $itensHolerite = [];

        // ✅ Pega o salário base diretamente do array
        $salarioBase = (float) ($colaborador['salario_base'] ?? 0);
        $cargaHorariaMensal = 220.0; // TODO: Buscar do banco de dados

        // 1. PROVENTOS
        $itensHolerite[] = [
            'codigo' => '101',
            'descricao' => 'Salario Base',
            'tipo' => 'provento',
            'valor' => $salarioBase
        ];

        // 2. DESCONTOS

        // Desconto INSS (simplificado - 9%)
        $descontoInss = $salarioBase * 0.09;
        if ($descontoInss > 0) {
            $itensHolerite[] = [
                'codigo' => '201',
                'descricao' => 'Desconto INSS',
                'tipo' => 'desconto',
                'valor' => $descontoInss
            ];
        }

        // Base para cálculo do IRRF
        $baseIrrf = $salarioBase - $descontoInss;
        $descontoIrrf = 0; // TODO: Implementar cálculo progressivo real

        // Desconto de Faltas e Atrasos
        $descontoFaltas = 0.0;
        if ($totalHorasAusencia > 0) {
            $valorHora = $salarioBase / $cargaHorariaMensal;
            $descontoFaltas = $valorHora * $totalHorasAusencia;

            $itensHolerite[] = [
                'codigo' => '205',
                'descricao' => "Faltas e Atrasos (" .
                    number_format($totalHorasAusencia, 2, ',', '.') . " Horas)",
                'tipo' => 'desconto',
                'valor' => $descontoFaltas
            ];
        }

        // 3. TOTALIZADORES
        $totalProventos = $salarioBase;
        $totalDescontos = $descontoInss + $descontoIrrf + $descontoFaltas;
        $salarioLiquido = $totalProventos - $totalDescontos;

        // 4. RETORNA ARRAY COM TODOS OS DADOS
        return [
            'total_proventos' => $totalProventos,
            'total_descontos' => $totalDescontos,
            'salario_liquido' => $salarioLiquido,
            'base_inss' => $salarioBase,
            'base_fgts' => $salarioBase,
            'valor_fgts' => $salarioBase * 0.08,
            'base_irrf' => $baseIrrf,
            'itens_holerite' => $itensHolerite
        ];
    }
}
