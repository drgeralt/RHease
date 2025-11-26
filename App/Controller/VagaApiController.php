<?php

namespace App\Controller;

use App\Core\Controller;
use App\Model\GestaoVagasModel;
use PDO;

class VagaApiController extends Controller
{
    protected GestaoVagasModel $vagaModel;

    public function __construct(GestaoVagasModel $vagaModel, PDO $pdo)
    {
        parent::__construct($pdo);
        $this->vagaModel = $vagaModel;
    }

    // 1. LISTAR TODAS (GET /api/vagas/listar)
    public function listarVagas()
    {
        header('Content-Type: application/json');
        try {
            $vagas = $this->vagaModel->listarTodas();
            echo json_encode(['success' => true, 'data' => $vagas]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    // 2. SALVAR NOVA (POST /api/vagas/salvar)
    public function salvar()
    {
        $this->exigirPermissaoGestor();
        $this->verificarMetodoPost();

        $dados = [
            'titulo' => $_POST['titulo'] ?? '',
            'departamento' => $_POST['departamento'] ?? '',
            'descricao' => $_POST['descricao'] ?? '',
            'status' => $_POST['status'] ?? 'rascunho',
            'skills_necessarias' => $_POST['skills_necessarias'] ?? '',
            'skills_recomendadas' => $_POST['skills_recomendadas'] ?? '',
            'skills_desejadas' => $_POST['skills_desejadas'] ?? ''
        ];

        try {
            $this->vagaModel->criar($dados);
            echo json_encode(['success' => true, 'message' => 'Vaga criada com sucesso!']);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    // 3. PEGAR DADOS PARA EDIÇÃO (GET /api/vagas/editar?id=1)
    public function editar()
    {
        $this->exigirPermissaoGestor();
        header('Content-Type: application/json');
        $id = (int)($_GET['id'] ?? 0);

        try {
            $vaga = $this->vagaModel->buscarPorId($id);
            if ($vaga) {
                echo json_encode(['success' => true, 'data' => $vaga]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Vaga não encontrada.']);
            }
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    // 4. ATUALIZAR (POST /api/vagas/atualizar)
    public function atualizar()
    {
        $this->exigirPermissaoGestor();
        $this->verificarMetodoPost();
        $id = (int)($_POST['id_vaga'] ?? 0);

        $dados = [
            'titulo' => $_POST['titulo'],
            'departamento' => $_POST['departamento'],
            'descricao' => $_POST['descricao'],
            'status' => $_POST['status'],
            'skills_necessarias' => $_POST['skills_necessarias'],
            'skills_recomendadas' => $_POST['skills_recomendadas'],
            'skills_desejadas' => $_POST['skills_desejadas']
        ];

        try {
            $this->vagaModel->atualizar($id, $dados);
            echo json_encode(['success' => true, 'message' => 'Vaga atualizada!']);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    // 5. EXCLUIR (GET /api/vagas/excluir?id=1)
    public function excluir()
    {
        $this->exigirPermissaoGestor();
        header('Content-Type: application/json');
        $id = (int)($_GET['id'] ?? 0);

        try {
            $this->vagaModel->excluir($id);
            echo json_encode(['success' => true, 'message' => 'Vaga excluída.']);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    public function verCandidatos()
    {
        $this->exigirPermissaoGestor();
        header('Content-Type: application/json');
        $id = (int)($_GET['id'] ?? 0);

        try {
            $vaga = $this->vagaModel->buscarPorId($id);
            $candidatos = $this->vagaModel->listarCandidatos($id);

            echo json_encode([
                'success' => true,
                'titulo_vaga' => $vaga['titulo'] ?? 'Desconhecida',
                'data' => $candidatos
            ]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    private function verificarMetodoPost()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método inválido']);
            exit;
        }
    }
}