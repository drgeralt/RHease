<?php
declare(strict_types=1);

namespace Tests\Unit\Controller;

use PHPUnit\Framework\TestCase;
use App\Controller\GestaoVagasController;
use App\Model\GestaoVagasModel;
use App\Model\CandidaturaModel;
use PDO;

class TestableGestaoVagasController extends GestaoVagasController
{
    public $viewChamada;
    public $viewData;

    public function __construct(
        GestaoVagasModel $vagasModel,
        CandidaturaModel $candidaturaModel,
        PDO $pdo
    ) {
        $this->vagasModel = $vagasModel;
        $this->candidaturaModel = $candidaturaModel;
        $this->pdo = $pdo;
    }

    public function view($view, $data = []): void
    {
        $this->viewChamada = $view;
        $this->viewData = $data;
    }

    protected function header(string $location): void {}
    protected function exit(): void {}
}

class GestaoVagasControllerTest extends TestCase
{
    private $vagasModelMock;
    private $candidaturaModelMock;
    private $pdoMock;
    private $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->vagasModelMock = $this->createMock(GestaoVagasModel::class);
        $this->candidaturaModelMock = $this->createMock(CandidaturaModel::class);
        $this->pdoMock = $this->createMock(PDO::class);

        $this->controller = new TestableGestaoVagasController(
            $this->vagasModelMock,
            $this->candidaturaModelMock,
            $this->pdoMock
        );
    }

    public function testListarVagasChamaViewCorretaComDados(): void
    {
        $dadosVagasFalsas = [['id_vaga' => 1, 'titulo_vaga' => 'Vaga Teste']];

        $this->vagasModelMock
            ->expects($this->once())
            ->method('listarVagas')
            ->willReturn($dadosVagasFalsas);

        $this->controller->listarVagas();

        $this->assertEquals('vaga/gestaoVagas', $this->controller->viewChamada);
        $this->assertArrayHasKey('vagas', $this->controller->viewData);
        $this->assertEquals($dadosVagasFalsas, $this->controller->viewData['vagas']);
    }

    public function testCriarChamaViewDeNovaVaga(): void
    {
        $this->controller->criar();
        $this->assertEquals('vaga/novaVaga', $this->controller->viewChamada);
    }

    public function testEditarChamaViewDeEdicao(): void
    {
        $this->controller->editar();
        $this->assertEquals('vaga/editarVaga', $this->controller->viewChamada);
    }

    public function testVerCandidatosChamaViewCorretaComDados(): void
    {
        $_GET['id'] = 1;
        $dadosVagaFalsa = ['id_vaga' => 1, 'titulo_vaga' => 'Vaga Teste'];
        $dadosCandidatosFalsos = [['id_candidato' => 10, 'nome_completo' => 'Candidato Teste']];

        $this->vagasModelMock
            ->expects($this->once())
            ->method('buscarPorId')
            ->with(1)
            ->willReturn($dadosVagaFalsa);

        $this->candidaturaModelMock
            ->expects($this->once())
            ->method('buscarPorVaga')
            ->with(1)
            ->willReturn($dadosCandidatosFalsos);

        $this->controller->verCandidatos();

        $this->assertEquals('Candidatura/lista_candidatos', $this->controller->viewChamada);
        $this->assertArrayHasKey('vaga', $this->controller->viewData);
        $this->assertArrayHasKey('candidatos', $this->controller->viewData);
        $this->assertEquals($dadosVagaFalsa, $this->controller->viewData['vaga']);
        $this->assertEquals($dadosCandidatosFalsos, $this->controller->viewData['candidatos']);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($_GET['id']);
    }
}