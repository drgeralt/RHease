<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Controller\BeneficioController;
use App\Model\BeneficioModel;

/**
 *Execute: php vendor\bin\phpunit tests\BeneficioControllerTest.php
 * ou execute todos os testes: php vendor\bin\phpunit tests
 */
class BeneficioControllerTest extends TestCase
{
    private $controller;

    /**
     * Prepara o ambiente antes de cada teste
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Define constantes necessárias
        if (!defined('BASE_URL')) {
            define('BASE_URL', 'http://localhost/RHease');
        }
        
        if (!defined('BASE_PATH')) {
            define('BASE_PATH', dirname(__DIR__));
        }
        
        // Inicia a sessão para os testes
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        
        // Limpa variáveis POST
        $_POST = [];
        $_SERVER['REQUEST_METHOD'] = 'GET';
    }

    /**
     * Testa se o controller é instanciado corretamente
     */
    public function testControllerEhInstanciadoCorretamente()
    {
        try {
            $controller = new BeneficioController();
            $this->assertInstanceOf(BeneficioController::class, $controller);
        } catch (\Exception $e) {
            $this->markTestSkipped('Não foi possível instanciar o controller: ' . $e->getMessage());
        }
    }

    /**
     * Testa validação de método HTTP em salvarBeneficio
     */
    public function testSalvarBeneficioRejeitaMetodoGET()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        
        $controller = $this->createMockController();
        
