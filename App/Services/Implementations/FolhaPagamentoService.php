<?php
// App/Services/Implementations/FolhaPagamentoService.php

namespace App\Services\Implementations;

use App\Core\Database;
use App\Model\ParametrosFolhaModel;
use App\Services\Contracts\FolhaPagamentoServiceInterface;
use PDO;
use Exception;
use stdClass;

class FolhaPagamentoService implements FolhaPagamentoServiceInterface
{
    private PDO $db;
    private $parametrosModel;

    // Tabelas de impostos carregadas uma única vez
    private $tabelaInss;
    private $tabelaIrrf;
    private $deducaoDependenteIrrf;
    private $valorFgtsAliquota = 0.08;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->parametrosModel = new ParametrosFolhaModel();

        $paramInss = $this->parametrosModel->findParametroPorNome('TABELA_INSS_VIGENTE');
        $this->tabelaInss = $paramInss ? json_decode($paramInss->valor, true) : null;

        $paramIrrf = $this->parametrosModel->findParametroPorNome('TABELA_IRRF_VIGENTE');
        $this->tabelaIrrf = $paramIrrf ? json_decode($paramIrrf->valor, true) : null;

        // --- CORREÇÃO APLICADA AQUI ---
        // Removido o "_VIGENTE" para corresponder ao que está no banco de dados
        $paramDependente = $this->parametrosModel->findParametroPorNome('DEDUCAO_DEPENDENTE_IRRF');
        $this->deducaoDependenteIrrf = $paramDependente ? (float) $paramDependente->valor : 0.0;
    }

    /**
     * @inheritDoc
     */
    public function processarFolha(int $ano, int $mes): array
    {
        $colaboradores = $this->buscarColaboradoresAtivos();
        $resultados = ['sucesso' => [], 'falha' => []];

        if (empty($this->tabelaInss) || empty($this->tabelaIrrf)) {
            $resultados['falha'][] = ['nome' => 'SISTEMA', 'erro' => 'Tabelas de impostos nao encontradas no banco de dados. Verifique a tabela parametros_folha.'];
            return $resultados;
        }

        foreach ($colaboradores as $colaborador) {
            $this->db->beginTransaction();
            try {
                $this->limparHoleriteAnterior($colaborador->id_colaborador, $ano, $mes);
                $eventos = $this->coletarEventos($colaborador, $ano, $mes);
                $calculo = $this->calcularImpostosETotais($eventos, $colaborador->numero_dependentes ?? 0);
                $this->salvarHolerite($colaborador, $ano, $mes, $calculo);

                $this->db->commit();
                $resultados['sucesso'][] = $colaborador->nome_completo;
            } catch (Exception $e) {
                $this->db->rollBack();
                $resultados['falha'][] = ['nome' => $colaborador->nome_completo, 'erro' => $e->getMessage()];
            }
        }
        return $resultados;
    }

    private function buscarColaboradoresAtivos(): array
    {
        $sql = "SELECT id_colaborador, nome_completo, salario_base, numero_dependentes 
                FROM colaborador 
                WHERE situacao = 'ativo'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    private function coletarEventos(stdClass $colaborador, int $ano, int $mes): array
    {
        $proventos = [];
        $descontos = [];
        if (!empty($colaborador->salario_base)) {
            $proventos[] = ['codigo' => '101', 'descricao' => 'Salário Base', 'valor' => $colaborador->salario_base, 'tipo' => 'PROVENTO'];
        }
        return ['proventos' => $proventos, 'descontos' => $descontos];
    }

    private function calcularImpostosETotais(array $eventos, int $numDependentes): array
    {
        $totalProventos = array_sum(array_column($eventos['proventos'], 'valor'));

        $baseInss = $totalProventos;
        $valorInss = $this->calcularINSS($baseInss);
        if ($valorInss > 0) {
            $eventos['descontos'][] = ['codigo' => '301', 'descricao' => 'INSS sobre Salário', 'valor' => $valorInss, 'tipo' => 'DESCONTO'];
        }

        $deducaoTotalDependentes = $numDependentes * $this->deducaoDependenteIrrf;
        $baseIrrf = $totalProventos - $valorInss - $deducaoTotalDependentes;
        $valorIrrf = $this->calcularIRRF($baseIrrf);
        if ($valorIrrf > 0) {
            $eventos['descontos'][] = ['codigo' => '302', 'descricao' => 'IRRF', 'valor' => $valorIrrf, 'tipo' => 'DESCONTO'];
        }

        $totalDescontos = array_sum(array_column($eventos['descontos'], 'valor'));
        $salarioLiquido = $totalProventos - $totalDescontos;
        $valorFgts = $totalProventos * $this->valorFgtsAliquota;

        return [
            'total_proventos' => $totalProventos,
            'total_descontos' => $totalDescontos,
            'salario_liquido' => $salarioLiquido,
            'base_inss' => $baseInss,
            'base_irrf' => $baseIrrf,
            'valor_fgts' => $valorFgts,
            'base_fgts' => $totalProventos,
            'itens_holerite' => array_merge($eventos['proventos'], $eventos['descontos'])
        ];
    }

    private function salvarHolerite(stdClass $colaborador, int $ano, int $mes, array $calculo): void
    {
        $sqlHolerite = "INSERT INTO holerites (id_colaborador, mes_referencia, ano_referencia, data_processamento, total_proventos, total_descontos, salario_liquido, base_calculo_inss, base_calculo_fgts, valor_fgts, base_calculo_irrf) VALUES (?, ?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?)";
        $stmtHolerite = $this->db->prepare($sqlHolerite);
        $stmtHolerite->execute([
            $colaborador->id_colaborador, $mes, $ano,
            $calculo['total_proventos'], $calculo['total_descontos'], $calculo['salario_liquido'],
            $calculo['base_inss'], $calculo['base_fgts'], $calculo['valor_fgts'], $calculo['base_irrf']
        ]);
        $holeriteId = $this->db->lastInsertId();

        $sqlItem = "INSERT INTO holerite_itens (id_holerite, codigo_evento, descricao, tipo, valor) VALUES (?, ?, ?, ?, ?)";
        $stmtItem = $this->db->prepare($sqlItem);
        foreach ($calculo['itens_holerite'] as $item) {
            $stmtItem->execute([$holeriteId, $item['codigo'], $item['descricao'], $item['tipo'], $item['valor']]);
        }
    }

    private function limparHoleriteAnterior(int $colaboradorId, int $ano, int $mes): void
    {
        $sql = "DELETE FROM holerites WHERE id_colaborador = ? AND ano_referencia = ? AND mes_referencia = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$colaboradorId, $ano, $mes]);
    }

    private function calcularINSS(float $base): float
    {
        if (empty($this->tabelaInss)) return 0.0;
        $desconto = 0.0;
        foreach ($this->tabelaInss as $faixa) {
            if ($base <= $faixa['ate']) {
                $desconto = ($base * ($faixa['aliquota'] / 100)) - $faixa['deduzir'];
                return round(max(0, $desconto), 2);
            }
        }
        $ultimaFaixa = end($this->tabelaInss);
        $desconto = ($ultimaFaixa['ate'] * ($ultimaFaixa['aliquota'] / 100)) - $ultimaFaixa['deduzir'];
        return round(max(0, $desconto), 2);
    }

    private function calcularIRRF(float $base): float
    {
        if (empty($this->tabelaIrrf)) return 0.0;
        $imposto = 0.0;
        foreach ($this->tabelaIrrf as $faixa) {
            if ($faixa['base_ate'] === "Infinity" || $base <= $faixa['base_ate']) {
                $imposto = ($base * ($faixa['aliquota'] / 100)) - $faixa['deduzir'];
                return round(max(0, $imposto), 2);
            }
        }
        return 0.0;
    }
}

