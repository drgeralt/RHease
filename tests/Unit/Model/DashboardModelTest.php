<?php
namespace Tests\Unit;

use App\Model\DashboardModel;
use PHPUnit\Framework\TestCase;

/**
* Execute: vendor/bin/phpunit tests/DashboardModelTest.php
 */
class DashboardModelTest extends TestCase
{
    private $dashboardModel;

    /**
     * Prepara o ambiente antes de cada teste
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Cria uma instância do model
        try {
            $this->dashboardModel = new DashboardModel();
        } catch (\Exception $e) {
            $this->markTestSkipped('Não foi possível conectar ao banco de dados: ' . $e->getMessage());
        }
    }

    /**
     * Testa se o método getGestorDashboardData retorna um array
     */
    public function testGetGestorDashboardDataRetornaArray()
    {
        $resultado = $this->dashboardModel->getGestorDashboardData();
        
        // Verifica se o retorno é um array
        $this->assertIsArray($resultado, 'O método deve retornar um array');
    }

    /**
     * Testa se o método retorna todas as chaves esperadas
     */
    public function testGetGestorDashboardDataTemTodasAsChaves()
    {
        $resultado = $this->dashboardModel->getGestorDashboardData();
        
        // Chaves que devem estar presentes
        $chavesEsperadas = [
            'total_colaboradores_ativos',
            'total_vagas_publicadas',
            'total_curriculos_processados',
            'total_beneficios_ativos',
            'distribuicao_contratos'
        ];
        
        foreach ($chavesEsperadas as $chave) {
            $this->assertArrayHasKey(
                $chave, 
                $resultado, 
                "A chave '{$chave}' deve estar presente no array retornado"
            );
        }
    }

    /**
     * Testa se os valores numéricos são válidos
     */
    public function testGetGestorDashboardDataValoresNumericos()
    {
        $resultado = $this->dashboardModel->getGestorDashboardData();
        
        // Verifica se os valores numéricos são não-negativos
        $this->assertGreaterThanOrEqual(0, $resultado['total_colaboradores_ativos']);
        $this->assertGreaterThanOrEqual(0, $resultado['total_vagas_publicadas']);
        $this->assertGreaterThanOrEqual(0, $resultado['total_curriculos_processados']);
        $this->assertGreaterThanOrEqual(0, $resultado['total_beneficios_ativos']);
    }

    /**
     * Testa se distribuicao_contratos é um array
     */
    public function testDistribuicaoContratosEhArray()
    {
        $resultado = $this->dashboardModel->getGestorDashboardData();
        
        $this->assertIsArray(
            $resultado['distribuicao_contratos'],
            'distribuicao_contratos deve ser um array'
        );
    }

    /**
     * Testa o método getUserRole com um ID inexistente
     */
    public function testGetUserRoleComIdInexistente()
    {
        // Usa um ID muito alto que provavelmente não existe
        $idInexistente = 999999;
        $role = $this->dashboardModel->getUserRole($idInexistente);
        
        // Deve retornar 'colaborador' como padrão
        $this->assertEquals(
            'colaborador', 
            $role,
            'getUserRole deve retornar "colaborador" como padrão para IDs inexistentes'
        );
    }

    /**
     * Testa se getUserRole retorna string ou null
     */
    public function testGetUserRoleRetornaTipoCorreto()
    {
        // Testa com ID 1 (assumindo que existe)
        $role = $this->dashboardModel->getUserRole(1);
        
        $this->assertTrue(
            is_string($role) || is_null($role),
            'getUserRole deve retornar string ou null'
        );
    }

    /**
     * Teste de integração simples: verifica se os dados fazem sentido juntos
     */
    public function testDadosDashboardSaoCoerentes()
    {
        $resultado = $this->dashboardModel->getGestorDashboardData();
        
        // Se há distribuição de contratos, deve haver colaboradores ativos
        if (!empty($resultado['distribuicao_contratos'])) {
            $totalDistribuicao = array_sum($resultado['distribuicao_contratos']);
            
            $this->assertEquals(
                $resultado['total_colaboradores_ativos'],
                $totalDistribuicao,
                'A soma da distribuição de contratos deve ser igual ao total de colaboradores ativos'
            );
        }
    }

    /**
     * Limpa o ambiente após cada teste
     */
    protected function tearDown(): void
    {
        $this->dashboardModel = null;
        parent::tearDown();
    }
}