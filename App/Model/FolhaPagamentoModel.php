<?php
// App/Model/FolhaPagamentoModel.php

namespace App\Model;

use PDO;
use PDOException;
use stdClass;

class FolhaPagamentoModel
{
    private $pdo;
    private $parametrosModel;
    private $colaboradorModel;

    // Tabelas de impostos carregadas uma única vez
    private $tabelaInss;
    private $tabelaIrrf;
    private $deducaoDependenteIrrf;
    private $valorFgtsAliquota = 0.08; // 8%

    public function __construct()
    {
        // Conexão principal com o banco
        $host = 'localhost'; $user = 'root'; $password = ''; $database = 'rhease';
        try {
            $this->pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $user, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erro ao conectar: " . $e->getMessage());
        }

        // Instancia os outros models que vamos precisar
        $this->parametrosModel = new ParametrosFolhaModel();
        $this->colaboradorModel = new ColaboradorModel();

        // Carrega os parâmetros da folha no construtor
        $paramInss = $this->parametrosModel->findParametroPorChave('TABELA_INSS_VIGENTE');
        $this->tabelaInss = json_decode($paramInss->valor_texto, true);

        $paramIrrf = $this->parametrosModel->findParametroPorChave('TABELA_IRRF_VIGENTE');
        $this->tabelaIrrf = json_decode($paramIrrf->valor_texto, true);

        $paramDependente = $this->parametrosModel->findParametroPorChave('DEDUCAO_DEPENDENTE_IRRF_VIGENTE');
        $this->deducaoDependenteIrrf = (float) $paramDependente->valor_decimal;
    }

    /**
     * Orquestra o processamento completo da folha de pagamento.
     * Este é o método principal a ser chamado pelo Controller.
     */
    public function processarFolha(int $ano, int $mes): array
    {
        $colaboradores = $this->colaboradorModel->findAllAtivos();
        $resultados = ['sucesso' => [], 'falha' => []];

        foreach ($colaboradores as $colaborador) {
            $this->pdo->beginTransaction();
            try {
                // Limpa holerite anterior para evitar duplicidade no reprocessamento
                $this->limparHoleriteAnterior($colaborador->id_colaborador, $ano, $mes);

                // 1. Coleta eventos (salário, bônus, faltas, etc.)
                $eventos = $this->_coletarEventos($colaborador, $ano, $mes);

                // 2. Calcula os impostos e totais
                $calculo = $this->_calcularImpostosETotais($eventos, $colaborador->numero_dependentes);

                // 3. Salva o holerite e seus itens no banco
                $this->_salvarHolerite($colaborador->id_colaborador, $ano, $mes, $colaborador->salario_base, $calculo);

                $this->pdo->commit();
                $resultados['sucesso'][] = $colaborador->id_colaborador;
            } catch (\Exception $e) {
                $this->pdo->rollBack();
                $resultados['falha'][] = ['id' => $colaborador->id_colaborador, 'erro' => $e->getMessage()];
            }
        }
        return $resultados;
    }

    // MÉTODOS PRIVADOS DE LÓGICA INTERNA

    private function _coletarEventos(stdClass $colaborador, int $ano, int $mes): array
    {
        $proventos = [];
        $descontos = [];
        $proventos[] = ['codigo' => '101', 'descricao' => 'Salário Base', 'valor' => $colaborador->salario_base, 'tipo' => 'PROVENTO'];

        // AQUI ENTRA A INTEGRAÇÃO REAL COM OUTROS MODELS
        // Ex: $descontoVT = (new BeneficioModel())->getDescontoVT(...);
        // Ex: $horasExtras = (new PontoModel())->getHorasExtras(...);

        return ['proventos' => $proventos, 'descontos' => $descontos];
    }

    private function _calcularImpostosETotais(array $eventos, int $numDependentes): array
    {
        $totalProventos = array_sum(array_column($eventos['proventos'], 'valor'));

        $baseInss = $totalProventos;
        $valorInss = $this->_calcularINSS($baseInss);
        if ($valorInss > 0) {
            $eventos['descontos'][] = ['codigo' => '301', 'descricao' => 'INSS sobre Salário', 'valor' => $valorInss, 'tipo' => 'DESCONTO'];
        }

        $deducaoTotalDependentes = $numDependentes * $this->deducaoDependenteIrrf;
        $baseIrrf = $totalProventos - $valorInss - $deducaoTotalDependentes;
        $valorIrrf = $this->_calcularIRRF($baseIrrf);
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

    private function _salvarHolerite(int $colaboradorId, int $ano, int $mes, float $salarioBase, array $calculo): void
    {
        $sqlHolerite = "INSERT INTO holerites (id_colaborador, mes_referencia, ano_referencia, data_processamento, total_proventos, total_descontos, salario_liquido, base_calculo_inss, base_calculo_fgts, valor_fgts, base_calculo_irrf) VALUES (?, ?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?)";
        $stmtHolerite = $this->pdo->prepare($sqlHolerite);
        $stmtHolerite->execute([
            $colaboradorId, $mes, $ano,
            $calculo['total_proventos'], $calculo['total_descontos'], $calculo['salario_liquido'],
            $calculo['base_inss'], $calculo['base_fgts'], $calculo['valor_fgts'], $calculo['base_irrf']
        ]);
        $holeriteId = $this->pdo->lastInsertId();

        $sqlItem = "INSERT INTO holerite_itens (id_holerite, codigo_evento, descricao, tipo, valor) VALUES (?, ?, ?, ?, ?)";
        $stmtItem = $this->pdo->prepare($sqlItem);
        foreach ($calculo['itens_holerite'] as $item) {
            $stmtItem->execute([$holeriteId, $item['codigo'], $item['descricao'], $item['tipo'], $item['valor']]);
        }
    }

    private function limparHoleriteAnterior(int $colaboradorId, int $ano, int $mes): void
    {
        $sql = "DELETE FROM holerites WHERE id_colaborador = ? AND ano_referencia = ? AND mes_referencia = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$colaboradorId, $ano, $mes]);
    }

    private function _calcularINSS(float $base): float { /* ... lógica de cálculo ... */ return round(max(0, $valor), 2); }
    private function _calcularIRRF(float $base): float { /* ... lógica de cálculo ... */ return round(max(0, $valor), 2); }
}