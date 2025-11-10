<?php

declare(strict_types=1);

namespace Tests\Unit\Controller;

use App\Controller\GestaoVagasController;
use PHPUnit\Framework\TestCase;

/**
 * Testes Unitários para GestaoVagasController
 * 
 * COMO FUNCIONA:
 * ==============
 * 
 * O Controller é responsável por renderizar views (páginas).
 * Para testar, usamos mocks parciais que interceptam apenas o método view(),
 * permitindo verificar se a view correta foi chamada sem realmente renderizar HTML.
 * 
 * OPERAÇÕES TESTADAS:
 * ===================
 * - listarVagas(): Renderiza a view de listagem de vagas
 * - criar(): Renderiza a view de criação de nova vaga
 * - editar(): Renderiza a view de edição de vaga
 * - verCandidatos(): Renderiza a view de lista de candidatos
 */
class GestaoVagasControllerTest extends TestCase
{
    private GestaoVagasController $controller;

    /**
     * setUp() é executado ANTES de cada teste
     * 
     * Criamos um mock parcial do controller que intercepta apenas o método view()
     * Isso permite testar se a view correta foi chamada sem renderizar HTML real
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Criar mock parcial do controller
        // onlyMethods(['view']) significa que apenas o método 'view' será mockado
        // Os outros métodos (listarVagas, criar, etc.) serão executados normalmente
        $this->controller = $this->getMockBuilder(GestaoVagasController::class)
            ->onlyMethods(['view'])
            ->getMock();
    }

    /**
     * TESTE 1: listarVagas()
     * 
     * OBJETIVO: Verificar se o método chama a view correta para listar vagas
     * 
     * COMO TESTA:
     * 1. Configuramos o mock para esperar uma chamada ao método view()
     * 2. Especificamos qual view deve ser chamada ('vaga/gestaoVagas')
     * 3. Executamos o método listarVagas()
     * 4. O mock verifica automaticamente se a view correta foi chamada
     */
    public function testListarVagasChamaViewCorreta(): void
    {
        // ARRANGE & ASSERT: Configurar expectativa do mock
        // Espera que o método view() seja chamado 1 vez com o parâmetro 'vaga/gestaoVagas'
        $this->controller
            ->expects($this->once())  // Deve ser chamado exatamente 1 vez
            ->method('view')
            ->with('vaga/gestaoVagas');  // Com este parâmetro específico

        // ACT: Executar o método sendo testado
        $this->controller->listarVagas();
        
        // Se chegou aqui sem erro, o teste passou!
        // O mock já verificou se view() foi chamado corretamente
    }

    /**
     * TESTE 2: criar()
     * 
     * OBJETIVO: Verificar se o método chama a view de criação de vaga
     */
    public function testCriarChamaViewDeNovaVaga(): void
    {
        // ARRANGE & ASSERT
        $this->controller
            ->expects($this->once())
            ->method('view')
            ->with('vaga/novaVaga');  // View esperada para criar nova vaga

        // ACT
        $this->controller->criar();
    }

    /**
     * TESTE 3: editar()
     * 
     * OBJETIVO: Verificar se o método chama a view de edição de vaga
     */
    public function testEditarChamaViewDeEdicao(): void
    {
        // ARRANGE & ASSERT
        $this->controller
            ->expects($this->once())
            ->method('view')
            ->with('vaga/editarVaga');  // View esperada para editar vaga

        // ACT
        $this->controller->editar();
    }

    /**
     * TESTE 4: verCandidatos()
     * 
     * OBJETIVO: Verificar se o método chama a view de lista de candidatos
     */
    public function testVerCandidatosChamaViewDeListaCandidatos(): void
    {
        // ARRANGE & ASSERT
        $this->controller
            ->expects($this->once())
            ->method('view')
            ->with('Candidatura/lista_candidatos');  // View esperada para ver candidatos

        // ACT
        $this->controller->verCandidatos();
    }

    /**
     * tearDown() é executado DEPOIS de cada teste
     */
    protected function tearDown(): void
    {
        parent::tearDown();
    }
}

