<?php
// App/Controller/HoleriteController.php

namespace App\Controller;

use App\Core\Controller;
use App\Model\HoleriteModel;
require_once(BASE_PATH . '/vendor/setasign/fpdf/fpdf.php');
class HoleriteController extends Controller
{
    private HoleriteModel $model;

    public function __construct()
    {
        parent::__construct(); // Chama o construtor do Controller pai
        $this->model = new HoleriteModel($this->db_connection); // Passa a conexão!
    }

    public function index()
    {
        // 1. Verifica se o usuário está logado. Se não, redireciona para o login.
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        // 2. Pega o ID do colaborador logado a partir da sessão.
        $idColaborador = $_SESSION['user_id'];

        // 3. Busca os dados do colaborador e seus holerites usando o ID da sessão.
        $colaborador = $this->model->findColaboradorById($idColaborador);
        $holerites = $this->model->findByColaboradorId($idColaborador);

        // 4. Envia os dados corretos para a view.
        $this->view('Holerite/meusHolerites', [
            'colaborador' => $colaborador,
            'holerites' => $holerites
        ]);
    }

    public function gerarPDF()
    {
        /*
        |--------------------------------------------------------------------------
        | VERIFICAÇÃO DE LOGIN (TEMPORARIAMENTE DESATIVADA PARA TESTES)
        |--------------------------------------------------------------------------
        | O código original que verifica a sessão foi comentado para permitir
        | a geração do PDF sem a necessidade de login durante o desenvolvimento.
        | Lembre-se de reativá-lo antes de colocar o sistema em produção.
        |
        */
        // if (session_status() == PHP_SESSION_NONE) {
        //     session_start();
        // }
        // $idColaborador = $_SESSION['id_colaborador'] ?? null;
        // if (!$idColaborador) {
        //     header('Location: ' . BASE_URL . '/login');
        //     exit();
        // }

        // ✅ ADIÇÃO PARA TESTE: Simulamos um colaborador logado com ID = 1.
        // Você pode alterar este número para testar outros colaboradores.
        $idColaborador = filter_input(INPUT_POST, 'id_colaborador', FILTER_VALIDATE_INT);
        $mes = filter_input(INPUT_POST, 'mes', FILTER_VALIDATE_INT);
        $ano = filter_input(INPUT_POST, 'ano', FILTER_VALIDATE_INT);
        // As verificações agora são úteis e funcionam como deveriam.
        if (!$idColaborador || !$mes || !$ano) {
            die('Dados insuficientes para gerar o holerite. ID do colaborador, mês e ano são obrigatórios.');
        }

        $holerite = $this->model->findHoleritePorColaboradorEMes($idColaborador, $mes, $ano);

        if (!$holerite) {
            die("Holerite não encontrado para o colaborador e período informados.");
        }

        $itens = $this->model->findItensByHoleriteId($holerite->id_holerite);

        // 4. Inicia a geração do PDF com FPDF
        $pdf = new \FPDF('P', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 12);

        // --- Cabeçalho do Holerite ---
        $pdf->Cell(190, 10, 'DEMONSTRATIVO DE PAGAMENTO', 1, 1, 'C');
        $pdf->Cell(95, 7, 'Empresa: RHease Solutions', 'L', 0);
        $pdf->Cell(95, 7, 'Referencia: ' . str_pad((string)$mes, 2, '0', STR_PAD_LEFT) . '/' . $ano, 'R', 1, 'R');
        $pdf->Cell(95, 7, 'CNPJ: 12.345.678/0001-99', 'L', 0);
        $pdf->Cell(95, 7, 'Processamento: ' . date('d/m/Y', strtotime($holerite->data_processamento)), 'R', 1, 'R');
        $pdf->Cell(190, 7, '', 'LRB', 1);

        // --- Dados do Colaborador ---
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(20, 7, 'Matricula:', 'L');
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(75, 7, $holerite->matricula);
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(15, 7, 'Nome:');
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(80, 7, $holerite->nome_completo, 'R', 1);

        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(20, 7, 'CPF:', 'L');
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(75, 7, $holerite->CPF);
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(15, 7, 'Cargo:');
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(80, 7, $holerite->nome_cargo, 'R', 1);
        $pdf->Cell(190, 7, '', 'LRB', 1);


        // --- Corpo do Holerite (Itens) ---
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(20, 7, 'Codigo', '1', 0, 'C');
        $pdf->Cell(105, 7, 'Descricao', '1', 0, 'L');
        $pdf->Cell(32.5, 7, 'Proventos', '1', 0, 'C');
        $pdf->Cell(32.5, 7, 'Descontos', '1', 1, 'C');

        $pdf->SetFont('Arial', '', 9);
        foreach ($itens as $item) {
            $pdf->Cell(20, 6, $item->codigo_evento, 'LR', 0, 'C');
            $pdf->Cell(105, 6, $item->descricao, 'R', 0, 'L');
            if ($item->tipo == 'provento') {
                $pdf->Cell(32.5, 6, number_format($item->valor, 2, ',', '.'), 'R', 0, 'R');
                $pdf->Cell(32.5, 6, '', 'R', 1, 'R');
            } else {
                $pdf->Cell(32.5, 6, '', 'R', 0, 'R');
                $pdf->Cell(32.5, 6, number_format($item->valor, 2, ',', '.'), 'R', 1, 'R');
            }
        }
        $pdf->Cell(190, 0, '', 'T', 1);

        // --- Totais ---
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(125, 7, 'Total de Proventos', 'LR', 0);
        $pdf->Cell(65, 7, number_format($holerite->total_proventos, 2, ',', '.'), 'LR', 1, 'R');
        $pdf->Cell(125, 7, 'Total de Descontos', 'LR', 0);
        $pdf->Cell(65, 7, number_format($holerite->total_descontos, 2, ',', '.'), 'LR', 1, 'R');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(125, 8, 'Salario Liquido a Receber', 'LBR', 0);
        $pdf->Cell(65, 8, 'R$ ' . number_format($holerite->salario_liquido, 2, ',', '.'), 'LBR', 1, 'R');
        $pdf->Ln(5);

        // --- Bases de Cálculo ---
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(47.5, 6, 'Base Calc. INSS', '1', 0, 'C');
        $pdf->Cell(47.5, 6, 'Base Calc. FGTS', '1', 0, 'C');
        $pdf->Cell(47.5, 6, 'FGTS do Mes', '1', 0, 'C');
        $pdf->Cell(47.5, 6, 'Base Calc. IRRF', '1', 1, 'C');
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(47.5, 6, number_format($holerite->base_calculo_inss, 2, ',', '.'), '1', 0, 'C');
        $pdf->Cell(47.5, 6, number_format($holerite->base_calculo_fgts, 2, ',', '.'), '1', 0, 'C');
        $pdf->Cell(47.5, 6, number_format($holerite->valor_fgts, 2, ',', '.'), '1', 0, 'C');
        $pdf->Cell(47.5, 6, number_format($holerite->base_calculo_irrf, 2, ',', '.'), '1', 1, 'C');
        $pdf->Ln(10);

        // --- Assinatura ---
        $pdf->Cell(190, 7, '________________________________________', 0, 1, 'C');
        $pdf->Cell(190, 7, 'Assinatura do Colaborador', 0, 1, 'C');

        // 5. Envia o PDF para o navegador
        $pdf->Output('I', 'holerite_' . $mes . '_' . $ano . '.pdf');
        exit;
    }
}