<?php

namespace App\Controller;

use App\Core\Controller;
use App\Model\BeneficioModel;
use App\Model\ColaboradorModel;
use PDO;

class BeneficioController extends Controller
{
    protected BeneficioModel $beneficioModel;

    // Propriedade para compatibilidade se alguma view antiga usar $this->model
    // Definida sem tipo para evitar erro de Deprecated no PHP 8.2+
    protected $model;

    public function __construct(BeneficioModel $beneficioModel, PDO $pdo)
    {
        // Passa o PDO para o pai (Controller), que popula $this->pdo
        parent::__construct($pdo);

        $this->beneficioModel = $beneficioModel;
        $this->model = $beneficioModel;
    }

    /**
     * Carrega a View principal de Gerenciamento (Admin/RH).
     */
    public function gerenciamento()
    {
        $this->exigirPermissaoGestor();

        try {
            $beneficios = $this->beneficioModel->listarBeneficiosComCusto();
            $beneficios_selecao = $this->beneficioModel->listarBeneficiosAtivosParaSelecao();
            $regras = $this->beneficioModel->listarRegrasAtribuicao();
            $tiposContrato = ["CLT", "PJ", "Estágio", "Temporário"];

            // Ajustado para a pasta 'Beneficios' (plural)
            return $this->view('Beneficios/beneficios_gerenciamento', [
                'beneficios' => $beneficios,
                'beneficios_selecao' => $beneficios_selecao,
                'regras' => $regras,
                'tiposContrato' => $tiposContrato
            ]);
        } catch (\Exception $e) {
            error_log("Erro: " . $e->getMessage());
            die("Erro ao carregar página: " . $e->getMessage());
        }
    }

    /**
     * Carrega a View de Meus Benefícios (Colaborador).
     */
    public function meusBeneficios()
    {
        // Verifica se está logado
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit();
        }

        $id_colaborador_logado = $_SESSION['user_id'];

        try {
            $dados = $this->beneficioModel->buscarBeneficiosParaColaborador($id_colaborador_logado);

            // Ajustado para a pasta 'Beneficios' (plural)
            return $this->view('Beneficios/meus_beneficios', [
                'lista_beneficios' => $dados['beneficios'],
                'nome_colaborador' => $dados['nome']
            ]);
        } catch (\Exception $e) {
            error_log("Erro meusBeneficios: " . $e->getMessage());
            die("Erro ao carregar seus benefícios.");
        }
    }

    // ===================================
    // API: MÉTODOS DE CATÁLOGO
    // ===================================

    public function salvarBeneficio()
    {
        $this->exigirPermissaoGestor();
        $this->verificarMetodoPost();

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: null;
        $nome = $_POST['nome'] ?? '';
        $categoria = $_POST['categoria'] ?? '';
        $tipo_valor = $_POST['tipo_valor'] ?? '';
        $valor_fixo = filter_input(INPUT_POST, 'valor_fixo', FILTER_VALIDATE_FLOAT) ?: null;

        try {
            $this->beneficioModel->salvarBeneficio($id, $nome, $categoria, $tipo_valor, $valor_fixo);
            $this->jsonResponse(true, $id ? 'Benefício editado!' : 'Benefício criado!');
        } catch (\Exception $e) {
            $this->jsonResponse(false, $e->getMessage());
        }
    }

    public function deletarBeneficio()
    {
        $this->exigirPermissaoGestor();
        $this->verificarMetodoPost();
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        try {
            $this->beneficioModel->deletarBeneficio($id);
            $this->jsonResponse(true, 'Benefício deletado!');
        } catch (\Exception $e) {
            $this->jsonResponse(false, $e->getMessage());
        }
    }

    public function toggleStatus()
    {
        $this->exigirPermissaoGestor();
        $this->verificarMetodoPost();
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        try {
            $novo = $this->beneficioModel->toggleStatus($id);
            $this->jsonResponse(true, 'Status alterado para ' . $novo);
        } catch (\Exception $e) {
            $this->jsonResponse(false, $e->getMessage());
        }
    }

    // ===================================
    // API: REGRAS AUTOMÁTICAS
    // ===================================

    public function salvarRegrasAtribuicao()
    {
        $this->exigirPermissaoGestor();
        $this->verificarMetodoPost();
        $tipo = $_POST['tipo_contrato'] ?? '';
        $ids = $_POST['beneficios_ids'] ?? [];

        try {
            $this->beneficioModel->salvarRegrasAtribuicao($tipo, $ids);
            $this->jsonResponse(true, 'Regras salvas!');
        } catch (\Exception $e) {
            $this->jsonResponse(false, $e->getMessage());
        }
    }

    // ===================================
    // API: EXCEÇÕES DE COLABORADOR
    // ===================================

    public function carregarPorColaborador()
    {
        $this->exigirPermissaoGestor();
        header('Content-Type: application/json');

        try {
            $id = (int)($_POST['id_colaborador'] ?? 0);

            if (!$id) throw new \Exception('ID inválido');

            // Instancia ColaboradorModel usando $this->pdo (herdado)
            $colaboradorModel = new ColaboradorModel($this->pdo);
            $dados = $colaboradorModel->buscarPorId($id);

            if (!$dados) throw new \Exception('Colaborador não encontrado.');

            // Normaliza dados
            $info = $dados['colaborador'] ?? $dados;

            $idsAtuais = $this->beneficioModel->getIdsBeneficiosPorColaborador($id);

            echo json_encode([
                'success' => true,
                'dados_colaborador' => [
                    'nome_completo' => $info['nome_completo'] ?? 'Desconhecido',
                    'matricula' => $info['matricula'] ?? '--',
                    'tipo_contrato' => $info['tipo_contrato'] ?? '--'
                ],
                'beneficios_ids' => $idsAtuais
            ]);

        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'mensagem' => $e->getMessage()]);
        }
        exit;
    }

    public function salvarPorColaborador()
    {
        $this->exigirPermissaoGestor();
        header('Content-Type: application/json');

        $id = (int)($_POST['id_colaborador'] ?? 0);
        $ids = $_POST['beneficios_ids'] ?? [];

        if (is_array($ids)) {
            $ids = array_map('intval', $ids);
        } else {
            $ids = [];
        }

        if (!$id) {
            echo json_encode(['success' => false, 'mensagem' => 'ID inválido']);
            exit;
        }

        $sucesso = $this->beneficioModel->salvarVinculosColaborador($id, $ids);

        if ($sucesso) {
            echo json_encode(['success' => true, 'mensagem' => 'Exceções salvas!']);
        } else {
            echo json_encode(['success' => false, 'mensagem' => 'Erro ao salvar.']);
        }
        exit;
    }

    // ===================================
    // HELPERS
    // ===================================

    private function verificarMetodoPost()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(false, 'Método inválido', null, 405);
            exit;
        }
    }

    protected function jsonResponse($success, $message, $data = null, $code = 200)
    {
        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode(['success' => $success, 'mensagem' => $message, 'data' => $data]);
        exit;
    }
}