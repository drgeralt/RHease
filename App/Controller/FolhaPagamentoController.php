<?php
// App/Controller/FolhaPagamentoController.php

namespace App\Controller;

use App\Core\Controller;
use App\Service\Implementations\FolhaPagamentoService;

class FolhaPagamentoController extends Controller
{
    private FolhaPagamentoService $folhaPagamentoService;
    public function __construct()
    {
        parent::__construct();
        // A conexão ($this->db_connection) é passada para o serviço.
        $this->folhaPagamentoService = new FolhaPagamentoService($this->db_connection);
    }
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
        $ano = filter_input(INPUT_POST, 'ano', FILTER_VALIDATE_INT);
        $mes = filter_input(INPUT_POST, 'mes', FILTER_VALIDATE_INT);

        if (!$ano || !$mes || $mes < 1 || $mes > 12) {
            $this->view('FolhaPagamento/processarFolha', [
                'erro' => 'Por favor, insira um ano e mês válidos.'
            ]);
            return;
        }

        try {
            // ✅ CORRIGIDO: Usa a propriedade do serviço instanciada no construtor.
            $resultados = $this->folhaPagamentoService->processarFolha($ano, $mes);

            $mensagem = "Folha de pagamento para " . str_pad((string)$mes, 2, '0', STR_PAD_LEFT) . "/$ano processada!<br>";
            $mensagem .= "Colaboradores processados com sucesso: " . count($resultados['sucesso']) . ".<br>";
            if (!empty($resultados['falha'])) {
                $mensagem .= "Colaboradores com falha: " . count($resultados['falha']) . ".";
            }

            $this->view('FolhaPagamento/processarFolha', [
                'sucesso' => $mensagem
            ]);

        } catch (Exception $e) {
            // Captura qualquer erro geral que o serviço possa lançar
            $this->view('FolhaPagamento/processarFolha', [
                'erro' => 'Ocorreu um erro: ' . $e->getMessage()
            ]);
        }
    }
}
