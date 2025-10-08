<?php
declare(strict_types=1);

namespace App\Controller;

use App\Core\Controller;
use App\Core\Database;
use App\Model\BeneficioModel;

class BeneficioController extends Controller
{
    private BeneficioModel $beneficioModel;
    public function gerenciamento()
    {
        // Carrega o ficheiro antigo diretamente.
        require_once BASE_PATH . '/App/View/Beneficios/beneficios_gerenciamento.php';
        // A função termina aqui, pois o ficheiro requerido já gera toda a página.
    }
    public function __construct()
    {
        parent::__construct(); // garante que o Controller base funcione
        $db = Database::getInstance();
        $this->beneficioModel = new BeneficioModel($db);
    }
    public function apiAntiga()
    {
        // Carrega a lógica do ficheiro de ações diretamente.
        require_once BASE_PATH . '/App/View/Beneficios/acoes_beneficio.php';
    }
    /** Página principal de benefícios */
    public function index(): void
    {
        try {
            $beneficios = $this->beneficioModel->listarBeneficiosCatalogo();
            $regras = $this->beneficioModel->listarRegras();

            $this->view('Beneficios/beneficios', [
                'beneficios' => $beneficios,
                'regras' => $regras
            ]);
        } catch (\Throwable $e) {
            die("Erro no BeneficioController::index(): " . $e->getMessage());
        }
    }

    /** Endpoint JSON para AJAX */
    public function api(): void
    {
        header('Content-Type: application/json');
        $acao = $_POST['acao'] ?? $_GET['acao'] ?? null;

        try {
            switch ($acao) {
                case 'criar':
                    $id = $this->beneficioModel->criarBeneficio($_POST);
                    echo json_encode(['success' => true, 'mensagem' => 'Benefício criado!', 'id' => $id]);
                    break;

                case 'editar':
                    $ok = $this->beneficioModel->editarBeneficio($_POST);
                    echo json_encode(['success' => $ok, 'mensagem' => $ok ? 'Atualizado!' : 'Falha ao atualizar']);
                    break;

                case 'deletar':
                    $ok = $this->beneficioModel->deletarBeneficio((int)$_POST['id']);
                    echo json_encode(['success' => $ok, 'mensagem' => $ok ? 'Excluído!' : 'Erro ao excluir']);
                    break;

                case 'status':
                    $ok = $this->beneficioModel->atualizarStatus((int)$_POST['id']);
                    echo json_encode(['success' => $ok, 'mensagem' => 'Status atualizado']);
                    break;

                case 'salvarRegras':
                    $ok = $this->beneficioModel->salvarRegras($_POST['tipo_contrato'], $_POST['ids']);
                    echo json_encode(['success' => $ok, 'mensagem' => 'Regras salvas!']);
                    break;

                default:
                    echo json_encode(['success' => false, 'mensagem' => 'Ação inválida']);
                    break;
            }
        } catch (\Throwable $e) {
            echo json_encode(['success' => false, 'mensagem' => 'Erro: ' . $e->getMessage()]);
        }
    }
}