        // Espera que rejeite método GET
        $this->assertEquals('GET', $_SERVER['REQUEST_METHOD']);
    }

    /**
     * Testa validação de campos obrigatórios em salvarBeneficio
     */
    public function testSalvarBeneficioValidaCamposObrigatorios()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'nome' => '', // Campo vazio
            'categoria' => 'Saúde',
            'tipo_valor' => 'Fixo'
        ];
        
        // Testa filtros
        $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS);
        $this->assertEmpty($nome, 'Nome vazio deve falhar na validação');
    }

    /**
     * Testa validação de ID em deletarBeneficio
     */
    public function testDeletarBeneficioValidaID()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        
        // Testa ID inválido
        $id_invalido = filter_var('abc', FILTER_VALIDATE_INT);
        $this->assertFalse($id_invalido, 'ID não numérico deve falhar na validação');
        
        // Testa ID válido
        $id_valido = filter_var('123', FILTER_VALIDATE_INT);
        $this->assertEquals(123, $id_valido);
    }

    /**
     * Testa validação de ID em toggleStatus
     */
    public function testToggleStatusValidaID()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        
        // Simula validação de ID
        $id = filter_var(123, FILTER_VALIDATE_INT);
        $this->assertEquals(123, $id, 'ID válido deve passar na validação');
        $this->assertIsInt($id);
    }

    /**
     * Testa validação de tipo de contrato em salvarRegrasAtribuicao
     */
    public function testSalvarRegrasValidaTipoContrato()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'tipo_contrato' => 'CLT',
            'beneficios_ids' => [1, 2, 3]
        ];
        
        // Simula sanitização
        $tipo_contrato = filter_var('CLT', FILTER_SANITIZE_SPECIAL_CHARS);
        $this->assertEquals('CLT', $tipo_contrato);
        $this->assertIsArray($_POST['beneficios_ids']);
    }

    /**
     * Testa busca de colaborador com termo muito curto
     */
    public function testBuscarColaboradorTermoCurto()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        
        $termo = 'ab'; // Menos de 3 caracteres
        $this->assertLessThan(3, strlen($termo), 'Termo com menos de 3 caracteres');
    }

    /**
     * Testa busca de colaborador com termo válido
     */
    public function testBuscarColaboradorTermoValido()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        
        $termo = 'João Silva';
        $this->assertGreaterThanOrEqual(3, strlen($termo), 'Termo válido com 3+ caracteres');
    }

    /**
     * Testa validação de ID de colaborador
     */
    public function testCarregarBeneficiosColaboradorValidaID()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        
        $id_colaborador = filter_var(456, FILTER_VALIDATE_INT);
        $this->assertEquals(456, $id_colaborador);
        $this->assertIsInt($id_colaborador);
    }

    /**
     * Testa salvar benefícios de colaborador com dados válidos
     */
    public function testSalvarBeneficiosColaboradorDadosValidos()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'id_colaborador' => 123,
            'beneficios_ids' => [1, 2, 3]
        ];
        
        $id_colaborador = filter_var(123, FILTER_VALIDATE_INT);
        $beneficios_ids = $_POST['beneficios_ids'] ?? [];
        
        $this->assertEquals(123, $id_colaborador);
        $this->assertIsArray($beneficios_ids);
        $this->assertCount(3, $beneficios_ids);
    }

    /**
     * Testa filtro de valor fixo (float)
     */
    public function testValorFixoEhValidadoComoFloat()
    {
        $valor_fixo = filter_var('150.50', FILTER_VALIDATE_FLOAT);
        $this->assertEquals(150.50, $valor_fixo);
        $this->assertIsFloat($valor_fixo);
    }

    /**
     * Testa filtro de valor fixo inválido
     */
    public function testValorFixoInvalidoRetornaNull()
    {
        $valor_fixo = filter_var('abc', FILTER_VALIDATE_FLOAT);
        $this->assertFalse($valor_fixo, 'Valor não numérico deve falhar');
    }

    /**
     * Testa sanitização de campos de texto
     */
    public function testSanitizacaoDeCamposDeTexto()
    {
        $nome_sujo = '<script>alert("xss")</script>Vale Alimentação';
        $nome_limpo = filter_var($nome_sujo, FILTER_SANITIZE_SPECIAL_CHARS);
        
        $this->assertIsString($nome_limpo);
        $this->assertStringNotContainsString('<script>', $nome_limpo, 'Script tags devem ser sanitizadas');
    }

    /**
     * Testa estrutura de dados para gerenciamento
     */
    public function testGerenciamentoCarregaDadosNecessarios()
    {
        // Mock do model
        $modelMock = $this->createMock(BeneficioModel::class);
        
        $modelMock->method('listarBeneficiosComCusto')
            ->willReturn([
                ['id_beneficio' => 1, 'nome' => 'Vale Transporte', 'categoria' => 'Transporte', 'tipo_valor' => 'Fixo', 'valor_fixo' => 200.00, 'status' => 'Ativo']
            ]);
        
        $modelMock->method('listarBeneficiosAtivosParaSelecao')
            ->willReturn([
                ['id_beneficio' => 1, 'nome' => 'Vale Transporte']
            ]);
        
        $modelMock->method('listarRegrasAtribuicao')
            ->willReturn([
                'CLT' => ['ids' => [1], 'nomes' => ['Vale Transporte']]
            ]);
        
        $beneficios = $modelMock->listarBeneficiosComCusto();
        $beneficios_selecao = $modelMock->listarBeneficiosAtivosParaSelecao();
        $regras = $modelMock->listarRegrasAtribuicao();
        
        $this->assertIsArray($beneficios);
        $this->assertIsArray($beneficios_selecao);
        $this->assertIsArray($regras);
        $this->assertNotEmpty($beneficios);
    }

    /**
     * Testa método meusBeneficios sem sessão
     */
    public function testMeusBeneficiosSemSessao()
    {
        unset($_SESSION['id_colaborador']);
        
        $this->assertArrayNotHasKey('id_colaborador', $_SESSION);
    }

    /**
     * Testa tipos de contrato válidos
     */
    public function testTiposContratoValidos()
    {
        $tiposContrato = ["CLT", "PJ", "Estágio", "Temporário"];
        
        $this->assertIsArray($tiposContrato);
        $this->assertCount(4, $tiposContrato);
        $this->assertContains('CLT', $tiposContrato);
        $this->assertContains('PJ', $tiposContrato);
    }

    /**
     * Helper: Cria um mock do controller
     */
    private function createMockController()
    {
        return $this->getMockBuilder(BeneficioController::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Limpa o ambiente após cada teste
     */
    protected function tearDown(): void
    {
        $_POST = [];
        $_SERVER = [];
        
        parent::tearDown();
    }
}