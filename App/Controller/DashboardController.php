<?php
namespace App\Controller;

use App\Core\Controller;
use App\Model\DashboardModel;
use App\Model\ColaboradorModel;
use App\Core\Database;

class DashboardController extends Controller {
    private $dashboardModel;
    private $colaboradorModel;

    public function __construct() {
        $pdo = Database::getInstance();
        $this->dashboardModel = new DashboardModel();
        $this->colaboradorModel = new ColaboradorModel($pdo);
    }

    public function index() {
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $userRole = $this->dashboardModel->getUserRole($userId);

        if ($userRole === 'gestor_rh') {
            $dashboard_data = $this->dashboardModel->getGestorDashboardData();
            return $this->view('Dashboard/gestor', ['dashboard_data' => $dashboard_data]);
        } else {
            // --- Dados Gerais ---
            $dados_colaborador = $this->colaboradorModel->getDadosColaborador($userId);
            // Se não veio salário base do model, busca direto da tabela colaborador
            if (empty($dados_colaborador['salario_base'])) {
                $pdo = Database::getInstance();
                $stmt = $pdo->prepare("SELECT salario_base FROM colaborador WHERE id_colaborador = :id");
                $stmt->bindValue(':id', $userId, \PDO::PARAM_INT);
                $stmt->execute();
                $salarioDireto = $stmt->fetchColumn();

                if ($salarioDireto !== false) {
                    $dados_colaborador['salario_base'] = $salarioDireto;
                }
            }
            $ultimo_ponto = $this->colaboradorModel->getUltimoPonto($userId);
            $beneficios_count = $this->colaboradorModel->getBeneficiosAtivosCount($userId);
            $salario_liquido = $this->colaboradorModel->getUltimoSalarioLiquido($userId);
            $beneficios_valor = $this->colaboradorModel->getTotalBeneficiosValor($userId);
            $descontos_valor = $this->colaboradorModel->getTotalDescontos($userId);


            // --- Lógica para Gráfico de Salário ---
            $itens_holerite = $this->colaboradorModel->getItensUltimoHolerite($userId);
            $salario_base_grafico = 0;
            $beneficios_grafico = 0;
            $descontos_grafico = 0;

            if (!empty($itens_holerite)) {
                foreach ($itens_holerite as $item) {
                    if (isset($item['descricao']) && $item['tipo'] === 'provento') {
                        if (stripos($item['descricao'], 'Salário Base') !== false) {
                            $salario_base_grafico += (float)$item['valor'];
                        } else {
                            $beneficios_grafico += (float)$item['valor'];
                        }
                    } elseif (isset($item['tipo']) && $item['tipo'] === 'desconto') {
                        $descontos_grafico += (float)$item['valor'];
                    }
                }
            }

            // Se não houver itens de holerite, usa o salário_base do colaborador
            if ($salario_base_grafico == 0 && empty($itens_holerite)) {
                $salario_base_grafico = (float)($dados_colaborador['salario_base'] ?? 0);
                $beneficios_grafico = 0;
                $descontos_grafico = 0;
            }

            $total_bruto_grafico = $salario_base_grafico + $beneficios_grafico;
            $salario_chart_style = '';
            $salario_chart_data_present = $total_bruto_grafico > 0 || $descontos_grafico > 0;

            if ($salario_chart_data_present) {
                $total_para_porcentagem = $total_bruto_grafico + $descontos_grafico;
                if ($total_para_porcentagem > 0) {
                    $perc_base = ($salario_base_grafico / $total_para_porcentagem) * 100;
                    $perc_beneficios = ($beneficios_grafico / $total_para_porcentagem) * 100;

                    $salario_chart_style = sprintf(
                        'background: conic-gradient(#4CAF50 0%% %1$.2f%%, #2196F3 %1$.2f%% %2$.2f%%, #F44336 %2$.2f%% 100%%);',
                        $perc_base,
                        $perc_base + $perc_beneficios
                    );
                }
            }

            // --- Lógica para Gráfico de Horas ---
            $dados_grafico_horas = $this->colaboradorModel->getDadosGraficoHoras($userId);
            $total_horas_semana_grafico = array_sum($dados_grafico_horas);
            $horas_chart_style = '';
            $horas_chart_data_present = $total_horas_semana_grafico > 0;

            if ($horas_chart_data_present) {
                $current_percentage = 0;
                $gradient_parts = [];
                $day_colors = [
                    1 => '#9E9E9E', // Domingo (Cinza)
                    2 => '#FFC107', // Segunda (Amarelo)
                    3 => '#FF9800', // Terça (Laranja)
                    4 => '#8BC34A', // Quarta (Verde Claro)
                    5 => '#00BCD4', // Quinta (Ciano)
                    6 => '#673AB7', // Sexta (Roxo)
                    7 => '#E91E63'  // Sábado (Rosa)
                ];

                for ($i = 1; $i <= 7; $i++) {
                    $horas = $dados_grafico_horas[$i] ?? 0;
                    if ($horas > 0) {
                        $percentage = ($horas / $total_horas_semana_grafico) * 100;
                        $start_angle = $current_percentage;
                        $end_angle = $current_percentage + $percentage;
                        $gradient_parts[] = sprintf('%s %1$.2f%% %2$.2f%%', $day_colors[$i], $start_angle, $end_angle);
                        $current_percentage = $end_angle;
                    }
                }
                if (!empty($gradient_parts)) {
                    $horas_chart_style = 'background: conic-gradient(' . implode(', ', $gradient_parts) . ');';
                }
            }

            // --- Montagem final dos dados para a View ---
            $viewData = array_merge((array)$dados_colaborador, [
                'ultimo_ponto' => $ultimo_ponto,
                'beneficios_count' => $beneficios_count,
                'beneficios_valor' => $beneficios_valor,
                'salario_liquido' => $salario_liquido,
                'horas_semana' => round($total_horas_semana_grafico),
                'salario_chart_style' => $salario_chart_style,
                'salario_chart_data_present' => $salario_chart_data_present,
                'horas_chart_style' => $horas_chart_style,
                'horas_chart_data_present' => $horas_chart_data_present,
            ]);

            return $this->view('Dashboard/colaborador', $viewData);
        }
    }
}