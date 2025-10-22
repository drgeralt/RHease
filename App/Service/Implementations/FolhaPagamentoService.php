<?php
// App/Service/Implementations/FolhaPagamentoService.php

namespace App\Service\Implementations;

use App\Model\ColaboradorModel;
use App\Model\FolhaPagamentoModel;
use App\Model\ParametrosFolhaModel;
use PDO;
use Exception;

class FolhaPagamentoService
{
    private PDO $db;
    private FolhaPagamentoModel $folhaPagamentoModel;
    private ColaboradorModel $colaboradorModel;
    private ParametrosFolhaModel $parametrosModel;

    // Propriedades para armazenar os parâmetros de cálculo
    private array $tabelaInss = [];
    private array $tabelaIrrf = [];

    /**
     * @param PDO $pdo A conexão com o banco de dados.
     */
    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
        $this->folhaPagamentoModel = new FolhaPagamentoModel($this->db);
        $this->colaboradorModel = new ColaboradorModel($this->db);
        $this->parametrosModel = new ParametrosFolhaModel($this->db);

        // Carrega os parâmetros de INSS e IRRF uma vez na instanciação do serviço.
        $this->carregarParametros();
    }

    private function carregarParametros(): void
    {
        $faixasInss = $this->parametrosModel->findFaixasPorPrefixo('INSS_FAIXA_');
        foreach ($faixasInss as $faixa) {
            // ✅ CORRIGIDO: Acessando 'valor' como uma propriedade de objeto.
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
            // ✅ CORRIGIDO: Acessando 'valor' como uma propriedade de objeto.
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
        // ✅ CORRIGIDO: O método correto é getAll().
        $colaboradores = $this->colaboradorModel->getAll();

        if (empty($colaboradores)) {
            throw new Exception("Nenhum colaborador ativo encontrado para processamento.");
        }

        $resultados = ['sucesso' => [], 'falha' => []];

        $this->db->beginTransaction();
        try {
            foreach ($colaboradores as $colaborador) {
                // ✅ CORRIGIDO: A chave correta é 'id_colaborador', como definido no model.
                $colaboradorId = (int) ($colaborador['id_colaborador'] ?? 0);

                // Supondo que exista um método para buscar o salário.
                // Se o salário já vem em 'obterTodosCompletos', esta linha pode ser removida.
                $salario = $this->colaboradorModel->salarioPorID($colaboradorId);
                $colaborador['salario_base'] = $salario;

                // 1. Limpa os registros antigos
                $this->folhaPagamentoModel->limparHoleriteAnterior($colaboradorId, $ano, $mes);

                // 2. Calcula os valores (lógica interna do Service)
                $dadosCalculados = $this->calcularValores($colaborador);

                // 3. Salva o holerite principal
                $holeriteId = $this->folhaPagamentoModel->salvarHolerite($colaboradorId, $ano, $mes, $dadosCalculados);

                // 4. Salva os itens do holerite
                $this->folhaPagamentoModel->salvarItens((int)$holeriteId, $dadosCalculados['itens_holerite']);

                // ✅ CORRIGIDO: A chave correta para o nome é 'nome_completo'.
                $resultados['sucesso'][] = $colaborador['nome_completo'];
            }
            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new Exception("Falha ao processar a folha de pagamento. A operação foi revertida. Erro: " . $e->getMessage());
        }

        return $resultados;
    }

    private function calcularValores(array $colaborador): array
    {
        // (Aqui entra toda a sua lógica de cálculo de INSS, IRRF, etc., usando $this->tabelaInss, etc.)
        // Exemplo simplificado:
        $salarioBase = (float) $colaborador['salario_base'];
        $descontoInss = $salarioBase * 0.09; // Simulação
        $descontoIrrf = 0; // Simulação
        $totalDescontos = $descontoInss + $descontoIrrf;
        $salarioLiquido = $salarioBase - $totalDescontos;

        return [
            'total_proventos' => $salarioBase,
            'total_descontos' => $totalDescontos,
            'salario_liquido' => $salarioLiquido,
            'base_inss' => $salarioBase,
            'base_fgts' => $salarioBase,
            'valor_fgts' => $salarioBase * 0.08,
            'base_irrf' => $salarioBase - $descontoInss,
            'itens_holerite' => [
                ['codigo' => '101', 'descricao' => 'Salário Base', 'tipo' => 'PROVENTO', 'valor' => $salarioBase],
                ['codigo' => '501', 'descricao' => 'INSS', 'tipo' => 'DESCONTO', 'valor' => $descontoInss],
            ]
        ];
    }
}