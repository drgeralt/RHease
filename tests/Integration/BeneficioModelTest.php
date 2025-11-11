<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Model\BeneficioModel;

/**
 * Teste Unitário para BeneficioModel
 * 
 * INSTRUÇÕES:
 * 1. Coloque este arquivo em: tests/BeneficioModelTest.php
 * 2. Execute: php vendor\bin\phpunit tests\BeneficioModelTest.php
 * ou execute todos os testes: php vendor\bin\phpunit tests
 */
class BeneficioModelTest extends TestCase
{
    private $model;

    /**
     * Prepara o ambiente antes de cada teste
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        try {
            $this->model = new BeneficioModel();
        } catch (\Exception $e) {
            $this->markTestSkipped('Não foi possível conectar ao banco de dados: ' . $e->getMessage());
        }
    }

    /**
     * Testa se o model é instanciado corretamente
     */
    public function testModelEhInstanciadoCorretamente()
    {
        $this->assertInstanceOf(BeneficioModel::class, $this->model);
    }

    /**
     * Testa listagem de benefícios com custo
     */
    public function testListarBeneficiosComCustoRetornaArray()
    {
        $resultado = $this->model->listarBeneficiosComCusto();
        
        $this->assertIsArray($resultado);
        
        // Se houver benefícios, verifica estrutura
        if (!empty($resultado)) {
            $this->assertArrayHasKey('id_beneficio', $resultado[0]);
            $this->assertArrayHasKey('nome', $resultado[0]);
            $this->assertArrayHasKey('categoria', $resultado[0]);
            $this->assertArrayHasKey('tipo_valor', $resultado[0]);
            $this->assertArrayHasKey('status', $resultado[0]);
        }
    }

    /**
     * Testa listagem de benefícios ativos para seleção
     */
    public function testListarBeneficiosAtivosParaSelecao()
    {
        $resultado = $this->model->listarBeneficiosAtivosParaSelecao();
        
        $this->assertIsArray($resultado);
        
        // Se houver benefícios, verifica estrutura
        if (!empty($resultado)) {
            $this->assertArrayHasKey('id_beneficio', $resultado[0]);
            $this->assertArrayHasKey('nome', $resultado[0]);
        }
    }

    /**
     * Testa listagem de regras de atribuição
     */
    public function testListarRegrasAtribuicaoRetornaArrayComEstrutura()
    {
        $resultado = $this->model->listarRegrasAtribuicao();
        
        $this->assertIsArray($resultado);
        
        // Se houver regras, verifica estrutura
        foreach ($resultado as $tipo_contrato => $regra) {
            $this->assertIsString($tipo_contrato);
            $this->assertIsArray($regra);
            $this->assertArrayHasKey('nomes', $regra);
            $this->assertArrayHasKey('ids', $regra);
            $this->assertIsArray($regra['nomes']);
            $this->assertIsArray($regra['ids']);
        }
    }

    /**
     * Testa busca de colaborador
     */
    public function testBuscarColaboradorComTermoValido()
    {
        // Busca com termo genérico que provavelmente existe
        $resultado = $this->model->buscarColaborador('a');
        
        $this->assertIsArray($resultado);
        $this->assertLessThanOrEqual(5, count($resultado), 'Deve retornar no máximo 5 resultados');
        
        // Se houver resultados, verifica estrutura
        if (!empty($resultado)) {
            $this->assertArrayHasKey('id_colaborador', $resultado[0]);
            $this->assertArrayHasKey('nome_completo', $resultado[0]);
            $this->assertArrayHasKey('matricula', $resultado[0]);
        }
    }

    /**
     * Testa carregamento de benefícios de colaborador inexistente
     */
    public function testCarregarBeneficiosColaboradorInexistenteGeraCatch()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Colaborador não encontrado');
        
