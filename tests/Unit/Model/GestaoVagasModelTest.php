<?php

declare(strict_types=1);

namespace Tests\Unit\Model;

use App\Model\GestaoVagasModel;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;

/**
 * Testes Unitários para GestaoVagasModel
 * 
 * COMO FUNCIONA:
 * ==============
 * 
 * 1. MOCKS: Usamos objetos simulados (mocks) do PDO e PDOStatement
 *    para não precisar de um banco de dados real durante os testes.
 * 
 * 2. ARRANGE-ACT-ASSERT: Cada teste segue o padrão:
 *    - ARRANGE: Prepara os dados e configura os mocks
 *    - ACT: Executa o método sendo testado
 *    - ASSERT: Verifica se o resultado está correto
 * 
 * 3. ISOLAMENTO: Cada teste é independente e não afeta outros testes.
 * 
 * OPERAÇÕES TESTADAS:
 * ====================
 * - listarVagas(): Lista todas as vagas
 * - listarVagasAbertas(): Lista apenas vagas abertas
 * - buscarPorId(): Busca uma vaga específica pelo ID
 * - atualizarVaga(): Atualiza os dados de uma vaga
 * - excluirVaga(): Remove uma vaga do banco
 */
class GestaoVagasModelTest extends TestCase
{
    private GestaoVagasModel $model;
    private PDO $pdoMock;
    private PDOStatement $stmtMock;

    /**
     * setUp() é executado ANTES de cada teste
     * Aqui criamos os mocks necessários
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Criar mock do PDO (simula conexão com banco)
        $this->pdoMock = $this->createMock(PDO::class);
        
        // Criar mock do PDOStatement (simula resultado de query)
        $this->stmtMock = $this->createMock(PDOStatement::class);
        
        // Instanciar o model com o PDO mockado
        $this->model = new GestaoVagasModel($this->pdoMock);
    }

    /**
     * TESTE 1: listarVagas()
     * 
     * OBJETIVO: Verificar se o método retorna um array com todas as vagas
     * 
     * COMO TESTA:
     * 1. Configuramos o mock para simular uma query SELECT
     * 2. Definimos dados fictícios que serão "retornados" pelo banco
     * 3. Executamos o método listarVagas()
     * 4. Verificamos se retornou o array esperado
     */
    public function testListarVagasRetornaArrayDeVagas(): void
    {
        // ARRANGE: Preparar dados fictícios
        $vagasEsperadas = [
            [
                'id_vaga' => 1,
                'titulo' => 'Desenvolvedor PHP',
                'departamento' => 'TI',
                'situacao' => 'aberta'
            ],
            [
                'id_vaga' => 2,
                'titulo' => 'Analista de RH',
                'departamento' => 'Recursos Humanos',
                'situacao' => 'fechada'
            ]
        ];

        // Configurar o comportamento do mock PDO
        // Quando o método prepare() for chamado, retorna o statement mock
        $this->pdoMock
            ->expects($this->once())  // Espera ser chamado exatamente 1 vez
            ->method('prepare')
            ->willReturn($this->stmtMock);

        // Configurar o comportamento do statement mock
        $this->stmtMock
            ->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        // Quando fetchAll() for chamado, retorna os dados fictícios
        $this->stmtMock
            ->expects($this->once())
            ->method('fetchAll')
            ->with(PDO::FETCH_ASSOC)  // Verifica se foi chamado com o parâmetro correto
            ->willReturn($vagasEsperadas);

        // ACT: Executar o método sendo testado
        $resultado = $this->model->listarVagas();

        // ASSERT: Verificar se o resultado está correto
        $this->assertIsArray($resultado, 'O resultado deve ser um array');
        $this->assertCount(2, $resultado, 'Deve retornar 2 vagas');
        $this->assertEquals('Desenvolvedor PHP', $resultado[0]['titulo'], 'Primeira vaga deve ter o título correto');
        $this->assertEquals('Analista de RH', $resultado[1]['titulo'], 'Segunda vaga deve ter o título correto');
    }

