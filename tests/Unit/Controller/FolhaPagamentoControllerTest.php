<?php
// tests/FolhaPagamentoControllerTest.php

namespace Unit\Controller; // (Confirme se o namespace está certo)

use App\Controller\FolhaPagamentoController;
use App\Service\Implementations\FolhaPagamentoService;
use PHPUnit\Framework\TestCase;

// Importe o Service

class FolhaPagamentoControllerTest extends TestCase
{
    private $mockService; // O Mock do nosso Service
    private $controller;  // O Controller sendo testado

    /**
     * Este método setUp() é executado ANTES de cada teste.
     * Ele prepara o ambiente para nós.
     */
    protected function setUp(): void
    {
        // 1. Crie um Mock do Service (a dependência que queremos controlar)
        //
        $this->mockService = $this->createMock(FolhaPagamentoService::class);

        // 2. Crie um Mock do Controller
        $this->controller = $this->getMockBuilder(FolhaPagamentoController::class)
            ->disableOriginalConstructor() // Ignora o __construct()
            // Vamos mockar o método 'view' (da classe pai)
            ->onlyMethods(['view'])
            ->getMock();

        // 3. Injete o Mock Service no Controller
        // (Usando Reflection, como aprendemos, pois a propriedade é private)
        $reflection = new \ReflectionClass(FolhaPagamentoController::class);
        $property = $reflection->getProperty('folhaPagamentoService');
        $property->setValue($this->controller, $this->mockService);
    }

    /**
     * TESTE 1: Verifica se o método index() chama a view correta.
     */
    public function testIndexCarregaViewCorreta()
    {
        // 1. VERIFICAÇÃO (Assert)
        // Nós esperamos que o método 'view' seja chamado
        // exatamente uma vez, com estes argumentos:
        $this->controller->expects($this->once())
            ->method('view')
            ->with(
                'FolhaPagamento/processarFolha', //
                ['data' => []]                     //
            );

        // 2. EXECUÇÃO (Act)
        $this->controller->index();
    }

    /**
     * TESTE 2: Verifica se o processar() mostra erro com data inválida.
     * (O "caminho triste" da validação)
     */
    public function testProcessarFalhaComDataInvalida()
    {
        // 1. PREPARAÇÃO (Arrange)
        // Force o $_POST a estar vazio para falhar no 'if'
        $_POST = [];

        // 2. VERIFICAÇÃO (Assert)
        // Esperamos que a 'view' seja chamada com a mensagem de erro
        $this->controller->expects($this->once())
            ->method('view')
            ->with(
                'FolhaPagamento/processarFolha',
                ['erro' => 'Por favor, insira um ano e mês válidos.'] //
            );

        // 3. EXECUÇÃO (Act)
        $this->controller->processar();
    }
    /**
     * TESTE 3: Verifica o "caminho feliz" do processamento.
     */
    public function testProcessarSucesso()
    {
        // 1. PREPARAÇÃO (Arrange)

        // A. Forneça um $_POST válido
        $ano = 2025;
        $mes = 10;
        $_POST['ano'] = $ano;
        $_POST['mes'] = $mes;

        // B. Defina os resultados falsos que o Service deve retornar
        $resultadosFalsos = [
            'sucesso' => ['colaborador1', 'colaborador2'], // 2 sucessos
            'falha' => ['colaborador3']                   // 1 falha
        ];

        // C. "Ensine" o Mock Service (que já foi criado no setUp)
        // "Quando 'processarFolha' for chamado com 2025 e 10..."
        $this->mockService->method('processarFolha')
            ->with($ano, $mes)
            // "...retorne os nossos $resultadosFalsos."
            ->willReturn($resultadosFalsos);

        // D. Calcule a mensagem de sucesso exata que esperamos
        $mesFormatado = str_pad((string)$mes, 2, '0', STR_PAD_LEFT);
        $mensagemEsperada = "Folha de pagamento para $mesFormatado/$ano processada!<br>";
        $mensagemEsperada .= "Colaboradores processados com sucesso: 2.<br>";
        $mensagemEsperada .= "Colaboradores com falha: 1.";


        // 2. VERIFICAÇÃO (Assert)
        // Esperamos que a 'view' seja chamada com a mensagem de SUCESSO
        $this->controller->expects($this->once())
            ->method('view')
            ->with(
                'FolhaPagamento/processarFolha',
                ['sucesso' => $mensagemEsperada] //
            );


        // 3. EXECUÇÃO (Act)
        $this->controller->processar();
    }
    /**
     * TESTE 4: Verifica se o 'catch' é ativado se o Service falhar.
     */
    public function testProcessarApanhaExcecaoDoService()
    {
        // 1. PREPARAÇÃO (Arrange)

        // A. Forneça um $_POST válido (para passar da primeira validação)
        $ano = 2025;
        $mes = 10;
        $_POST['ano'] = $ano;
        $_POST['mes'] = $mes;

        // B. Defina a mensagem de erro que esperamos
        $mensagemErroFalsa = "Erro simulado do banco de dados";

        // C. "Ensine" o Mock Service a LANÇAR UMA EXCEÇÃO
        // "Quando 'processarFolha' for chamado..."
        $this->mockService->method('processarFolha')
            // "...lance uma nova Exceção com esta mensagem."
            ->will($this->throwException(new \Exception($mensagemErroFalsa)));


        $this->controller->expects($this->once())
            ->method('view')
            ->with(
                'FolhaPagamento/processarFolha',
                // O erro exato do bloco 'catch'
                // CORRIGIDO:
                ['erro' => 'Ocorreu um erro: ' . $mensagemErroFalsa]
            );


        // 3. EXECUÇÃO (Act)
        // Quando chamarmos isto, o mock vai lançar a exceção,
        // o 'try' vai falhar, e o 'catch' será executado.
        $this->controller->processar();
    }
}