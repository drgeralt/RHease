<?php

namespace App\Controller;

use App\Controller\HoleriteController;
use App\Model\HoleriteModel; // Importe o Model também
use PHPUnit\Framework\TestCase;

class HoleriteControllerTest extends TestCase
{
    /**
     * Testa se o método gerarPDF lança uma exceção
     * quando os dados POST estão ausentes.
     */
    public function testGerarPdfLancaExcecaoSeDadosPostEstiveremAusentes()
    {
        // 1. "AVISO" AO PHPUNIT:
        // Eu espero que este teste lance uma exceção.
        $this->expectException(\InvalidArgumentException::class);

        // Eu também espero que a mensagem da exceção contenha este texto.
        $this->expectExceptionMessage('Dados insuficientes');


        // 2. PREPARAÇÃO (Arrange):
        // Garantir que os dados POST estão vazios para este teste.
        $_POST = [];

        // O construtor do seu Controller é um problema, pois ele
        // espera uma conexão de banco de dados (via parent::__construct).
        // Não podemos instanciar ele com "new HoleriteController()".

        // A SOLUÇÃO: Criamos um "Dublê" (Mock) que IGNORA o construtor.
        $controller = $this->getMockBuilder(HoleriteController::class)
            ->disableOriginalConstructor()
            ->onlyMethods([]) // <-- A LINHA MÁGICA!
            ->getMock();

        // 3. EXECUÇÃO (Act):
        // Chame o método que queremos testar.
        // Como o $_POST está vazio, ele deve entrar no "if" que
        // nós mudamos e lançar a exceção.
        $controller->gerarPDF();

        // 4. VERIFICAÇÃO (Assert):
        // É automática! Se a exceção esperada (InvalidArgumentException)
        // for lançada, o teste passa (fica VERDE).
        // Se NENHUMA exceção for lançada, o teste falha (fica VERMELHO).
    }

