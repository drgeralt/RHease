<?php

namespace App\Controller;

use App\Core\Controller;
use App\Model\ColaboradorModel;
use PDO;

class GestaoFacialController extends Controller
{
    protected ColaboradorModel $colaboradorModel;

    public function __construct(ColaboradorModel $colaboradorModel, PDO $pdo)
    {
        parent::__construct($pdo);
        $this->colaboradorModel = $colaboradorModel;
    }

    public function index()
    {
        $this->exigirPermissaoGestor();
        $colaboradores = $this->colaboradorModel->listarStatusFaces();

        require_once BASE_PATH . '/App/View/Gestor/gestaoFacial.php';
    }

    public function resetar()
    {
        $this->exigirPermissaoGestor();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Método inválido']);
            exit;
        }

        $id = $_POST['id'] ?? null;

        if (!$id) {
            echo json_encode(['status' => 'error', 'message' => 'ID não informado']);
            exit;
        }

        if ($this->colaboradorModel->resetarFace((int)$id)) {
            echo json_encode(['status' => 'success', 'message' => 'Face resetada. O colaborador deverá cadastrar novamente.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao resetar face.']);
        }
    }
}