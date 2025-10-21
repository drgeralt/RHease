<?php
namespace App\Controller;

use App\Model\BeneficioModel;
use App\Core\Controller;

class BeneficioController extends Controller {
    private $model;

    public function __construct() {
        $this->model = new BeneficioModel();
    }

    /**
     * Carrega a View principal para o gerenciamento de benefícios (tela do admin).
     */
    public function gerenciamento() {
        try {
            $beneficios = $this->model->listarBeneficiosComCusto();
            $beneficios_selecao = $this->model->listarBeneficiosAtivosParaSelecao();
            $regras = $this->model->listarRegrasAtribuicao();
            $tiposContrato = ["CLT", "PJ", "Estágio", "Temporário"]; // Poderia vir de um Model também

            // O método 'view' do seu Controller base deve carregar o arquivo e extrair os dados
            return $this->view('Beneficios/beneficios_gerenciamento', [
                'beneficios' => $beneficios,
                'beneficios_selecao' => $beneficios_selecao,
                'regras' => $regras,
                'tiposContrato' => $tiposContrato
            ]);
        } catch (\Exception $e) {
            error_log("Erro ao carregar dados de gerenciamento: " . $e->getMessage());
            return $this->view('Common/error', ['mensagem' => 'Erro ao carregar a página de benefícios.']);
        }
    }

    // ===================================
    // MÉTODOS DE API (CHAMADOS VIA JAVASCRIPT)
    // ===================================

    /**
     * Salva ou edita um benefício no catálogo.
     */
    public function salvarBeneficio() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(false, "Método inválido.", null, 405); // Method Not Allowed
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: null;
        $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS);
        $categoria = filter_input(INPUT_POST, 'categoria', FILTER_SANITIZE_SPECIAL_CHARS);
        $tipo_valor = filter_input(INPUT_POST, 'tipo_valor', FILTER_SANITIZE_SPECIAL_CHARS);
        $valor_fixo = filter_input(INPUT_POST, 'valor_fixo', FILTER_VALIDATE_FLOAT) ?: null;

        if (empty($nome) || empty($categoria) || empty($tipo_valor)) {
            return $this->jsonResponse(false, "Todos os campos obrigatórios devem ser preenchidos.", null, 400); // Bad Request
        }

        try {
            $this->model->salvarBeneficio($id, $nome, $categoria, $tipo_valor, $valor_fixo);
            $mensagem = $id ? 'Benefício editado com sucesso!' : 'Benefício criado com sucesso!';
            return $this->jsonResponse(true, $mensagem);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage(), null, 500); // Internal Server Error
        }
    }

    /**
     * Deleta um benefício permanentemente.
     */
    public function deletarBeneficio() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(false, "Método inválido.", null, 405);
        }
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            return $this->jsonResponse(false, "ID do benefício não informado.", null, 400);
        }

        try {
            $this->model->deletarBeneficio($id);
            return $this->jsonResponse(true, 'Benefício deletado permanentemente com sucesso!');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Alterna o status de um benefício entre 'Ativo' e 'Inativo'.
     */
    public function toggleStatus() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(false, "Método inválido.", null, 405);
        }
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            return $this->jsonResponse(false, "ID do benefício não informado.", null, 400);
        }

        try {
            $novo_status = $this->model->toggleStatus($id);
            return $this->jsonResponse(true, 'Status atualizado para ' . $novo_status . ' com sucesso!');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, "Erro ao atualizar status: " . $e->getMessage(), null, 500);
        }
    }

    /**
     * Salva as regras de atribuição para um tipo de contrato.
     */
    public function salvarRegrasAtribuicao() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(false, "Método inválido.", null, 405);
        }

        // CORREÇÃO: Substituído filtro deprecado FILTER_SANITIZE_STRING
        $tipo_contrato = filter_input(INPUT_POST, 'tipo_contrato', FILTER_SANITIZE_SPECIAL_CHARS);
        $beneficios_ids = $_POST['beneficios_ids'] ?? [];

        if (empty($tipo_contrato)) {
            return $this->jsonResponse(false, 'Tipo de contrato não informado.', null, 400);
        }

        try {
            $this->model->salvarRegrasAtribuicao($tipo_contrato, $beneficios_ids);
            return $this->jsonResponse(true, 'Regras salvas com sucesso para ' . $tipo_contrato . '!');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, 'Erro ao salvar regras: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Busca colaboradores por um termo de pesquisa (usado no autocompletar).
     */
    public function buscarColaborador() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(false, "Método inválido.", null, 405);
        }
        // CORREÇÃO: Substituído filtro deprecado FILTER_SANITIZE_STRING
        $termo = filter_input(INPUT_POST, 'termo', FILTER_SANITIZE_SPECIAL_CHARS);

        // Retorna um array vazio se o termo for muito curto, evitando buscas desnecessárias
        if (strlen($termo) < 3) {
            return $this->jsonResponse(true, '', []);
        }

        try {
            $colaboradores = $this->model->buscarColaborador($termo);
            return $this->jsonResponse(true, '', $colaboradores);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, "Erro ao buscar: " . $e->getMessage(), null, 500);
        }
    }

    /**
     * Carrega os dados de um colaborador e os seus benefícios manuais.
     */
    public function carregarBeneficiosColaborador() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(false, "Método inválido.", null, 405);
        }
        $id_colaborador = filter_input(INPUT_POST, 'id_colaborador', FILTER_VALIDATE_INT);

        if (!$id_colaborador) {
            return $this->jsonResponse(false, 'ID de colaborador inválido.', null, 400);
        }

        try {
            $dados = $this->model->carregarBeneficiosColaborador($id_colaborador);
            return $this->jsonResponse(true, '', $dados);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Salva as exceções (benefícios manuais) para um colaborador.
     */
    public function salvarBeneficiosColaborador() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(false, "Método inválido.", null, 405);
        }

        // CORREÇÃO: Removido código redundante que lia de $_POST duas vezes.
        $id_colaborador = filter_input(INPUT_POST, 'id_colaborador', FILTER_VALIDATE_INT);
        $beneficios_ids = $_POST['beneficios_ids'] ?? [];

        if (!$id_colaborador) {
            return $this->jsonResponse(false, 'ID do colaborador não informado.', null, 400);
        }

        try {
            $this->model->salvarBeneficiosColaborador($id_colaborador, $beneficios_ids);
            return $this->jsonResponse(true, 'Benefícios manuais salvos com sucesso!');
        } catch (\Exception $e) {
            error_log("Erro ao salvar benefícios do colaborador: " . $e->getMessage());
            return $this->jsonResponse(false, 'Ocorreu um erro ao salvar. Por favor, contate o suporte.', null, 500);
        }
    }
    public function meusBeneficios() {
        // A lógica de sessão deve ser centralizada, mas por agora, isso funciona.
        session_start();
        $id_colaborador_logado = $_SESSION['id_colaborador'] ?? null;

        if (!$id_colaborador_logado) {
            header('Location: ' . BASE_URL); // Redireciona se não estiver logado
            exit();
        }

        // 1. Controller chama o Model para buscar os dados
        $dados = $this->model->buscarBeneficiosParaColaborador($id_colaborador_logado);
        $lista_beneficios = $dados['beneficios'];
        $nome_colaborador = $dados['nome']; // Passa o nome para a View

        // 2. Controller carrega a View e passa as variáveis para ela
        return $this->view('Beneficios/meus_beneficios', [
            'lista_beneficios' => $lista_beneficios,
            'nome_colaborador' => $nome_colaborador
        ]);
    }
}