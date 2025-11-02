<?php
namespace App\Services;

// Importa o Model/Repository para que o Service possa usá-lo
use App\Model\BeneficioModel;
use InvalidArgumentException; // Exceção específica para erros de validação

class BeneficiosService {
    private $beneficioModel;

    public function __construct() {
        // O Service instancia o Model/Repository que ele precisa
        $this->beneficioModel = new BeneficioModel();
    }

    /**
     * Busca todos os benefícios para a tela de gerenciamento.
     * @return array
     */
    public function getAllBeneficios(): array {
        return $this->beneficioModel->listarBeneficiosComCusto();
    }

    /**
     * Busca todos os benefícios ativos para preencher selects e modais.
     * @return array
     */
    public function getAllBeneficiosAtivosParaSelecao(): array {
        return $this->beneficioModel->listarBeneficiosAtivosParaSelecao();
    }

    /**
     * Busca todas as regras de atribuição formatadas.
     * @return array
     */
    public function getAllRegrasAtribuicao(): array {
        return $this->beneficioModel->listarRegrasAtribuicao();
    }

    /**
     * Valida e cria um benefício.
     * @param array $dados Os dados vindo do formulário ($_POST)
     * @return int O ID do novo benefício
     * @throws InvalidArgumentException Se a validação falhar
     */
    public function criarBeneficio(array $dados): int {
        $nome = trim($dados['nome'] ?? '');
        $categoria = $dados['categoria'] ?? '';
        $tipo_valor = $dados['tipo_valor'] ?? '';
        $valor_fixo = !empty($dados['valor_fixo']) ? (float)$dados['valor_fixo'] : null;

        // --- Lógica de Negócio (Validação) ---
        if (empty($nome) || empty($categoria) || empty($tipo_valor)) {
            throw new InvalidArgumentException("Nome, categoria e tipo de valor são obrigatórios.");
        }
        if ($tipo_valor === 'Fixo' && $valor_fixo === null) {
            throw new InvalidArgumentException("Para benefícios do tipo 'Fixo', o valor é obrigatório.");
        }

        // Chama o Model para salvar, passando null para o ID (criação)
        return $this->beneficioModel->salvarBeneficio(null, $nome, $categoria, $tipo_valor, $valor_fixo);
    }

    /**
     * Valida e edita um benefício existente.
     * @param int $id
     * @param array $dados Os dados vindo do formulário ($_POST)
     * @return bool
     * @throws InvalidArgumentException
     */
    public function editarBeneficio(int $id, array $dados): bool {
        if ($id <= 0) {
            throw new InvalidArgumentException("ID do benefício é inválido.");
        }
        // Reutiliza a mesma lógica de validação da criação
        return $this->criarBeneficio($dados + ['id' => $id]) > 0;
    }

    /**
     * Deleta um benefício.
     * @param int $id
     * @return bool
     */
    public function deletarBeneficio(int $id): bool {
        return $this->beneficioModel->deletarBeneficio($id);
    }

    /**
     * Alterna o status de um benefício.
     * @param int $id
     * @return string O novo status ('Ativo' ou 'Inativo')
     */
    public function toggleStatus(int $id): string {
        // Lógica de negócio: o Model não precisa saber como alternar, apenas como definir um status.
        // O Service decide qual será o novo status.
        return $this->beneficioModel->toggleStatus($id);
    }

    /**
     * Salva as regras de atribuição para um contrato.
     * @param string $tipoContrato
     * @param array $beneficiosIds
     * @return bool
     */
    public function salvarRegras(string $tipoContrato, array $beneficiosIds): bool {
        return $this->beneficioModel->salvarRegrasAtribuicao($tipoContrato, $beneficiosIds);
    }

    /**
     * Busca colaboradores por um termo.
     * @param string $termo
     * @return array
     */
    public function buscarColaborador(string $termo): array {
        return $this->beneficioModel->buscarColaborador($termo);
    }

    /**
     * Carrega os dados de um colaborador e os seus benefícios manuais.
     * @param int $idColaborador
     * @return array
     */
    public function carregarBeneficiosColaborador(int $idColaborador): array {
        return $this->beneficioModel->carregarBeneficiosColaborador($idColaborador);
    }

    /**
     * Salva os benefícios manuais (exceções) de um colaborador.
     * @param int $idColaborador
     * @param array $beneficiosIds
     * @return bool
     */
    public function salvarBeneficiosColaborador(int $idColaborador, array $beneficiosIds): bool {
        return $this->beneficioModel->salvarBeneficiosColaborador($idColaborador, $beneficiosIds);
    }
}