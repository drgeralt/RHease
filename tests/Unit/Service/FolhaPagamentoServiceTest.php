<?php
// tests/FolhaPagamentoServiceTest.php

namespace App\Tests\Service;

use App\Model\ColaboradorModel;
use App\Model\FolhaPagamentoModel;
use App\Model\ParametrosFolhaModel;
use App\Service\Contracts\PontoServiceInterface;
use App\Service\Implementations\FolhaPagamentoService;
use PHPUnit\Framework\TestCase;

class FolhaPagamentoServiceTest extends TestCase
{
    private $mockFolhaPagamentoModel;
    private $mockColaboradorModel;
    private $mockParametrosModel;
    private $mockPontoService;
    private FolhaPagamentoService $service;

    protected function setUp(): void
    {
        // 1. Criar mocks
        $this->mockFolhaPagamentoModel = $this->createMock(FolhaPagamentoModel::class);
        $this->mockColaboradorModel = $this->createMock(ColaboradorModel::class);
        $this->mockParametrosModel = $this->createMock(ParametrosFolhaModel::class);
        $this->mockPontoService = $this->createMock(PontoServiceInterface::class);

        // 2. Configurar parâmetros para o construtor
        $jsonInssFalso = json_encode(['aliquota' => 7.5, 'de' => 0, 'ate' => 1500.0, 'deduzir' => 0]);
        $dadosInss = [(object)['nome' => 'INSS_FAIXA_1', 'valor' => $jsonInssFalso]];

        $jsonIrrfFalso = json_encode(['aliquota' => 15.0, 'de' => 2000.0, 'ate' => 3000.0, 'deduzir' => 100.0]);
        $dadosIrrf = [(object)['nome' => 'IRRF_FAIXA_1', 'valor' => $jsonIrrfFalso]];

        $this->mockParametrosModel->method('findFaixasPorPrefixo')
            ->willReturnCallback(function ($prefixo) use ($dadosInss, $dadosIrrf) {
                if ($prefixo === 'INSS_FAIXA_') {
                    return $dadosInss;
                }
                if ($prefixo === 'IRRF_FAIXA_') {
                    return $dadosIrrf;
                }
                return [];
            });

        // 3. Criar o service com os mocks
        $this->service = new FolhaPagamentoService(
            $this->mockFolhaPagamentoModel,
            $this->mockColaboradorModel,
            $this->mockParametrosModel,
            $this->mockPontoService
        );
    }

    /**
     * TESTE 1: Verificar se a folha REPROCESSA corretamente (comportamento esperado do sistema)
     * O sistema deve permitir reprocessamento, limpando holerites anteriores
     */
    public function testProcessarFolhaReprocessaHoleriteExistente()
    {
        $ano = 2025;
        $mes = 10;

        $colaboradorFalso = [
            'id_colaborador' => 1,
            'nome_completo' => 'Teste Silva',
            'salario_base' => 3000.0,
            'cpf' => '111.222.333-44',
            'email_pessoal' => 'teste@email.com',
            'data_nascimento' => '1990-01-01',
            'cargo' => 'Analista',
            'setor' => 'TI',
            'situacao' => 'ativo'
        ];

        $this->mockColaboradorModel->method('getAll')
            ->willReturn([$colaboradorFalso]);

        // ✅ Verificar que o sistema LIMPA o holerite anterior
        $this->mockFolhaPagamentoModel->expects($this->once())
            ->method('limparHoleriteAnterior')
            ->with(1, $ano, $mes);

        // ✅ E então salva o novo holerite
        $this->mockFolhaPagamentoModel->expects($this->once())
            ->method('salvarHolerite')
            ->willReturn('999');

        $this->mockFolhaPagamentoModel->expects($this->once())
            ->method('salvarItens');

        $this->mockPontoService->method('calcularTotalAusenciasEmHoras')
            ->willReturn(0.0);

        // Executar
        $resultados = $this->service->processarFolha($ano, $mes);

        // ✅ Deve reprocessar COM SUCESSO
        $this->assertCount(1, $resultados['sucesso'], 'Deve reprocessar com sucesso');
        $this->assertEmpty($resultados['falha'], 'Não deve ter falhas');
        $this->assertEquals('Teste Silva', $resultados['sucesso'][0]);
    }

