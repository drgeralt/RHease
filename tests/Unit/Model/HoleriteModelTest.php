<?php

namespace App\Model;

use App\Model\HoleriteModel;
use PHPUnit\Framework\TestCase;
use PDO; // Precisamos importar o PDO original
use PDOStatement; // E o PDOStatement original

class HoleriteModelTest extends TestCase
{
    private $mockPdo;
    private $mockStatement;
    private HoleriteModel $model; // O "Subject Under Test" (SUT)

    /**
     * Este método especial do PHPUnit é executado ANTES
     * de CADA teste (cada método 'test...') desta classe.
     * * Usamos ele para "limpar a mesa" e preparar os mocks.
     */
    protected function setUp(): void
    {
        // 1. Crie os dois mocks que precisamos
        $this->mockPdo = $this->createMock(PDO::class);
        $this->mockStatement = $this->createMock(PDOStatement::class);

        // 2. Crie uma instância REAL do nosso Model,
        //    mas injete o mockPdo no construtor.
        $this->model = new HoleriteModel($this->mockPdo);
    }

    /**
     * Agora vamos testar o método 'findColaboradorById'
     */
    public function testFindColaboradorById()
    {
        // 1. PREPARAÇÃO (Arrange)

        // A. Defina os dados falsos que esperamos que o banco retorne
        $idFalso = 123;
        $dadosFalsos = ['id_colaborador' => $idFalso, 'nome_completo' => 'Colaborador Teste'];

        // B. "Ensine" os mocks (a cadeia que explicamos)

        // "Quando o $mockPdo chamar 'prepare' (com qualquer SQL)..."
        $this->mockPdo->method('prepare')
            // "...ele DEVE retornar o $mockStatement."
            ->willReturn($this->mockStatement);

        // "Quando o $mockStatement chamar 'bindParam'..."
        $this->mockStatement->method('bindParam')
            ->willReturn(true); // "...apenas retorne true."

        // "Quando o $mockStatement chamar 'execute'..."
        $this->mockStatement->method('execute')
            ->willReturn(true); // "...apenas retorne true."

        // "Quando o $mockStatement chamar 'fetch'..."
        $this->mockStatement->method('fetch')
            // "...retorne os nossos $dadosFalsos."
            ->willReturn($dadosFalsos);


        // 2. EXECUÇÃO (Act)
        // Agora, chame o método real.
        // O $this->model vai usar o $mockPdo e o $mockStatement
        // que nós ensinamos acima.
        $resultado = $this->model->findColaboradorById($idFalso);


        // 3. VERIFICAÇÃO (Assert)
        // Verifique se o resultado do método é exatamente
        // o que nós mandamos o mock retornar.
        $this->assertEquals($dadosFalsos, $resultado);
    }
    /**
     * Testa o método 'findByColaboradorId'
     */
    public function testFindByColaboradorId()
    {
        // 1. PREPARAÇÃO (Arrange)

        // A. Defina os dados falsos (desta vez, um array de resultados)
        $idFalso = 456;
        $dadosFalsos = [
            ['id_holerite' => 1, 'mes_referencia' => 10, 'ano_referencia' => 2025],
            ['id_holerite' => 2, 'mes_referencia' => 11, 'ano_referencia' => 2025]
        ];

        // B. "Ensine" os mocks (repare na sutil diferença)
        // O setUp() já criou os mocks $this->mockPdo e $this->mockStatement

        $this->mockPdo->method('prepare')
            ->willReturn($this->mockStatement);

        $this->mockStatement->method('bindParam')
            ->willReturn(true);

        $this->mockStatement->method('execute')
            ->willReturn(true);

        // AQUI ESTÁ A DIFERENÇA:
        // "Quando o $mockStatement chamar 'fetchAll'..."
        $this->mockStatement->method('fetchAll')
            // "...retorne o nosso array de $dadosFalsos."
            ->willReturn($dadosFalsos);


        // 2. EXECUÇÃO (Act)
        // Chame o método real
        $resultado = $this->model->findByColaboradorId($idFalso);


        // 3. VERIFICAÇÃO (Assert)
        // Verifique se o resultado é o array de dados falsos
        $this->assertEquals($dadosFalsos, $resultado);
    }
    /**
     * Testa o método 'buscarDadosParaTela' que faz duas consultas.
     */
    public function testBuscarDadosParaTela()
    {
        // 1. PREPARAÇÃO (Arrange)

        // A. Crie DOIS mocks de Statement, um para cada consulta
        $mockStatement1 = $this->createMock(PDOStatement::class);
        $mockStatement2 = $this->createMock(PDOStatement::class);

        // B. Defina os dados falsos para ambas as consultas
        $idFalso = 789;
        $dadosFalsosNome = ['nome_completo' => 'Colaborador Tela Teste'];
        $dadosFalsosHolerites = [(object)['id_holerite' => 3]]; // FETCH_OBJ retorna objetos

        // C. Ensine o $mockPdo a retornar os mocks NA ORDEM CERTA
        $this->mockPdo->method('prepare')
            ->will($this->onConsecutiveCalls(
                $mockStatement1, // 1ª chamada de prepare()
                $mockStatement2  // 2ª chamada de prepare()
            ));

        // D. Ensine o mock 1 (para o nome do colaborador)
        $mockStatement1->method('bindParam')->willReturn(true);
        $mockStatement1->method('execute')->willReturn(true);
        $mockStatement1->method('fetch')->willReturn($dadosFalsosNome);

        // E. Ensine o mock 2 (para a lista de holerites)
        $mockStatement2->method('bindParam')->willReturn(true);
        $mockStatement2->method('execute')->willReturn(true);
        $mockStatement2->method('fetchAll')->willReturn($dadosFalsosHolerites);


        // 2. EXECUÇÃO (Act)
        // Chame o método real
        $resultado = $this->model->buscarDadosParaTela($idFalso);


        // 3. VERIFICAÇÃO (Assert)
        // Verifique se a estrutura final da resposta está correta
        $resultadoEsperado = [
            'colaborador_nome' => 'Colaborador Tela Teste',
            'holerites' => $dadosFalsosHolerites
        ];
        $this->assertEquals($resultadoEsperado, $resultado);
    }
}