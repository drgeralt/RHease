<?php
require_once 'Models/BeneficioModel.php';

class BeneficioController {
    private $model;

    public function __construct() {
        $this->model = new BeneficioModel();
    }

    // Retorna todos os benefícios
    public function index() {
        return $this->model->listarBeneficios();
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
        $this->model->deletarBeneficio($id);
    }

    // Alternar status
    public function toggleStatus($id, $status) {
        $this->model->atualizarStatus($id, $status);
    }
}
?>