    /**
     * TESTE 2: listarVagasAbertas()
     * 
     * OBJETIVO: Verificar se retorna apenas vagas com situação 'aberta'
     * 
     * COMO TESTA:
     * 1. Configuramos o mock para simular uma query com WHERE situacao = 'aberta'
     * 2. Verificamos se o método execute() foi chamado com o parâmetro correto
     * 3. Verificamos se retornou apenas vagas abertas
     */
    public function testListarVagasAbertasRetornaApenasVagasAbertas(): void
    {
        // ARRANGE
        $vagasAbertas = [
            [
                'id_vaga' => 1,
                'titulo' => 'Desenvolvedor PHP',
                'descricao_vaga' => 'Vaga para desenvolvedor',
                'requisitos' => 'PHP, MySQL',
                'data_criacao' => '2024-01-01',
                'situacao' => 'aberta',
                'departamento' => 'TI'
            ]
        ];

        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->willReturn($this->stmtMock);

        // Verificar se execute() foi chamado com o parâmetro correto
        $this->stmtMock
            ->expects($this->once())
            ->method('execute')
            ->with([':situacao' => 'aberta'])  // Verifica se passou 'aberta' como parâmetro
            ->willReturn(true);

        $this->stmtMock
            ->expects($this->once())
            ->method('fetchAll')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn($vagasAbertas);

        // ACT
        $resultado = $this->model->listarVagasAbertas();

        // ASSERT
        $this->assertIsArray($resultado);
        $this->assertCount(1, $resultado);
        $this->assertEquals('aberta', $resultado[0]['situacao'], 'A vaga retornada deve estar aberta');
    }

    /**
     * TESTE 3: buscarPorId() - Vaga encontrada
     * 
     * OBJETIVO: Verificar se retorna os dados corretos de uma vaga pelo ID
     */
    public function testBuscarPorIdRetornaVagaQuandoExiste(): void
    {
        // ARRANGE
        $idVaga = 1;
        $vagaEsperada = [
            'id_vaga' => 1,
            'titulo_vaga' => 'Desenvolvedor PHP',
            'descricao_vaga' => 'Vaga para desenvolvedor PHP',
            'situacao' => 'aberta',
            'requisitos_necessarios' => 'PHP, MySQL',
            'requisitos_recomendados' => 'Laravel',
            'requisitos_desejados' => 'Vue.js',
            'nome_setor' => 'TI'
        ];

        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->willReturn($this->stmtMock);

        $this->stmtMock
            ->expects($this->once())
            ->method('execute')
            ->with([':id_vaga' => $idVaga])  // Verifica se passou o ID correto
            ->willReturn(true);

        // fetch() retorna um único registro (não fetchAll)
        $this->stmtMock
            ->expects($this->once())
            ->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn($vagaEsperada);

        // ACT
        $resultado = $this->model->buscarPorId($idVaga);

        // ASSERT
        $this->assertIsArray($resultado, 'Deve retornar um array');
        $this->assertEquals(1, $resultado['id_vaga'], 'ID da vaga deve ser 1');
        $this->assertEquals('Desenvolvedor PHP', $resultado['titulo_vaga'], 'Título deve estar correto');
        $this->assertEquals('aberta', $resultado['situacao'], 'Situação deve estar correta');
    }

    /**
     * TESTE 4: buscarPorId() - Vaga não encontrada
     * 
     * OBJETIVO: Verificar se retorna false quando a vaga não existe
     */
    public function testBuscarPorIdRetornaFalseQuandoVagaNaoExiste(): void
    {
        // ARRANGE
        $idVagaInexistente = 999;

        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->willReturn($this->stmtMock);

        $this->stmtMock
            ->expects($this->once())
            ->method('execute')
            ->with([':id_vaga' => $idVagaInexistente])
            ->willReturn(true);

        // Quando a vaga não existe, fetch() retorna false
        $this->stmtMock
            ->expects($this->once())
            ->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn(false);

        // ACT
        $resultado = $this->model->buscarPorId($idVagaInexistente);

        // ASSERT
        $this->assertFalse($resultado, 'Deve retornar false quando a vaga não existe');
    }