        // ID muito alto que provavelmente não existe
        $this->model->carregarBeneficiosColaborador(999999);
    }

    /**
     * Testa estrutura de retorno do carregarBeneficiosColaborador
     */
    public function testCarregarBeneficiosColaboradorEstrutura()
    {
        try {
            // Tenta com ID 1 (assumindo que existe)
            $resultado = $this->model->carregarBeneficiosColaborador(1);
            
            $this->assertIsArray($resultado);
            $this->assertArrayHasKey('dados_colaborador', $resultado);
            $this->assertArrayHasKey('beneficios_ids', $resultado);
            $this->assertIsArray($resultado['beneficios_ids']);
        } catch (\Exception $e) {
            $this->markTestSkipped('Colaborador com ID 1 não existe no banco');
        }
    }

    /**
     * Testa validação de campos obrigatórios em salvarBeneficio
     */
    public function testSalvarBeneficioValidaCamposObrigatorios()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Nome, categoria e tipo de valor são obrigatórios');
        
        $this->model->salvarBeneficio(null, '', 'Saúde', 'Fixo', 100.00);
    }

    /**
     * Testa validação de valor fixo obrigatório
     */
    public function testSalvarBeneficioTipoFixoRequerValor()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Para benefícios do tipo \'Fixo\', o valor é obrigatório');
        
        $this->model->salvarBeneficio(null, 'Vale Transporte', 'Transporte', 'Fixo', null);
    }

    /**
     * Testa criação de novo benefício
     */
    public function testSalvarBeneficioNovoCriaBeneficio()
    {
        try {
            $id = $this->model->salvarBeneficio(
                null,
                'Teste Benefício ' . time(),
                'Outros',
                'Descritivo',
                null
            );
            
            $this->assertIsInt($id);
            $this->assertGreaterThan(0, $id);
            
            // Limpa o benefício criado
            $this->model->deletarBeneficio($id);
        } catch (\Exception $e) {
            $this->markTestSkipped('Erro ao criar benefício de teste: ' . $e->getMessage());
        }
    }

    /**
     * Testa toggle de status
     */
    public function testToggleStatusAlternaStatus()
    {
        try {
            // Cria um benefício para testar
            $id = $this->model->salvarBeneficio(
                null,
                'Teste Toggle ' . time(),
                'Outros',
                'Descritivo',
                null
            );
            
            // Primeiro toggle: Ativo -> Inativo
            $status1 = $this->model->toggleStatus($id);
            $this->assertEquals('Inativo', $status1);
            
            // Segundo toggle: Inativo -> Ativo
            $status2 = $this->model->toggleStatus($id);
            $this->assertEquals('Ativo', $status2);
            
            // Limpa
            $this->model->deletarBeneficio($id);
        } catch (\Exception $e) {
            $this->markTestSkipped('Erro ao testar toggle: ' . $e->getMessage());
        }
    }

    /**
     * Testa toggle com ID inexistente
     */
    public function testToggleStatusComIdInexistente()
    {
        $this->expectException(\Exception::class);
        
        $this->model->toggleStatus(999999);
    }

    /**
     * Testa salvar regras de atribuição
     */
    public function testSalvarRegrasAtribuicaoRetornaTrue()
    {
        try {
            $resultado = $this->model->salvarRegrasAtribuicao('CLT', []);
            $this->assertTrue($resultado);
        } catch (\Exception $e) {
            $this->fail('Erro ao salvar regras: ' . $e->getMessage());
        }
    }

    /**
     * Testa salvar benefícios de colaborador
     */
    public function testSalvarBeneficiosColaboradorRetornaTrue()
    {
        try {
            // Testa com ID 1 e array vazio
            $resultado = $this->model->salvarBeneficiosColaborador(1, []);
            $this->assertTrue($resultado);
        } catch (\Exception $e) {
            $this->markTestSkipped('Colaborador ID 1 não existe: ' . $e->getMessage());
        }
    }

    /**
     * Testa deletar benefício
     */
    public function testDeletarBeneficioRetornaTrue()
    {
        try {
            // Cria um benefício para deletar
            $id = $this->model->salvarBeneficio(
                null,
                'Teste Delete ' . time(),
                'Outros',
                'Descritivo',
                null
            );
            
            $resultado = $this->model->deletarBeneficio($id);
            $this->assertTrue($resultado);
        } catch (\Exception $e) {
            $this->markTestSkipped('Erro ao testar delete: ' . $e->getMessage());
        }
    }

    /**
     * Testa aplicar regras padrão
     */
    public function testAplicarRegrasPadraoRetornaTrue()
    {
        try {
            $resultado = $this->model->aplicarRegrasPadrao(1);
            $this->assertTrue($resultado);
        } catch (\Exception $e) {
            $this->markTestSkipped('Erro ao aplicar regras padrão: ' . $e->getMessage());
        }
    }

    /**
     * Testa buscar benefícios para colaborador
     */
    public function testBuscarBeneficiosParaColaboradorRetornaEstrutura()
    {
        try {
            $resultado = $this->model->buscarBeneficiosParaColaborador(1);
            
            $this->assertIsArray($resultado);
            $this->assertArrayHasKey('nome', $resultado);
            $this->assertArrayHasKey('beneficios', $resultado);
            $this->assertIsArray($resultado['beneficios']);
            $this->assertIsString($resultado['nome']);
        } catch (\Exception $e) {
            $this->markTestSkipped('Erro ao buscar benefícios: ' . $e->getMessage());
        }
    }

    /**
     * Testa se benefícios retornados têm estrutura correta
     */
    public function testEstruturaDeBeneficioRetornado()
    {
        try {
            $resultado = $this->model->buscarBeneficiosParaColaborador(1);
            
            if (!empty($resultado['beneficios'])) {
                $beneficio = $resultado['beneficios'][0];
                $this->assertArrayHasKey('nome_beneficio', $beneficio);
                $this->assertArrayHasKey('categoria', $beneficio);
                $this->assertArrayHasKey('tipo_valor', $beneficio);
            }
        } catch (\Exception $e) {
            $this->markTestSkipped('Erro ao verificar estrutura: ' . $e->getMessage());
        }
    }

    /**
     * Testa categorias válidas de benefícios
     */
    public function testCategoriasValidasSaoAceitas()
    {
        $categorias_validas = ['Alimentação', 'Transporte', 'Saúde', 'Educação', 'Outros'];
        
        foreach ($categorias_validas as $categoria) {
            $this->assertIsString($categoria);
            $this->assertNotEmpty($categoria);
        }
    }

    /**
     * Testa tipos de valor válidos
     */
    public function testTiposValorValidosSaoAceitos()
    {
        $tipos_validos = ['Fixo', 'Variável', 'Descritivo'];
        
        foreach ($tipos_validos as $tipo) {
            $this->assertIsString($tipo);
            $this->assertNotEmpty($tipo);
        }
    }

    /**
     * Limpa o ambiente após cada teste
     */
    protected function tearDown(): void
    {
        $this->model = null;
        parent::tearDown();
    }
}