    /**
     * TESTE 2: Verificar processamento com sucesso de um novo holerite
     */
    public function testProcessarFolhaComSucesso()
    {
        $ano = 2025;
        $mes = 11;

        $colaboradorFalso = [
            'id_colaborador' => 2,
            'nome_completo' => 'Maria Santos',
            'salario_base' => 5000.0,
            'cpf' => '222.333.444-55',
            'email_pessoal' => 'maria@email.com',
            'data_nascimento' => '1985-05-15',
            'cargo' => 'Gerente',
            'setor' => 'Administrativo',
            'situacao' => 'ativo'
        ];

        $this->mockColaboradorModel->method('getAll')
            ->willReturn([$colaboradorFalso]);

        $this->mockFolhaPagamentoModel->expects($this->once())
            ->method('limparHoleriteAnterior')
            ->with(2, $ano, $mes);

        $this->mockFolhaPagamentoModel->expects($this->once())
            ->method('salvarHolerite')
            ->willReturn('456');

        $this->mockFolhaPagamentoModel->expects($this->once())
            ->method('salvarItens');

        $this->mockPontoService->method('calcularTotalAusenciasEmHoras')
            ->willReturn(0.0);

        // Executar
        $resultados = $this->service->processarFolha($ano, $mes);

        // Verificar
        $this->assertCount(1, $resultados['sucesso']);
        $this->assertEmpty($resultados['falha']);
        $this->assertEquals('Maria Santos', $resultados['sucesso'][0]);
    }

    /**
     * TESTE 3: Verificar erro quando não há colaboradores ativos
     */
    public function testProcessarFolhaFalhaSemColaboradores()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Nenhum colaborador ativo encontrado para processamento.');

        // Simular que não há colaboradores
        $this->mockColaboradorModel->method('getAll')
            ->willReturn([]);

