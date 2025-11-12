<?php
declare(strict_types=1);

namespace Tests\Unit\Controller;

use PHPUnit\Framework\TestCase;
use App\Controller\AuthController;
use App\Model\AuthModel;
use PDO;

/**
 * Classe "Dublê" (Test Double) para o AuthController.
 * Ela nos permite isolar o teste do Core\Controller.
 */
class TestableAuthController extends AuthController
{
    public $viewChamada;
    public $viewData;
    public $redirecionamento;

    public function __construct(AuthModel $authModel, PDO $pdo)
    {
        // Pula o parent::__construct() para não precisar de $db_connection
        // e armazena os mocks
        $this->authModel = $authModel;
        // Não precisamos do PDO aqui, mas o mantemos para consistência
    }

    public function view($view, $data = []): void
    {
        $this->viewChamada = $view;
        $this->viewData = $data;
    }

    // Sobrescreve o header() e exit() para que o PHPUnit não pare
    protected function header(string $location): void
    {
        $this->redirecionamento = $location;
    }

    protected function exit(): void {
        // Não faz nada
    }
}

/**
 * Teste Unitário para o AuthController.
 */
class AuthControllerTest extends TestCase
{
    private $authModelMock;
    private $pdoMock;

    protected function setUp(): void
    {
        $this->authModelMock = $this->createMock(AuthModel::class);
        $this->pdoMock = $this->createMock(PDO::class);

        // Define a constante BASE_URL se ainda não estiver definida
        if (!defined('BASE_URL')) {
            define('BASE_URL', 'http://localhost/RHease/public');
        }

        // Limpa a sessão antes de cada teste
        $_SESSION = [];
        $_POST = [];
        $_SERVER = [];
    }

    public function testShowLoginCarregaViewDeLogin()
    {
        $controller = new TestableAuthController($this->authModelMock, $this->pdoMock);
        $controller->showLogin();

        $this->assertEquals('Auth/login', $controller->viewChamada);
    }

    public function testProcessLoginComSucessoParaGestorRedirecionaCorretamente()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = ['email_profissional' => 'gestor@empresa.com', 'senha' => 'senha123'];

        $dadosRetorno = [
            'status' => 'success',
            'user_id' => 1,
            'user_perfil' => 'gestor_rh'
        ];

        $this->authModelMock
            ->expects($this->once())
            ->method('loginUser')
            ->with('gestor@empresa.com', 'senha123')
            ->willReturn($dadosRetorno);

        $controller = new TestableAuthController($this->authModelMock, $this->pdoMock);
        $controller->processLogin();

        $this->assertEquals(1, $_SESSION['user_id']);
        $this->assertEquals('gestor_rh', $_SESSION['user_perfil']);
        $this->assertEquals('Location: ' . BASE_URL . '/inicio', $controller->redirecionamento);
    }

    public function testProcessLoginComSucessoParaColaboradorRedirecionaCorretamente()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = ['email_profissional' => 'colab@empresa.com', 'senha' => 'senha123'];

        $dadosRetorno = [
            'status' => 'success',
            'user_id' => 2,
            'user_perfil' => 'colaborador'
        ];

        $this->authModelMock
            ->expects($this->once())
            ->method('loginUser')
            ->with('colab@empresa.com', 'senha123')
            ->willReturn($dadosRetorno);

        $controller = new TestableAuthController($this->authModelMock, $this->pdoMock);
        $controller->processLogin();

        $this->assertEquals(2, $_SESSION['user_id']);
        $this->assertEquals('colaborador', $_SESSION['user_perfil']);
        // Baseado na sua última lógica, o colaborador também vai para /inicio
        $this->assertEquals('Location: ' . BASE_URL . '/inicio', $controller->redirecionamento);
    }

    public function testProcessLoginFalhoMostraMensagemDeErro()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = ['email_profissional' => 'user@empresa.com', 'senha' => 'senhaErrada'];

        $dadosRetorno = [
            'status' => 'error',
            'message' => 'E-mail ou senha inválidos.'
        ];

        $this->authModelMock
            ->expects($this->once())
            ->method('loginUser')
            ->willReturn($dadosRetorno);

        $controller = new TestableAuthController($this->authModelMock, $this->pdoMock);
        $controller->processLogin();

        $this->assertNull($controller->redirecionamento);
        $this->assertEquals('Auth/login', $controller->viewChamada);
        $this->assertArrayHasKey('error', $controller->viewData);
        $this->assertEquals('E-mail ou senha inválidos.', $controller->viewData['error']);
    }
}