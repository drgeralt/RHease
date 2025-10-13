<?php
// App/Controller/HoleriteController.php

namespace App\Controller;

use App\Core\Controller;
use App\Model\HoleriteModel;
require_once(BASE_PATH . '/vendor/setasign/fpdf/fpdf.php');
class HoleriteController extends Controller
{
    private $model;

    /**
     * O construtor cria a instância do nosso Model.
     */
    public function __construct()
    {
        $this->model = new HoleriteModel();
    }

    /**
     * Exibe a página "Meus Holerites".
     */
    public function index()
    {
        $colaboradorId = 1; // ID fixo para teste

        $holeritesDoColaborador = $this->model->findByColaboradorId($colaboradorId);

        $this->view('Holerite/meusHolerites', [
            'holerites' => $holeritesDoColaborador
        ]);
    }

    public function gerarPDF($id)
    {
        $holerite = $this->model->findHoleriteCompletoById($id);
        $itens = $this->model->findItensByHoleriteId($id);

        if (!$holerite) {
            echo "Holerite não encontrado.";
            return;
        }

        $proventos = array_filter($itens, function($item) { return $item->tipo == 'PROVENTO'; });
        $descontos = array_filter($itens, function($item) { return $item->tipo == 'DESCONTO'; });

        $pdf = new \FPDF('P', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 12);



        // --- Cabeçalho ---
        $pdf->Cell(40, 10, 'Nome da Empresa LTDA', 0, 0, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(150, 10, 'Recibo de Pagamento de Salario', 0, 1, 'R');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(40, 5, 'CNPJ: 00.000.000/0001-00', 0, 0, 'L');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(150, 5, 'Referencia: ' . str_pad($holerite->mes_referencia, 2, '0', STR_PAD_LEFT) . '/' . $holerite->ano_referencia, 0, 1, 'R');
        $pdf->Cell(40, 5, 'Endereco da Empresa, 123 - Centro', 0, 1, 'L');
        $pdf->Ln(5);

        // --- Dados do Colaborador ---
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(25, 7, 'Matricula', 'LTR', 0, 'C');
        $pdf->Cell(105, 7, 'Nome do Funcionario', 'LTR', 0, 'C');
        $pdf->Cell(60, 7, 'Cargo', 'LTR', 1, 'C');
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(25, 7, $holerite->matricula, 'LBR', 0, 'C');
        $pdf->Cell(105, 7, $holerite->nome_completo, 'LBR', 0, 'C');
        $pdf->Cell(60, 7, $holerite->nome_cargo, 'LBR', 1, 'C');
        $pdf->Ln(5);

        // --- Tabela de Itens ---
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(15, 6, 'Cod.', '1', 0, 'C');
        $pdf->Cell(80, 6, 'Descricao', '1', 0, 'C');
        $pdf->Cell(30, 6, 'Proventos (R$)', '1', 0, 'C');
        $pdf->Cell(65, 6, 'Descontos (R$)', '1', 1, 'C');

        $pdf->SetFont('Arial', '', 8);
        $maxRows = max(count($proventos), count($descontos));
        $proventos = array_values($proventos);
        $descontos = array_values($descontos);

        for ($i = 0; $i < $maxRows; $i++) {
            $pdf->Cell(15, 5, $proventos[$i]->codigo_evento ?? '', 'LRB', 0);
            $pdf->Cell(80, 5, $proventos[$i]->descricao ?? '', 'LRB', 0);
            $pdf->Cell(30, 5, isset($proventos[$i]) ? number_format($proventos[$i]->valor, 2, ',', '.') : '', 'LRB', 0, 'R');
            $pdf->Cell(65, 5, isset($descontos[$i]) ? number_format($descontos[$i]->valor, 2, ',', '.') : '', 'LRB', 1, 'R');
        }

        // --- Totais ---
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(125, 7, 'Total de Proventos', 'LTR', 0);
        $pdf->Cell(65, 7, number_format($holerite->total_proventos, 2, ',', '.'), 'LTR', 1, 'R');
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
        $pdf->Cell(190, 5, '________________________________________', 0, 1, 'C');
        $pdf->Cell(190, 5, $holerite->nome_completo, 0, 1, 'C');

        $pdf->Output('I', 'holerite_' . $holerite->mes_referencia . '_' . $holerite->ano_referencia . '.pdf');
        exit;
    }
}