    /**
     * TESTE 5: atualizarVaga()
     * 
     * OBJETIVO: Verificar se atualiza uma vaga com sucesso
     * 
     * COMO TESTA:
     * 1. Preparamos os dados de atualização
     * 2. Verificamos se execute() foi chamado com os dados corretos
     * 3. Verificamos se retornou true (sucesso)
     */
    public function testAtualizarVagaRetornaTrueQuandoSucesso(): void
    {
        // ARRANGE
        $idVaga = 1;
        $dadosAtualizacao = [
            'titulo_vaga' => 'Desenvolvedor PHP Sênior',
            'id_setor' => 1,
            'situacao' => 'aberta',
            'descricao_vaga' => 'Nova descrição da vaga',
            'requisitos_necessarios' => 'PHP avançado',
            'requisitos_recomendados' => 'Laravel, PHPUnit',
            'requisitos_desejados' => 'Vue.js, Docker'
        ];

        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->willReturn($this->stmtMock);

        // O método atualizarVaga adiciona 'id_vaga' ao array de dados
        // O PDO aceita parâmetros com ou sem ':', então verificamos ambos os formatos
        $dadosEsperados = array_merge($dadosAtualizacao, ['id_vaga' => $idVaga]);

        // O método atualizarVaga passa o array $dados diretamente para execute()
        // que contém as chaves sem ':' (PDO aceita ambos os formatos)
        $this->stmtMock
            ->expects($this->once())
            ->method('execute')
            ->with($this->callback(function ($parametros) use ($dadosAtualizacao, $idVaga) {
                // Verifica se é um array
                if (!is_array($parametros)) {
                    return false;
                }
                
                // Verifica se contém os campos essenciais (com ou sem ':')
                $temTitulo = isset($parametros['titulo_vaga']) || isset($parametros[':titulo_vaga']);
                $temSetor = isset($parametros['id_setor']) || isset($parametros[':id_setor']);
                $temSituacao = isset($parametros['situacao']) || isset($parametros[':situacao']);
                $temIdVaga = isset($parametros['id_vaga']) || isset($parametros[':id_vaga']);
                
                // Verifica se o id_vaga está correto
                $idVagaCorreto = false;
                if (isset($parametros['id_vaga']) && $parametros['id_vaga'] === $idVaga) {
                    $idVagaCorreto = true;
                } elseif (isset($parametros[':id_vaga']) && $parametros[':id_vaga'] === $idVaga) {
                    $idVagaCorreto = true;
                }
                
                return $temTitulo && $temSetor && $temSituacao && $temIdVaga && $idVagaCorreto;
            }))
            ->willReturn(true);

        // ACT
        $resultado = $this->model->atualizarVaga($idVaga, $dadosAtualizacao);

        // ASSERT
        $this->assertTrue($resultado, 'Deve retornar true quando a atualização é bem-sucedida');
    }

    /**
     * TESTE 6: excluirVaga() - Sucesso
     * 
     * OBJETIVO: Verificar se exclui uma vaga com sucesso
     */
    public function testExcluirVagaRetornaTrueQuandoSucesso(): void
    {
        // ARRANGE
        $idVaga = 1;

        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->willReturn($this->stmtMock);

        $this->stmtMock
            ->expects($this->once())
            ->method('execute')
            ->with([':id_vaga' => $idVaga])  // Verifica se passou o ID correto
            ->willReturn(true);

        // ACT
        $resultado = $this->model->excluirVaga($idVaga);

        // ASSERT
        $this->assertTrue($resultado, 'Deve retornar true quando a exclusão é bem-sucedida');
    }

    /**
     * TESTE 7: excluirVaga() - Falha
     * 
     * OBJETIVO: Verificar se retorna false quando a exclusão falha
     */
    public function testExcluirVagaRetornaFalseQuandoFalha(): void
    {
        // ARRANGE
        $idVaga = 1;

        $this->pdoMock
            ->expects($this->once())
            ->method('prepare')
            ->willReturn($this->stmtMock);

        // Simular falha na execução
        $this->stmtMock
            ->expects($this->once())
            ->method('execute')
            ->with([':id_vaga' => $idVaga])
            ->willReturn(false);  // Retorna false para simular erro

        // ACT
        $resultado = $this->model->excluirVaga($idVaga);

        // ASSERT
        $this->assertFalse($resultado, 'Deve retornar false quando a exclusão falha');
    }

    /**
     * tearDown() é executado DEPOIS de cada teste
     * Aqui podemos limpar recursos se necessário
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        // Neste caso, não há nada para limpar pois usamos mocks
    }
}