        $this->service->processarFolha(2025, 11);
    }

    /**
     * TESTE 4: Verificar se colaborador sem salário é marcado como falha
     */
    public function testProcessarFolhaComColaboradorSemSalario()
    {
        $ano = 2025;
        $mes = 11;

        $colaboradorSemSalario = [
            'id_colaborador' => 3,
            'nome_completo' => 'Pedro Oliveira',
            'salario_base' => 0, // ❌ Salário zerado
            'cpf' => '333.444.555-66',
            'email_pessoal' => 'pedro@email.com',
            'data_nascimento' => '1988-03-10',
            'cargo' => 'Estagiário',
            'setor' => 'RH',
            'situacao' => 'ativo'
        ];

        $this->mockColaboradorModel->method('getAll')
            ->willReturn([$colaboradorSemSalario]);

        // Não deve chamar salvarHolerite nem salvarItens
        $this->mockFolhaPagamentoModel->expects($this->never())
            ->method('salvarHolerite');

        $this->mockFolhaPagamentoModel->expects($this->never())
            ->method('salvarItens');

        // Executar
        $resultados = $this->service->processarFolha($ano, $mes);

        // Verificar que foi para falha
        $this->assertEmpty($resultados['sucesso']);
        $this->assertNotEmpty($resultados['falha']);
        $this->assertArrayHasKey(3, $resultados['falha']);
        $this->assertStringContainsString('Salário base não definido', $resultados['falha'][3]);
    }

    /**
     * TESTE 5: Verificar desconto de faltas no processamento
     */
    public function testProcessarFolhaComDescontoFaltas()
    {
        $ano = 2025;
        $mes = 11;

        $colaboradorFalso = [
            'id_colaborador' => 4,
            'nome_completo' => 'João Souza',
            'salario_base' => 4000.0,
            'cpf' => '444.555.666-77',
            'email_pessoal' => 'joao@email.com',
            'data_nascimento' => '1992-08-20',
            'cargo' => 'Assistente',
            'setor' => 'Vendas',
            'situacao' => 'ativo'
        ];

        $this->mockColaboradorModel->method('getAll')
            ->willReturn([$colaboradorFalso]);

        $this->mockFolhaPagamentoModel->method('limparHoleriteAnterior');
        $this->mockFolhaPagamentoModel->method('salvarHolerite')
            ->willReturn('789');

        // Capturar os itens que foram salvos
        $itensSalvos = null;
        $this->mockFolhaPagamentoModel->expects($this->once())
            ->method('salvarItens')
            ->with($this->anything(), $this->callback(function($itens) use (&$itensSalvos) {
                $itensSalvos = $itens;
                return true;
            }));

        // Simular 8 horas de ausência (1 dia)
        $this->mockPontoService->method('calcularTotalAusenciasEmHoras')
            ->willReturn(8.0);

        // Executar
        $resultados = $this->service->processarFolha($ano, $mes);

        // Verificações
        $this->assertCount(1, $resultados['sucesso']);
        $this->assertEquals('João Souza', $resultados['sucesso'][0]);

        // Verificar se existe item de desconto de faltas
        $this->assertNotNull($itensSalvos);
        $temDescontoFaltas = false;
        foreach ($itensSalvos as $item) {
            if ($item['codigo'] === '205' && $item['tipo'] === 'desconto') {
                $temDescontoFaltas = true;
                $this->assertStringContainsString('Faltas e Atrasos', $item['descricao']);
                $this->assertGreaterThan(0, $item['valor']);
            }
        }
        $this->assertTrue($temDescontoFaltas, 'Deve ter desconto de faltas nos itens');
    }

    /**
     * TESTE 6: Verificar processamento de múltiplos colaboradores
     */
    public function testProcessarFolhaComMultiplosColaboradores()
    {
        $ano = 2025;
        $mes = 12;

        $colaboradores = [
            [
                'id_colaborador' => 10,
                'nome_completo' => 'Ana Costa',
                'salario_base' => 3500.0,
                'cpf' => '111.111.111-11',
                'email_pessoal' => 'ana@email.com',
                'data_nascimento' => '1990-01-01',
                'cargo' => 'Analista',
                'setor' => 'TI',
                'situacao' => 'ativo'
            ],
            [
                'id_colaborador' => 11,
                'nome_completo' => 'Bruno Lima',
                'salario_base' => 4500.0,
                'cpf' => '222.222.222-22',
                'email_pessoal' => 'bruno@email.com',
                'data_nascimento' => '1985-05-15',
                'cargo' => 'Coordenador',
                'setor' => 'Vendas',
                'situacao' => 'ativo'
            ],
            [
                'id_colaborador' => 12,
                'nome_completo' => 'Carla Dias',
                'salario_base' => 0, // ❌ Sem salário
                'cpf' => '333.333.333-33',
                'email_pessoal' => 'carla@email.com',
                'data_nascimento' => '1995-12-20',
                'cargo' => 'Trainee',
                'setor' => 'RH',
                'situacao' => 'ativo'
            ]
        ];

        $this->mockColaboradorModel->method('getAll')
            ->willReturn($colaboradores);

        // Deve salvar 2 holerites (apenas os com salário válido)
        $this->mockFolhaPagamentoModel->expects($this->exactly(2))
            ->method('salvarHolerite')
            ->willReturnOnConsecutiveCalls('1001', '1002');

        $this->mockFolhaPagamentoModel->expects($this->exactly(2))
            ->method('salvarItens');

        $this->mockPontoService->method('calcularTotalAusenciasEmHoras')
            ->willReturn(0.0);

        // Executar
        $resultados = $this->service->processarFolha($ano, $mes);

        // Verificações
        $this->assertCount(2, $resultados['sucesso'], 'Deve processar 2 com sucesso');
        $this->assertCount(1, $resultados['falha'], 'Deve ter 1 falha');

        $this->assertContains('Ana Costa', $resultados['sucesso']);
        $this->assertContains('Bruno Lima', $resultados['sucesso']);

        $this->assertArrayHasKey(12, $resultados['falha']);
        $this->assertStringContainsString('Carla Dias', $resultados['falha'][12]);
    }
}