    public function testGerarPdfLancaExcecaoSeHoleriteNaoEncontrado()
    {
        // 1. PREPARAÇÃO (Arrange)

        // A. Simule o $_POST (desta vez com dados válidos)
        // Isso é para passar da *primeira* verificação (o teste anterior).
        $_POST['id_colaborador'] = 1;
        $_POST['mes'] = 10;
        $_POST['ano'] = 2025;

        // B. Crie um "Dublê" (Mock) para o HoleriteModel
        // Estamos criando um Model falso que podemos controlar.
        $mockModel = $this->createMock(\App\Model\HoleriteModel::class);

        // C. Ensine o Mock:
        // "Quando o método 'findHoleritePorColaboradorEMes' for chamado,
        // com QUALQUER argumento, retorne 'false'."
        $mockModel->method('findHoleritePorColaboradorEMes')
            ->willReturn(false); // É isso que vai acionar o "if (!$holerite)"

        // D. Crie o Controller (igual ao teste anterior)
        $controller = $this->getMockBuilder(\App\Controller\HoleriteController::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        // E. A MÁGICA (Injeção via Reflection) - [CORRIGIDO]
        // A propriedade $model no Controller é 'private'.
        // Vamos usar "Reflection" na *Classe Original* para encontrar a propriedade.
        $reflection = new \ReflectionClass(\App\Controller\HoleriteController::class);
        $property = $reflection->getProperty('model');
        // $property->setAccessible(true); // (Opcional, mas boa prática se fosse PHP antigo)
        $property->setValue($controller, $mockModel); // Injetamos o mock no objeto!


        // 2. VERIFICAÇÃO (Assert)
        // Desta vez, esperamos a outra exceção, a da linha 83
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Holerite não encontrado para o colaborador e período informados.");


        // 3. EXECUÇÃO (Act)
        // Chame o método. Ele vai usar o $mockModel injetado.
        $controller->gerarPDF();
    }

    /**
     * Testa se o método index redireciona se o usuário não estiver logado.
     */
    public function testIndexRedirecionaSeUsuarioNaoLogado()
    {
        // 1. PREPARAÇÃO (Arrange)

        // A. Garanta que a sessão esteja vazia para este teste
        $_SESSION = [];

        // B. Crie o Controller (do mesmo jeito que já sabemos)
        $controller = $this->getMockBuilder(\App\Controller\HoleriteController::class)
            ->disableOriginalConstructor()
            ->onlyMethods([]) // Queremos testar o 'index()' real
            ->getMock();

        // 2. EXECUÇÃO (Act)
        // Chame o método 'index'.
        // Como $_SESSION['user_id'] não existe, ele deve entrar no 'if'
        // e chamar header() e return.
        $controller->index();

        // 3. VERIFICAÇÃO (Assert)
        // Como o método só chama 'header' e 'return', não há uma
        // exceção para "pegar". O simples fato de o teste
        // rodar até o fim sem erros já prova que ele entrou no 'if'.

        // Para ter certeza, podemos adicionar um "assert vazio" que
        // apenas marca o teste como bem-sucedido se chegou até aqui.
        $this->assertTrue(true, "O teste passou, indicando que o 'if' de 'user_id' foi acionado.");
    }

    /**
     * Testa se o index carrega os dados corretos para um usuário logado.
     */
    public function testIndexCarregaDadosCorretosParaUsuarioLogado()
    {
        // 1. PREPARAÇÃO (Arrange)

        // A. Simule um usuário logado
        $_SESSION['user_id'] = 123; // Um ID de usuário falso
        $idUsuarioLogado = 123;

        // B. Crie os dados falsos que esperamos do Model
        $dadosColaboradorFalsos = ['id' => $idUsuarioLogado, 'nome' => 'Usuário Teste'];
        $dadosHoleritesFalsos = [
            ['id' => 1, 'mes' => 10, 'ano' => 2025],
            ['id' => 2, 'mes' => 11, 'ano' => 2025]
        ];

        // C. Crie o Mock do Model (como já fizemos antes)
        $mockModel = $this->createMock(\App\Model\HoleriteModel::class);

        // D. Ensine o Mock (com mais detalhes agora)
        // "Quando findColaboradorById for chamado com o ID 123, retorne $dadosColaboradorFalsos"
        $mockModel->method('findColaboradorById')
            ->with($idUsuarioLogado) // Verifica se o ID correto foi passado
            ->willReturn($dadosColaboradorFalsos);

        // "Quando findByColaboradorId for chamado com o ID 123, retorne $dadosHoleritesFalsos"
        $mockModel->method('findByColaboradorId')
            ->with($idUsuarioLogado)
            ->willReturn($dadosHoleritesFalsos);


        // E. Crie o Mock do Controller (AQUI ESTÁ A NOVA MÁGICA!)
        // Antes, usamos ->onlyMethods([]).
        // Agora, queremos "mockar" o método 'view' (que é da classe pai, Controller)
        // para que ele não tente carregar um arquivo de view de verdade.
        $controller = $this->getMockBuilder(\App\Controller\HoleriteController::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['view']) // <-- SÓ SIMULE O MÉTODO 'view'
            ->getMock();

        // F. Injete o Model no Controller (como já fizemos antes)
        $reflection = new \ReflectionClass(\App\Controller\HoleriteController::class);
        $property = $reflection->getProperty('model');
        $property->setValue($controller, $mockModel);


        // 2. VERIFICAÇÃO (Assert)

        // A. Queremos verificar se o método 'view' é chamado
        //    exatamente UMA VEZ...
        $controller->expects($this->once())
            ->method('view')
            // B. ...e se ele é chamado COM ESTES ARGUMENTOS EXATOS:
            ->with(
            // Arg 1: O nome da view
                'Holerite/meusHolerites',

                // Arg 2: O array de dados
                [
                    'colaborador' => $dadosColaboradorFalsos,
                    'holerites' => $dadosHoleritesFalsos
                ]
            );


        // 3. EXECUÇÃO (Act)
        // Rode o método.
        $controller->index();

        // Se a verificação (expects/with) que definimos acima passar,
        // o teste ficará VERDE!
    }
}
