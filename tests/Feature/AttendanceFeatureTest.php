<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Testes de Feature (End-to-End) do sistema de ponto
 *
 * Estes testes simulam o fluxo completo do usuário
 */
class AttendanceFeatureTest extends TestCase
{
    private $colaboradorId;

    protected function setUp(): void
    {
        parent::setUp();

        // Cria um colaborador completo com face registrada
        $colaborador = $this->createTestColaborador([
            'nome_completo' => 'Pedro Oliveira',
            'email_profissional' => 'pedro@test.com',
            'facial_embedding' => $this->getFakeEmbedding(),
            'facial_registered_at' => date('Y-m-d H:i:s')
        ]);
        $this->colaboradorId = $colaborador['id_colaborador'];
    }

    /**
     * @test
     */
    public function deve_calcular_horas_trabalhadas_corretamente()
    {
        // Cria entrada às 08:00
        $entrada = date('Y-m-d 08:00:00');
        $saida = date('Y-m-d 17:00:00'); // 9 horas depois

        $this->createTestPonto($this->colaboradorId, [
            'data_hora_entrada' => $entrada,
            'data_hora_saida' => $saida
        ]);

        // Busca e calcula horas
        $stmt = $this->getConnection()->prepare("
            SELECT 
                data_hora_entrada,
                data_hora_saida,
                TIMESTAMPDIFF(HOUR, data_hora_entrada, data_hora_saida) as horas_trabalhadas
            FROM folha_ponto 
            WHERE id_colaborador = ?
        ");
        $stmt->execute([$this->colaboradorId]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertEquals(9, $result['horas_trabalhadas']);
    }

    /**
     * @test
     */
    public function relatorio_mensal_deve_listar_todos_pontos()
    {
        $mesAtual = date('Y-m');

        // Cria vários pontos no mês
        for ($dia = 1; $dia <= 5; $dia++) {
            $entrada = "{$mesAtual}-" . str_pad($dia, 2, '0', STR_PAD_LEFT) . " 08:00:00";
            $saida = "{$mesAtual}-" . str_pad($dia, 2, '0', STR_PAD_LEFT) . " 17:00:00";

            $this->createTestPonto($this->colaboradorId, [
                'data_hora_entrada' => $entrada,
                'data_hora_saida' => $saida
            ]);
        }

        // Consulta relatório mensal
        $stmt = $this->getConnection()->prepare("
            SELECT 
                DATE(data_hora_entrada) as data,
                MIN(data_hora_entrada) as primeira_entrada,
                MAX(data_hora_saida) as ultima_saida,
                COUNT(*) as total_registros
            FROM folha_ponto
            WHERE id_colaborador = ?
            AND DATE_FORMAT(data_hora_entrada, '%Y-%m') = ?
            GROUP BY DATE(data_hora_entrada)
            ORDER BY data
        ");
        $stmt->execute([$this->colaboradorId, $mesAtual]);
        $relatorio = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $this->assertCount(5, $relatorio);

        // Verifica primeiro dia
        $this->assertEquals("{$mesAtual}-01", $relatorio[0]['data']);
        $this->assertEquals("{$mesAtual}-01 08:00:00", $relatorio[0]['primeira_entrada']);
        $this->assertEquals("{$mesAtual}-01 17:00:00", $relatorio[0]['ultima_saida']);
    }

    /**
     * @test
     */
    public function deve_identificar_dias_sem_registro_de_saida()
    {
        // Cria alguns pontos sem saída
        for ($i = 0; $i < 3; $i++) {
            $entrada = date('Y-m-d', strtotime("-{$i} days")) . " 08:00:00";

            $this->createTestPonto($this->colaboradorId, [
                'data_hora_entrada' => $entrada,
                'data_hora_saida' => null // Sem saída
            ]);
        }

        // Busca pontos em aberto
        $stmt = $this->getConnection()->prepare("
            SELECT 
                DATE(data_hora_entrada) as data,
                data_hora_entrada
            FROM folha_ponto
            WHERE id_colaborador = ?
            AND data_hora_saida IS NULL
            ORDER BY data_hora_entrada DESC
        ");
        $stmt->execute([$this->colaboradorId]);
        $pontosAbertos = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $this->assertCount(3, $pontosAbertos);
    }

    /**
     * @test
     */
    public function historico_deve_estar_ordenado_cronologicamente()
    {
        // Cria pontos fora de ordem
        $datas = [
            date('Y-m-d', strtotime('-3 days')) . ' 08:00:00',
            date('Y-m-d', strtotime('-1 day')) . ' 08:00:00',
            date('Y-m-d', strtotime('-2 days')) . ' 08:00:00',
        ];

        foreach ($datas as $data) {
            $this->createTestPonto($this->colaboradorId, [
                'data_hora_entrada' => $data
            ]);
        }

        // Busca ordenado
        $stmt = $this->getConnection()->prepare("
            SELECT data_hora_entrada FROM folha_ponto 
            WHERE id_colaborador = ?
            ORDER BY data_hora_entrada DESC
        ");
        $stmt->execute([$this->colaboradorId]);
        $historico = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        // Verifica ordem decrescente
        $this->assertGreaterThan($historico[1], $historico[0]);
        $this->assertGreaterThan($historico[2], $historico[1]);
    }
}