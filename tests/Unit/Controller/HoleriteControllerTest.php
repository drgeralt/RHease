<?php
declare(strict_types=1);

namespace Tests\Unit\Controller;

use PHPUnit\Framework\TestCase;
use App\Controller\HoleriteController;
use App\Model\HoleriteModel;
use PDO;

class TestableHoleriteController extends HoleriteController
{
    public $viewChamada;
    public $viewData;
    public $redirecionamento;

    public function __construct(HoleriteModel $holeriteModel, PDO $pdo)
    {
        $this->model = $holeriteModel;
    }

    public function view($view, $data = []): void
    {
        $this->viewChamada = $view;
        $this->viewData = $data;
    }

    protected function header(string $location): void
    {
        $this->redirecionamento = $location;
    }

    protected function exit(): void { }
}

class HoleriteControllerTest extends TestCase
{
    private $modelMock;
    private $pdoMock;
    private $controller;

    protected function setUp(): void
    {
        $this->modelMock = $this->createMock(HoleriteModel::class);
        $this->pdoMock = $this->createMock(PDO::class);

        $this->controller = new TestableHoleriteController($this->modelMock, $this->pdoMock);

        if (!defined('BASE_URL')) {
            define('BASE_URL', 'http://localhost');
        }

        $_SESSION = [];
        $_POST = [];
    }

    public function testIndexRedirecionaSeUsuarioNaoLogado()
    {
        unset($_SESSION['user_id']);

        $this->controller->index();

        $this->assertEquals('Location: ' . BASE_URL . '/login', $this->controller->redirecionamento);
    }

    public function testIndexCarregaDadosCorretosParaUsuarioLogado()
    {
        $_SESSION['user_id'] = 123;
        $idUsuarioLogado = 123;

        $dadosColaboradorFalsos = ['id' => $idUsuarioLogado, 'nome' => 'Usuário Teste'];
        $dadosHoleritesFalsos = [
            ['id' => 1, 'mes' => 10, 'ano' => 2025],
            ['id' => 2, 'mes' => 11, 'ano' => 2025]
        ];

        $this->modelMock->method('findColaboradorById')
            ->with($idUsuarioLogado)
            ->willReturn($dadosColaboradorFalsos);

        $this->modelMock->method('findByColaboradorId')
            ->with($idUsuarioLogado)
            ->willReturn($dadosHoleritesFalsos);

        $this->controller->index();

        $this->assertEquals('Holerite/meusHolerites', $this->controller->viewChamada);
        $this->assertArrayHasKey('colaborador', $this->controller->viewData);
        $this->assertArrayHasKey('holerites', $this->controller->viewData);
        $this->assertEquals($dadosColaboradorFalsos, $this->controller->viewData['colaborador']);
        $this->assertEquals($dadosHoleritesFalsos, $this->controller->viewData['holerites']);
    }

    public function testGerarPdfLancaExcecaoSeDadosPostEstiveremAusentes()
    {
        $_POST = [];

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Dados insuficientes');

        $this->controller->gerarPDF();
    }

    public function testGerarPdfLancaExcecaoSeHoleriteNaoEncontrado()
    {
        $_POST['id_colaborador'] = 1;
        $_POST['mes'] = 10;
        $_POST['ano'] = 2025;

        $this->modelMock->method('findHoleritePorColaboradorEMes')
            ->willReturn(false);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Holerite não encontrado");

        $this->controller->gerarPDF();
    }
}