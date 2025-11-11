<?php
// App/Controller/FolhaPagamentoController.php

namespace App\Controller;

use App\Core\Controller;
use App\Service\Implementations\FolhaPagamentoService;
use App\Model\FolhaPagamentoModel;
use App\Model\ColaboradorModel;
use App\Model\ParametrosFolhaModel;
use App\Service\Implementations\PontoService;
class FolhaPagamentoController extends Controller
{
    private FolhaPagamentoService $folhaPagamentoService;
    public function __construct()
    {
        parent::__construct();

        // 1. Crie todas as dependências (os Models)
        $folhaPagamentoModel = new \App\Model\FolhaPagamentoModel($this->db_connection);
        $colaboradorModel = new \App\Model\ColaboradorModel($this->db_connection);
        $parametrosModel = new \App\Model\ParametrosFolhaModel($this->db_connection);
        $pontoService = new \App\Service\Implementations\PontoService($this->db_connection);

        // 2. Injete-os no Service
        $this->folhaPagamentoService = new FolhaPagamentoService(
            $folhaPagamentoModel,
            $colaboradorModel,
            $parametrosModel,
            $pontoService
        );
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
        $ano = (int)($_POST['ano'] ?? 0);
        $mes = (int)($_POST['mes'] ?? 0);
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

        } catch (\Exception $e) {
            // Captura qualquer erro geral que o serviço possa lançar
            $this->view('FolhaPagamento/processarFolha', [
                'erro' => 'Ocorreu um erro: ' . $e->getMessage()
            ]);
        }
    }
}
