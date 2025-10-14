<?php
namespace App\Controller; 

use App\Model\BeneficioModel;
use App\Core\Controller;

class BeneficioController extends Controller {
    private $model;

    public function __construct() {
        // Assume que App\Core\Controller ou sua estrutura carrega o Model
        $this->model = new BeneficioModel(); 
    }


    // Método principal para carregar a tela de Gerenciamento de Benefícios
    public function gerenciamento() {
    try {
        $beneficios = $this->model->listarBeneficiosComCusto();
        $beneficios_selecao = $this->model->listarBeneficiosAtivosParaSelecao();
        $regras = $this->model->listarRegrasAtribuicao();
        $tiposContrato = ["CLT", "PJ", "Estágio", "Temporário"];

        return $this->view('Beneficios/beneficios_gerenciamento', [
            'beneficios' => $beneficios,
            'beneficios_selecao' => $beneficios_selecao,
            'regras' => $regras,
            'tiposContrato' => $tiposContrato
        ]);
    } catch (\Exception $e) {
        error_log("Erro ao carregar dados de gerenciamento: " . $e->getMessage());
        return $this->view('Common/error', ['mensagem' => 'Erro ao carregar benefícios.']);
    }
}


    
    // Método para salvar/editar benefício (Ação 'criar' e 'editar')
    public function salvarBeneficio() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(false, "Método inválido.");
        }
        
        // Validação e sanitização dos inputs
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?? null;
        $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS);
        $categoria = filter_input(INPUT_POST, 'categoria', FILTER_SANITIZE_SPECIAL_CHARS);
        $tipo_valor = filter_input(INPUT_POST, 'tipo_valor', FILTER_SANITIZE_SPECIAL_CHARS);

        $valor_fixo = filter_input(INPUT_POST, 'valor_fixo', FILTER_VALIDATE_FLOAT) ?? null;

        if (empty($nome) || empty($categoria) || empty($tipo_valor)) {
            return $this->jsonResponse(false, "Todos os campos obrigatórios devem ser preenchidos.");
        }
        
        try {
            $this->model->salvarBeneficio($id, $nome, $categoria, $tipo_valor, $valor_fixo);
            $mensagem = $id ? 'Benefício editado com sucesso!' : 'Benefício criado com sucesso!';
            return $this->jsonResponse(true, $mensagem);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }
    
    // Método para deletar benefício (Ação 'deletar')
    public function deletarBeneficio() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(false, "Método inválido.");
        }
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            return $this->jsonResponse(false, "ID do benefício não informado.");
        }

        try {
            $this->model->deletarBeneficio($id);
            return $this->jsonResponse(true, 'Benefício deletado permanentemente com sucesso!');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    // Método para alternar status (Ação 'desativar')
    public function toggleStatus() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(false, "Método inválido.");
        }
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            return $this->jsonResponse(false, "ID do benefício não informado.");
        }

        try {
            $novo_status = $this->model->toggleStatus($id);
            return $this->jsonResponse(true, 'Status atualizado para ' . $novo_status . ' com sucesso!');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, "Erro ao atualizar status: " . $e->getMessage());
        }
    }

    // Método para salvar regras de atribuição (Ação 'salvar_regras')
    public function salvarRegrasAtribuicao() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(false, "Método inválido.");
        }
        
        $tipo_contrato = filter_input(INPUT_POST, 'tipo_contrato', FILTER_SANITIZE_STRING);
        $beneficios_ids = $_POST['beneficios_ids'] ?? []; // Array de IDs, sem sanitização aqui, apenas no Model (cast para int)
        
        if (empty($tipo_contrato)) {
            return $this->jsonResponse(false, 'Tipo de contrato não informado.');
        }

        try {
            $this->model->salvarRegrasAtribuicao($tipo_contrato, $beneficios_ids);
            return $this->jsonResponse(true, 'Regras de Atribuição salvas com sucesso para ' . $tipo_contrato . '!');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, 'Erro ao salvar regras: ' . $e->getMessage());
        }
    }

    // Método para buscar colaborador (Ação 'buscar_colaborador')
    public function buscarColaborador() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(false, "Método inválido.");
        }
        $termo = filter_input(INPUT_POST, 'termo', FILTER_SANITIZE_STRING);

        if (empty($termo)) {
            return $this->jsonResponse(true, '', []);
        }

        try {
            $colaboradores = $this->model->buscarColaborador($termo);
            return $this->jsonResponse(true, '', $colaboradores);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, "Erro ao buscar: " . $e->getMessage());
        }
    }
    
    // Método para carregar benefícios do colaborador (Ação 'carregar_beneficios_colaborador')
    public function carregarBeneficiosColaborador() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(false, "Método inválido.");
        }
        $id_colaborador = filter_input(INPUT_POST, 'id_colaborador', FILTER_VALIDATE_INT);
        
        if (!$id_colaborador) {
            return $this->jsonResponse(false, 'ID de colaborador inválido.');
        }
        
        try {
            $dados = $this->model->carregarBeneficiosColaborador($id_colaborador);
            return $this->jsonResponse(true, '', $dados);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }

    // Método para salvar benefícios do colaborador (Ação 'salvar_beneficios_colaborador')
    public function salvarBeneficiosColaborador() {

        $id_colaborador = $_POST['id_colaborador'] ?? null;
        $beneficios_ids = $_POST['beneficios_ids'] ?? [];
       

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(false, "Método inválido.");
        }
        
        $id_colaborador = filter_input(INPUT_POST, 'id_colaborador', FILTER_VALIDATE_INT);
        $beneficios_ids = $_POST['beneficios_ids'] ?? []; // Array de IDs, sem sanitização aqui

        if (!$id_colaborador) {
            return $this->jsonResponse(false, 'ID do colaborador não informado.');
        }

        try {
            $this->model->salvarBeneficiosColaborador($id_colaborador, $beneficios_ids);
            return $this->jsonResponse(true, 'Benefícios manuais salvos com sucesso!');
        } catch (\Exception $e) {
            error_log("Erro ao salvar: " . $e->getMessage());
            return $this->jsonResponse(false, 'Erro ao salvar: Contate o suporte.'); 
        }
    }

    // Função auxiliar para retornar JSON (deve estar em App\Core\Controller ou similar)
    private function jsonResponse(bool $success, string $mensagem, $data = null) {
    // 1. Envia o Content-Type: application/json
    header('Content-Type: application/json');
    
    // 2. Monta o array de resposta
    $response = ['success' => $success, 'mensagem' => $mensagem];
    if ($data !== null) {
        $response['data'] = $data;
    }
    
    // 3. Imprime a string JSON
    echo json_encode($response);
    
    // 4. FORÇA A PARADA DO SCRIPT AQUI!
    // Esta é a linha que impede qualquer View de ser carregada depois.
    exit; 
}
    
    // O seu método applyDefaultRules de beneficios_gerenciamento.php
    public function aplicarRegrasPadraoColaborador($idColaborador) {
        try {
            return $this->model->aplicarRegrasPadrao($idColaborador);
        } catch (\Exception $e) {
            // Logar erro e retornar falso ou lançar exceção
            error_log("Erro em aplicarRegrasPadrao: " . $e->getMessage());
            return false;
        }
    }
}