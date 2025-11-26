<?php
namespace App\Controller;

use App\Core\Controller;
use App\Service\Implementations\FolhaPagamentoService;
use PDO;

class FolhaPagamentoController extends Controller
{
    protected FolhaPagamentoService $folhaPagamentoService;

    public function __construct(FolhaPagamentoService $folhaPagamentoService, PDO $pdo)
    {
        parent::__construct($pdo);
        $this->folhaPagamentoService = $folhaPagamentoService;
    }

    public function index()
    {
        $this->exigirPermissaoGestor();
        $this->view('FolhaPagamento/processarFolha', ['data' => []]);
    }

    public function processar()
    {
        $this->exigirPermissaoGestor();
        $ano = (int)($_POST['ano'] ?? 0);
        $mes = (int)($_POST['mes'] ?? 0);

        if (!$ano || !$mes || $mes < 1 || $mes > 12) {
            $this->view('FolhaPagamento/processarFolha', [
                'erro' => 'Por favor, insira um ano e mês válidos.'
            ]);
            return;
        }

        try {
            // Processa a folha
            $resultados = $this->folhaPagamentoService->processarFolha($ano, $mes);

            // Mensagem de resumo
            $qtdSucesso = count($resultados['sucesso']);
            $qtdFalha = count($resultados['falha']);

            $mensagem = "Processamento concluído!<br>";
            $mensagem .= "<strong>{$qtdSucesso}</strong> holerites gerados com sucesso.<br>";

            if ($qtdFalha > 0) {
                $mensagem .= "<strong>{$qtdFalha}</strong> erros encontrados.";
            }

            // ENVIA TUDO PARA A VIEW
            $this->view('FolhaPagamento/processarFolha', [
                'sucesso' => $mensagem,
                'resultados' => $resultados, // A lista detalhada (IDs, Nomes)
                'mes_ref' => $mes,
                'ano_ref' => $ano
            ]);

        } catch (\Exception $e) {
            $this->view('FolhaPagamento/processarFolha', [
                'erro' => 'Ocorreu um erro crítico: ' . $e->getMessage()
            ]);
        }
    }
}