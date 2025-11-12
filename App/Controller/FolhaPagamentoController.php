<?php
// App/Controller/FolhaPagamentoController.php

namespace App\Controller;

use App\Core\Controller;
use App\Service\Implementations\FolhaPagamentoService;
use App\Model\FolhaPagamentoModel;
use App\Model\ColaboradorModel;
use App\Model\ParametrosFolhaModel;
use App\Service\Implementations\PontoService;
use PDO;

class FolhaPagamentoController extends Controller
{
    protected FolhaPagamentoService $folhaPagamentoService;
    public function __construct(FolhaPagamentoService $folhaPagamentoService, PDO $pdo)
    {
        parent::__construct($pdo);
        $this->folhaPagamentoService = $folhaPagamentoService;
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
