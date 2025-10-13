<?php
namespace App\Controller; 

use App\Model\BeneficioModel;
use App\Core\Controller;
class BeneficioController extends Controller{
    private $model;

    public function __construct() {
        $this->model = new BeneficioModel();
    }

    // Retorna todos os benefícios
    public function index() {
        return $this->view('Beneficios/meus_beneficios');
    }

    public function meusBeneficios() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    require_once dirname(__DIR__, 2) . '/App/View/Beneficios/meus_beneficios.php'; 

    $id_colaborador_logado = $_SESSION['id_colaborador'] ?? null;
    
    if (!$id_colaborador_logado) {
        header("Location: /login"); 
        exit();
    }
    
    require_once dirname(__DIR__) . '/View/Beneficios/meus_beneficios.php'; 
}

    // Criar benefício
    public function criar($nome, $categoria, $tipo_valor, $valor_fixo = null, $descricao = null) {
        $this->model->criarBeneficio($nome, $categoria, $tipo_valor, $valor_fixo, $descricao);
    }

    // Editar benefício
    public function editar($id, $nome, $categoria, $tipo_valor, $valor_fixo = null, $descricao = null, $status = 1) {
        $this->model->editarBeneficio($id, $nome, $categoria, $tipo_valor, $valor_fixo, $descricao, $status);
    }

    // Deletar benefício
    public function deletar($id) {
        $this->model->desativarBeneficio($id);
    }

    // Alternar status
    //public function toggleStatus($id, $status) { // VERIFICAR FUNCIONALIDADE NO MODEL
      // $this->model->atualizarStatus($id, $status);
    //}
}
?>