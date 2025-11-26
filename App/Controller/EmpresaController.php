<?php
namespace App\Controller;

use App\Core\Controller;
use App\Model\EmpresaModel;
use PDO;

class EmpresaController extends Controller
{
    protected EmpresaModel $empresaModel;

    public function __construct(EmpresaModel $empresaModel, PDO $pdo)
    {
        parent::__construct($pdo);
        $this->empresaModel = $empresaModel;
    }

    // API: Retorna lista e qual está ativa
    public function listar()
    {
        header('Content-Type: application/json');
        $todas = $this->empresaModel->listarTodas();
        $ativa = $this->empresaModel->getEmpresaAtiva();

        echo json_encode(['success' => true, 'lista' => $todas, 'ativa_id' => $ativa['id_empresa']]);
        exit;
    }

    // API: Troca a empresa na sessão
    public function trocar()
    {
        header('Content-Type: application/json');
        $id = (int)($_POST['id'] ?? 0);

        if ($this->empresaModel->definirComoAtiva($id)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Empresa inválida']);
        }
        exit;
    }

    // API: Salvar/Editar
    public function salvar()
    {
        header('Content-Type: application/json');
        // Adicione validações de segurança aqui
        $dados = [
            'id' => $_POST['id'] ?? null,
            'razao_social' => $_POST['razao_social'],
            'cnpj' => $_POST['cnpj'],
            'endereco' => $_POST['endereco'],
            'cidade_uf' => $_POST['cidade_uf']
        ];

        if ($this->empresaModel->salvar($dados)) {
            echo json_encode(['success' => true, 'message' => 'Dados salvos!']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erro ao salvar.']);
        }
        exit;
    }
}