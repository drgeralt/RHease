<?php
declare(strict_types=1);

namespace Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use App\Service\Implementations\AnalisadorCurriculoService;
use GeminiAPI\Client;
use GeminiAPI\GenerativeModel;
use GeminiAPI\Resources\GenerateContentResponse;
use Exception;

class AnalisadorCurriculoServiceTest extends TestCase
{
    private $geminiClientMock;
    private $generativeModelMock;
    private $responseMock;
    private $service;
    private $dadosVagaFake;

    protected function setUp(): void
    {
        $this->geminiClientMock = $this->createMock(Client::class);
        $this->generativeModelMock = $this->createMock(GenerativeModel::class);
        $this->responseMock = $this->createMock(GenerateContentResponse::class);

        $this->geminiClientMock->method('generativeModel')
            ->willReturn($this->generativeModelMock);

        $this->generativeModelMock->method('generateContent')
            ->willReturn($this->responseMock);

        $this->service = new AnalisadorCurriculoService($this->geminiClientMock);

        $this->dadosVagaFake = [
            'titulo_vaga' => 'Engenheiro de Software',
            'descricao_vaga' => 'Desenvolver software.',
            'requisitos_necessarios' => 'PHP, SQL',
            'requisitos_recomendados' => 'Docker',
            'requisitos_desejados' => 'Kubernetes'
        ];
    }

    public function testAnalisarRetornaSucessoComJSONValido()
    {
        $jsonValido = '{"sumario": "Candidato altamente qualificado.", "nota": 95}';

        $this->responseMock->method('text')->willReturn($jsonValido);

        $resultado = $this->service->analisar("Texto do currículo...", $this->dadosVagaFake);

        $this->assertTrue($resultado['sucesso']);
        $this->assertArrayHasKey('resultado', $resultado);
        $this->assertEquals(95, $resultado['resultado']['nota']);
        $this->assertEquals("Candidato altamente qualificado.", $resultado['resultado']['sumario']);
    }

    public function testAnalisarRetornaSucessoComJSONLimpo()
    {
        $jsonSujo = '```json
        {
            "sumario": "Candidato bom.",
            "nota": 80
        }
        ```';

        $this->responseMock->method('text')->willReturn($jsonSujo);

        $resultado = $this->service->analisar("Texto do currículo...", $this->dadosVagaFake);

        $this->assertTrue($resultado['sucesso']);
        $this->assertEquals(80, $resultado['resultado']['nota']);
        $this->assertEquals("Candidato bom.", $resultado['resultado']['sumario']);
    }

    public function testAnalisarRetornaFalhaComJSONInvalido()
    {
        $jsonInvalido = '{"sumario": "Candidato...", "nota": 95'; // JSON quebrado

        $this->responseMock->method('text')->willReturn($jsonInvalido);

        $resultado = $this->service->analisar("Texto do currículo...", $this->dadosVagaFake);

        $this->assertFalse($resultado['sucesso']);
        $this->assertStringContainsString("A API retornou um formato JSON inválido:", $resultado['erro']);
    }

    public function testAnalisarRetornaFalhaComChavesFaltando()
    {
        $jsonIncompleto = '{"sumario_errado": "Candidato...", "nota": 95}';

        $this->responseMock->method('text')->willReturn($jsonIncompleto);

        $resultado = $this->service->analisar("Texto do currículo...", $this->dadosVagaFake);

        $this->assertFalse($resultado['sucesso']);
        $this->assertStringContainsString("A API retornou um formato JSON inválido:", $resultado['erro']);
    }

    public function testAnalisarRetornaFalhaQuandoAPIExcecao()
    {
        $this->generativeModelMock->method('generateContent')
            ->will($this->throwException(new Exception("Erro de rede da API")));

        $resultado = $this->service->analisar("Texto do currículo...", $this->dadosVagaFake);

        $this->assertFalse($resultado['sucesso']);
        $this->assertStringContainsString("Erro de API do Gemini: Erro de rede da API", $resultado['erro']);
    }
}