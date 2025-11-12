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
        // ✅ Busca colaboradores ativos usando getAll()
        $colaboradores = $this->colaboradorModel->getAll();

        if (empty($colaboradores)) {
            throw new Exception("Nenhum colaborador ativo encontrado para processamento.");
        }

        $resultados = ['sucesso' => [], 'falha' => []];

        // ✅ CORRIGIDO: Agora $this->db existe
        $this->db->beginTransaction();

        try {
            foreach ($colaboradores as $colaborador) {
                $colaboradorId = (int) ($colaborador['id_colaborador'] ?? 0);

                // Validação: verifica se o salário existe e é maior que zero
                if (!isset($colaborador['salario_base']) || $colaborador['salario_base'] <= 0) {
                    error_log("⚠️ Colaborador {$colaborador['nome_completo']} (ID: {$colaboradorId}) sem salário definido!");
                    $resultados['falha'][$colaboradorId] =
                        "Colaborador {$colaborador['nome_completo']} (ID: {$colaboradorId}) - " .
                        "Motivo: Salário base não definido (R$ 0,00)";
                    continue;
                }

                // 🔍 LOG para debug
                error_log("✅ Processando: {$colaborador['nome_completo']} | Salário: R$ " .
                    number_format($colaborador['salario_base'], 2, ',', '.'));

                // Busca horas de ausência do mês
                $totalHorasAusencia = $this->pontoService->calcularTotalAusenciasEmHoras(
                    $colaboradorId,
                    $mes,
                    $ano
                );

                // ✅ Limpa holerites anteriores do mesmo período (permite reprocessamento)
                $this->folhaPagamentoModel->limparHoleriteAnterior($colaboradorId, $ano, $mes);

                // Calcula os valores do holerite
                $dadosCalculados = $this->calcularValores($colaborador, $totalHorasAusencia);

                // Salva o holerite principal
                $holeriteId = $this->folhaPagamentoModel->salvarHolerite(
                    $colaboradorId,
                    $ano,
                    $mes,
                    $dadosCalculados
                );

                // Salva os itens detalhados (proventos e descontos)
                $this->folhaPagamentoModel->salvarItens(
                    (int)$holeriteId,
                    $dadosCalculados['itens_holerite']
                );

                $resultados['sucesso'][] = $colaborador['nome_completo'];
            }

            $this->db->commit();
            error_log("✅ Folha processada com sucesso! Total: " . count($resultados['sucesso']) . " colaboradores.");

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("❌ ERRO ao processar folha: " . $e->getMessage());
            throw new Exception("Falha ao processar a folha: " . $e->getMessage());
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
