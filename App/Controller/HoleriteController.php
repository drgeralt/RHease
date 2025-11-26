<?php

namespace App\Controller;

use App\Core\Controller;
use App\Model\HoleriteModel;
use App\Model\EmpresaModel; // <--- Importante
use PDO;

class HoleriteController extends Controller
{
    protected HoleriteModel $model;
    protected EmpresaModel $empresaModel; // <--- Nova propriedade

    // Construtor atualizado para receber o EmpresaModel
    public function __construct(HoleriteModel $holeriteModel, EmpresaModel $empresaModel, PDO $pdo)
    {
        parent::__construct($pdo);
        $this->model = $holeriteModel;
        $this->empresaModel = $empresaModel;
    }

    public function index()
    {
        // Verifica sessão
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            return;
        }

        $idColaborador = $_SESSION['user_id'];

        // Busca dados para a tela
        $colaborador = $this->model->findColaboradorById($idColaborador);
        $holerites = $this->model->findByColaboradorId($idColaborador);

        $this->view('Holerite/meusHolerites', [
            'colaborador' => $colaborador,
            'holerites' => $holerites
        ]);
    }

    public function gerarPDF()
    {
        // Desativa warnings que podem corromper o PDF
        error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);

        // 1. Recebe dados do POST
        $idColaborador = (int)($_POST['id_colaborador'] ?? 0);
        $mes = (int)($_POST['mes'] ?? 0);
        $ano = (int)($_POST['ano'] ?? 0);

        if (!$idColaborador || !$mes || !$ano) {
            die("Dados insuficientes para gerar o holerite.");
        }

        // 2. Busca dados do Holerite
        $holerite = $this->model->findHoleritePorColaboradorEMes($idColaborador, $mes, $ano);

        if (!$holerite) {
            die("Holerite não encontrado para o período solicitado.");
        }

        // Busca itens (proventos/descontos)
        $itens = $this->model->findItensByHoleriteId($holerite->id_holerite);

        // 3. BUSCA DADOS DA EMPRESA ATIVA (Multi-tenancy)
        $empresa = $this->empresaModel->getEmpresaAtiva();

        // 4. Inicia geração do PDF
        $pdf = new \FPDF('P', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 12);

        // --- Helper para Acentos (FPDF não suporta UTF-8 nativo) ---
        $txt = function($str) {
            return utf8_decode($str ?? '');
        };

        // --- CABEÇALHO ---
        $pdf->Cell(190, 10, $txt('DEMONSTRATIVO DE PAGAMENTO'), 1, 1, 'C');

        $pdf->SetFont('Arial', '', 9);
        // Usa dados dinâmicos da empresa
        $pdf->Cell(95, 7, $txt('Empresa: ' . $empresa['razao_social']), 'L', 0);
        $pdf->Cell(95, 7, $txt('Referência: ' . str_pad((string)$mes, 2, '0', STR_PAD_LEFT) . '/' . $ano), 'R', 1, 'R');

        $pdf->Cell(95, 7, $txt('CNPJ: ' . $empresa['cnpj']), 'L', 0);
        $pdf->Cell(95, 7, $txt('Processamento: ' . date('d/m/Y', strtotime($holerite->data_processamento))), 'R', 1, 'R');

        // Se tiver endereço, mostra
        if (!empty($empresa['endereco'])) {
            $pdf->Cell(190, 7, $txt($empresa['endereco'] . ' - ' . $empresa['cidade_uf']), 'L', 1);
        } else {
            $pdf->Ln(7); // Pula linha se não tiver endereço para manter layout
        }

        $pdf->Cell(190, 0, '', 'T', 1); // Linha separadora

        // --- DADOS DO COLABORADOR ---
        $pdf->Ln(2);
        $pdf->Cell(20, 7, $txt('Matrícula:'), 'L');
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(75, 7, $txt($holerite->matricula));

        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(15, 7, $txt('Nome:'));
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(80, 7, $txt($holerite->nome_completo), 'R', 1);

        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(20, 7, $txt('CPF:'), 'L');
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(75, 7, $holerite->CPF);

        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(15, 7, $txt('Cargo:'));
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(80, 7, $txt($holerite->nome_cargo), 'R', 1);

        $pdf->Ln(2);
        $pdf->Cell(190, 0, '', 'T', 1); // Linha separadora

        // --- CORPO (ITENS) ---
        $pdf->Ln(2);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(20, 7, $txt('Código'), 1, 0, 'C');
        $pdf->Cell(105, 7, $txt('Descrição'), 1, 0, 'L');
        $pdf->Cell(32.5, 7, $txt('Proventos'), 1, 0, 'C');
        $pdf->Cell(32.5, 7, $txt('Descontos'), 1, 1, 'C');

        $pdf->SetFont('Arial', '', 9);

        foreach ($itens as $item) {
            $pdf->Cell(20, 6, $item->codigo_evento, 'LR', 0, 'C');
            $pdf->Cell(105, 6, $txt($item->descricao), 'R', 0, 'L');

            if ($item->tipo == 'provento') {
                $pdf->Cell(32.5, 6, number_format($item->valor, 2, ',', '.'), 'R', 0, 'R');
                $pdf->Cell(32.5, 6, '', 'R', 1, 'R');
            } else {
                $pdf->Cell(32.5, 6, '', 'R', 0, 'R');
                $pdf->Cell(32.5, 6, number_format($item->valor, 2, ',', '.'), 'R', 1, 'R');
            }
        }

        // Fecha a tabela visualmente
        $pdf->Cell(190, 0, '', 'T', 1);

        // --- TOTAIS ---
        $pdf->Ln(2);
        $pdf->SetFont('Arial', '', 10);

        // Proventos
        $pdf->Cell(125, 7, $txt('Total de Proventos'), 'LRT', 0);
        $pdf->Cell(65, 7, number_format($holerite->total_proventos, 2, ',', '.'), 'LRT', 1, 'R');

        // Descontos
        $pdf->Cell(125, 7, $txt('Total de Descontos'), 'LR', 0);
        $pdf->Cell(65, 7, number_format($holerite->total_descontos, 2, ',', '.'), 'LR', 1, 'R');

        // Líquido
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(125, 8, $txt('Salário Líquido a Receber'), 'LRB', 0);
        $pdf->Cell(65, 8, 'R$ ' . number_format($holerite->salario_liquido, 2, ',', '.'), 'LRB', 1, 'R');
        $pdf->Ln(5);

        // --- BASES DE CÁLCULO (Rodapé Técnico) ---
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(47.5, 6, $txt('Base Calc. INSS'), 1, 0, 'C');
        $pdf->Cell(47.5, 6, $txt('Base Calc. FGTS'), 1, 0, 'C');
        $pdf->Cell(47.5, 6, $txt('FGTS do Mês'), 1, 0, 'C');
        $pdf->Cell(47.5, 6, $txt('Base Calc. IRRF'), 1, 1, 'C');

        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(47.5, 6, number_format($holerite->base_calculo_inss, 2, ',', '.'), 1, 0, 'C');
        $pdf->Cell(47.5, 6, number_format($holerite->base_calculo_fgts, 2, ',', '.'), 1, 0, 'C');
        $pdf->Cell(47.5, 6, number_format($holerite->valor_fgts, 2, ',', '.'), 1, 0, 'C');
        $pdf->Cell(47.5, 6, number_format($holerite->base_calculo_irrf, 2, ',', '.'), 1, 1, 'C');
        $pdf->Ln(15);

        // --- ASSINATURA ---
        $pdf->Cell(190, 5, '________________________________________', 0, 1, 'C');
        $pdf->Cell(190, 5, $txt('Assinatura do Colaborador'), 0, 1, 'C');

        // --- LIMPEZA FINAL (Evita erro "Some data has already been output") ---
        if (ob_get_length()) {
            ob_clean();
        }

        // 5. Envia PDF
        $pdf->Output('I', 'holerite_' . $mes . '_' . $ano . '.pdf');
        exit;
    }
}