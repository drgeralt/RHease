<?php
// App/Controller/FolhaPagamentoController.php

namespace App\Controller;

use App\Core\Controller;
use App\Services\Implementations\FolhaPagamentoService;

class FolhaPagamentoController extends Controller
{
    /**
     * Exibe a página para o RH processar a folha de pagamento.
     */
    public function index()
    {
        // Apenas carrega a view com o formulário.
        // O array vazio 'data' é para evitar erros caso a view espere a variável.
        $this->view('FolhaPagamento/processarFolha', ['data' => []]);
    }

    /**
     * Recebe os dados do formulário e dispara o processamento da folha.
     */
    public function processar()
    {
        // 1. Pega o ano e o mês enviados pelo formulário
        $ano = filter_input(INPUT_POST, 'ano', FILTER_VALIDATE_INT);
        $mes = filter_input(INPUT_POST, 'mes', FILTER_VALIDATE_INT);

        // Validação simples
        if (!$ano || !$mes || $mes < 1 || $mes > 12) {
            // Se os dados forem inválidos, recarrega a página com uma mensagem de erro
            $this->view('FolhaPagamento/processarFolha', [
                'erro' => 'Por favor, insira um ano e mês válidos.'
            ]);
            return;
        }

        // 2. Cria uma instância do nosso serviço de cálculo
        $folhaService = new FolhaPagamentoService();

        // 3. Chama o método principal para processar a folha
        $resultados = $folhaService->processarFolha($ano, $mes);

        // 4. Prepara uma mensagem de sucesso para exibir na tela
        $mensagem = "Folha de pagamento para " . str_pad((string)$mes, 2, '0', STR_PAD_LEFT) . "/$ano processada!<br>";
        $mensagem .= "Colaboradores processados com sucesso: " . count($resultados['sucesso']) . "<br>";
        if (!empty($resultados['falha'])) {
            $mensagem .= "Falhas: " . count($resultados['falha']);
        }

        // 5. Recarrega a view, passando a mensagem de sucesso
        $this->view('FolhaPagamento/processarFolha', [
            'sucesso' => $mensagem
        ]);
    }
}
