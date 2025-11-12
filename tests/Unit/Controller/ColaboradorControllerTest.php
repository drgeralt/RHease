<?php
declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Controller\ColaboradorController;
use App\Model\ColaboradorModel;
use App\Model\EnderecoModel;
use App\Model\CargoModel;
use App\Model\SetorModel;
use PDO;
use PDOStatement;

class TestableColaboradorController extends ColaboradorController
{
    public $viewChamada;
    public $viewData;
    public $redirecionamento;

    public function __construct(
        ColaboradorModel $colaboradorModel,
        EnderecoModel $enderecoModel,
        CargoModel $cargoModel,
        SetorModel $setorModel,
        PDO $pdo
    ) {
        $this->colaboradorModel = $colaboradorModel;
        $this->enderecoModel = $enderecoModel;
        $this->cargoModel = $cargoModel;
        $this->setorModel = $setorModel;
        $this->pdo = $pdo;
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

    protected function exit(): void {
    }
}

class ColaboradorControllerTest extends TestCase
{
    private $colaboradorModelMock;
    private $enderecoModelMock;
    private $cargoModelMock;
    private $setorModelMock;
    private $pdoMock;

    protected function setUp(): void
    {
        $this->colaboradorModelMock = $this->createMock(ColaboradorModel::class);
        $this->enderecoModelMock = $this->createMock(EnderecoModel::class);
        $this->cargoModelMock = $this->createMock(CargoModel::class);
        $this->setorModelMock = $this->createMock(SetorModel::class);
        $this->pdoMock = $this->createMock(PDO::class);
    }

    public function testListarCarregaViewCorretaComColaboradores()
    {
        $dadosFalsos = [
            ['id_colaborador' => 1, 'nome_completo' => 'Rhyan Teste'],
            ['id_colaborador' => 2, 'nome_completo' => 'Jarineura Teste']
        ];

        $this->colaboradorModelMock
            ->expects($this->once())
            ->method('listarColaboradores')
            ->willReturn($dadosFalsos);

        $controller = new TestableColaboradorController(
            $this->colaboradorModelMock,
            $this->enderecoModelMock,
            $this->cargoModelMock,
            $this->setorModelMock,
            $this->pdoMock
        );

        $controller->listar();

        $this->assertEquals('Colaborador/tabelaColaborador', $controller->viewChamada);
        $this->assertArrayHasKey('colaboradores', $controller->viewData);
        $this->assertEquals($dadosFalsos, $controller->viewData['colaboradores']);
    }

    public function testNovoCarregaViewDeCadastro()
    {
        $controller = new TestableColaboradorController(
            $this->colaboradorModelMock,
            $this->enderecoModelMock,
            $this->cargoModelMock,
            $this->setorModelMock,
            $this->pdoMock
        );

        $controller->novo();

        $this->assertEquals('Colaborador/cadastroColaborador', $controller->viewChamada);
    }

    public function testCriarChamaTodosOsModelsEComitaTransacao()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'nome_completo' => 'Novo Colaborador',
            'cargo' => 'Tester',
            'salario' => '1500,00',
            'setor' => 'QA',
        ];

        $this->pdoMock->expects($this->once())->method('beginTransaction');
        $this->pdoMock->expects($this->once())->method('commit');
        $this->pdoMock->expects($this->never())->method('rollBack');

        $this->enderecoModelMock->expects($this->once())->method('create')->willReturn(1);
        $this->cargoModelMock->expects($this->once())->method('findOrCreateByName')->willReturn(2);
        $this->setorModelMock->expects($this->once())->method('findOrCreateByName')->willReturn(3);
        $this->colaboradorModelMock->expects($this->once())->method('create');

        $controller = new TestableColaboradorController(
            $this->colaboradorModelMock,
            $this->enderecoModelMock,
            $this->cargoModelMock,
            $this->setorModelMock,
            $this->pdoMock
        );

        $controller->criar();
    }
}