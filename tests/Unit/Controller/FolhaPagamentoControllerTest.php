<?php
declare(strict_types=1);

namespace Tests\Unit\Controller;

use PHPUnit\Framework\TestCase;
use App\Controller\FolhaPagamentoController;
use App\Service\Implementations\FolhaPagamentoService;
use PDO;

class TestableFolhaPagamentoController extends FolhaPagamentoController
{
    public $viewChamada;
    public $viewData;

    public function __construct(FolhaPagamentoService $folhaPagamentoService, PDO $pdo)
    {
        $this->folhaPagamentoService = $folhaPagamentoService;
    }

    public function view($view, $data = []): void
    {
        $this->viewChamada = $view;
        $this->viewData = $data;
    }

    protected function header(string $location): void {}
    protected function exit(): void {}
}

class FolhaPagamentoControllerTest extends TestCase
{
    private $mockService;
    private $pdoMock;
    private $controller;

    protected function setUp(): void
    {
        $this->mockService = $this->createMock(FolhaPagamentoService::class);
        $this->pdoMock = $this->createMock(PDO::class);

        $this->controller = new TestableFolhaPagamentoController(
            $this->mockService,
            $this->pdoMock
        );
    }

    public function testIndexCarregaViewCorreta()
    {
        $this->controller->expects($this->once())
            ->method('view')
            ->with(
                'FolhaPagamento/processarFolha',
                ['data' => []]
            );

        $this->controller->index();
    }

    public function testProcessarFalhaComDataInvalida()
    {
        $_POST = [];

        $this->controller->expects($this->once())
            ->method('view')
            ->with(
                'FolhaPagamento/processarFolha',
                ['erro' => 'Por favor, insira um ano e mês válidos.']
            );

        $this->controller->processar();
    }

    public function testProcessarSucesso()
    {
        $ano = 2025;
        $mes = 10;
        $_POST['ano'] = $ano;
        $_POST['mes'] = $mes;

        $resultadosFalsos = [
            'sucesso' => ['colaborador1', 'colaborador2'],
            'falha' => ['colaborador3']
        ];

        $this->mockService->method('processarFolha')
            ->with($ano, $mes)
            ->willReturn($resultadosFalsos);

        $mesFormatado = str_pad((string)$mes, 2, '0', STR_PAD_LEFT);
        $mensagemEsperada = "Folha de pagamento para $mesFormatado/$ano processada!<br>";
        $mensagemEsperada .= "Colaboradores processados com sucesso: 2.<br>";
        $mensagemEsperada .= "Colaboradores com falha: 1.";

        $this->controller->expects($this->once())
            ->method('view')
            ->with(
                'FolhaPagamento/processarFolha',
                ['sucesso' => $mensagemEsperada]
            );

        $this->controller->processar();
    }

    public function testProcessarApanhaExcecaoDoService()
    {
        $ano = 2025;
        $mes = 10;
        $_POST['ano'] = $ano;
        $_POST['mes'] = $mes;

        $mensagemErroFalsa = "Erro simulado do banco de dados";

        $this->mockService->method('processarFolha')
            ->will($this->throwException(new \Exception($mensagemErroFalsa)));

        $this->controller->expects($this->once())
            ->method('view')
            ->with(
                'FolhaPagamento/processarFolha',
                ['erro' => 'Ocorreu um erro: ' . $mensagemErroFalsa]
            );

        $this->controller->processar();
    }
}