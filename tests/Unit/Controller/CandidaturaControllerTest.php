<?php
declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Controller\CandidaturaController;
use App\Model\CandidaturaModel;
use App\Model\GestaoVagasModel;
use App\Model\CandidatoModel;
use App\Service\Implementations\AnalisadorCurriculoService;
use Smalot\PdfParser\Parser;
use Smalot\PdfParser\Pdf;

/**
 * Classe 'TestableCandidaturaController'
 *
 * Esta classe "dublê" estende o Controller real, mas sobrescreve
 * os métodos que causam problemas em um ambiente de teste (como
 * carregar views ou conectar-se ao banco no construtor).
 */
class TestableCandidaturaController extends CandidaturaController
{
    // Propriedades públicas para armazenar o que o controller tentar fazer
    public $viewChamada;
    public $viewData;

    /**
     * Sobrescreve o construtor original.
     * Nós removemos a chamada 'parent::__construct()' para
     * impedir que o teste tente se conectar ao banco de dados real.
     */
    public function __construct(
        CandidaturaModel $candidaturaModel,
        GestaoVagasModel $gestaoVagasModel,
        CandidatoModel $candidatoModel,
        AnalisadorCurriculoService $analisadorService,
        Parser $pdfParser
    ) {
        // NÃO chamamos parent::__construct()

        // Apenas armazenamos os Mocks (objetos falsos)
        $this->candidaturaModel = $candidaturaModel;
        $this->gestaoVagasModel = $gestaoVagasModel;
        $this->candidatoModel = $candidatoModel;
        $this->analisadorService = $analisadorService;
        $this->pdfParser = $pdfParser;
    }

    /**
     * Sobrescreve o método 'view' da classe Controller base.
     * Em vez de carregar um arquivo PHP, ele apenas armazena
     * o nome da view e os dados que seriam enviados para ela.
     */
    public function view($view, $data = []): void
    {
        $this->viewChamada = $view;
        $this->viewData = $data;
    }
}


/**
 * Classe de Teste Unitário para o CandidaturaController.
 */
class CandidaturaControllerTest extends TestCase
{
    // Propriedades para armazenar nossos objetos falsos (Mocks)
    private $candidaturaModelMock;
    private $gestaoVagasModelMock;
    private $candidatoModelMock;
    private $analisadorServiceMock;
    private $pdfParserMock;
    private $pdfMock; // Mock para o objeto PDF retornado pelo parser

    /**
     * Configura os Mocks antes de cada teste ser executado.
     */
    protected function setUp(): void
    {
        // Cria os Mocks para todas as dependências
        $this->candidaturaModelMock = $this->createMock(CandidaturaModel::class);
        $this->gestaoVagasModelMock = $this->createMock(GestaoVagasModel::class);
        $this->candidatoModelMock = $this->createMock(CandidatoModel::class);
        $this->analisadorServiceMock = $this->createMock(AnalisadorCurriculoService::class);
        $this->pdfParserMock = $this->createMock(Parser::class);
        $this->pdfMock = $this->createMock(Pdf::class); // Mock do resultado do parse
    }

    /**
     * Testa o método 'listar'
     */
    public function testListarCarregaViewCorretaComVagas()
    {
        // 1. DADOS FALSOS
        $dadosVagasFalsas = [
            ['id_vaga' => 1, 'titulo_vaga' => 'Vaga Teste 1'],
            ['id_vaga' => 2, 'titulo_vaga' => 'Vaga Teste 2']
        ];

        // 2. CONFIGURAR O MOCK (GestaoVagasModel)
        // Diz ao Model falso que esperamos que ele chame 'listarVagasAbertas' uma vez
        // e que, quando isso acontecer, ele deve retornar nossos dados falsos.
        $this->gestaoVagasModelMock
            ->expects($this->once())
            ->method('listarVagasAbertas')
            ->willReturn($dadosVagasFalsas);

        // 3. EXECUTAR O TESTE
        // Instancia o nosso Controller de Teste, injetando os Mocks
        $controller = new TestableCandidaturaController(
            $this->candidaturaModelMock,
            $this->gestaoVagasModelMock,
            $this->candidatoModelMock,
            $this->analisadorServiceMock,
            $this->pdfParserMock
        );

        // Chama o método que queremos testar
        $controller->listar();

        // 4. VERIFICAR OS RESULTADOS (ASSERT)
        // Verificamos se o controller tentou carregar a view correta
        $this->assertEquals('Candidatura/lista_vagas', $controller->viewChamada);

        // Verificamos se os dados enviados para a view contêm a chave 'vagas'
        $this->assertArrayHasKey('vagas', $controller->viewData);

        // Verificamos se os dados das vagas são exatamente os dados falsos que criamos
        $this->assertEquals($dadosVagasFalsas, $controller->viewData['vagas']);
    }

    /**
     * Testa o método 'exibirFormulario' quando o ID da vaga é válido.
     */
    public function testExibirFormularioCarregaViewComVaga()
    {
        // 1. DADOS FALSOS
        $idVaga = 5;
        $dadosVagaFalsa = ['id_vaga' => $idVaga, 'titulo_vaga' => 'Vaga de Teste'];

        // Simula os dados do formulário
        $_POST['id'] = $idVaga;

        // 2. CONFIGURAR O MOCK
        $this->gestaoVagasModelMock
            ->expects($this->once())
            ->method('buscarPorId')
            ->with($idVaga) // Verifica se o método foi chamado com o ID correto
            ->willReturn($dadosVagaFalsa);

        // 3. EXECUTAR O TESTE
        $controller = new TestableCandidaturaController(
            $this->candidaturaModelMock,
            $this->gestaoVagasModelMock,
            $this->candidatoModelMock,
            $this->analisadorServiceMock,
            $this->pdfParserMock
        );
        $controller->exibirFormulario();

        // 4. VERIFICAR
        $this->assertEquals('Candidatura/formulario_candidatura', $controller->viewChamada);
        $this->assertEquals($dadosVagaFalsa, $controller->viewData['vaga']);
    }
}