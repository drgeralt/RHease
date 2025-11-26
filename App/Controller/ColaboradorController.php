<?php
declare(strict_types=1);

namespace App\Controller;

use App\Core\Controller;
use App\Model\CargoModel;
use App\Model\ColaboradorModel;
use App\Model\EnderecoModel;
use App\Model\SetorModel;
use PDOException;
use PDO;

class ColaboradorController extends Controller
{
    protected ColaboradorModel $colaboradorModel;
    protected EnderecoModel $enderecoModel;
    protected CargoModel $cargoModel;
    protected SetorModel $setorModel;

    public function __construct(
        ColaboradorModel $colaboradorModel,
        EnderecoModel $enderecoModel,
        CargoModel $cargoModel,
        SetorModel $setorModel,
        PDO $pdo
    ) {
        parent::__construct($pdo);
        $this->colaboradorModel = $colaboradorModel;
        $this->enderecoModel = $enderecoModel;
        $this->cargoModel = $cargoModel;
        $this->setorModel = $setorModel;
        $this->pdo = $pdo;
    }

    public function listar()
    {
        $this->exigirPermissaoGestor();
        $todosOsColaboradores = $this->colaboradorModel->listarColaboradores();
        return $this->view('Colaborador/tabelaColaborador', [
            'colaboradores' => $todosOsColaboradores
        ]);
    }

    // Método usado pelo AJAX para preencher o Modal de Edição
    public function buscarDados(): void
    {
        $this->exigirPermissaoGestor();
        header('Content-Type: application/json');

        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['erro' => true, 'msg' => 'ID inválido']);
            exit;
        }

        $dados = $this->colaboradorModel->buscarPorId($id);

        if (!$dados) {
            echo json_encode(['erro' => true, 'msg' => 'Colaborador não encontrado']);
            exit;
        }

        echo json_encode($dados);
        exit;
    }

    public function criar(): void
    {
        $this->exigirPermissaoGestor();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/colaboradores');
            exit;
        }

        $post = $_POST;
        $this->pdo->beginTransaction();

        try {
            $idEndereco = $this->enderecoModel->create($post);

            // Sanitização de dinheiro (R$ 3.500,00 -> 3500.00)
            $salario = (float)str_replace(['R$', '.', ','], ['', '', '.'], $post['salario'] ?? 0);

            $idCargo = $this->cargoModel->findOrCreateByName($post['cargo'], $salario);

            // Aceita tanto 'setor' quanto 'departamento' do post
            $nomeSetor = $post['setor'] ?? $post['departamento'] ?? '';
            $idSetor = $this->setorModel->findOrCreateByName($nomeSetor);

            $dadosColaborador = [
                'matricula' => $post['matricula'] ?? null,
                'nome_completo' => $post['nome_completo'] ?? '',
                'data_nascimento' => $post['data_nascimento'] ?? null,
                'cpf' => $post['cpf'] ?? '',
                'rg' => $post['rg'] ?? '',
                'genero' => $post['genero'] ?? null,
                'email_pessoal' => $post['email_pessoal'] ?? '',
                'email_profissional' => $post['email_corporativo'] ?? '',
                'telefone' => $post['telefone'] ?? '',
                'data_admissao' => $post['data_admissao'] ?? null,
                'tipo_contrato' => $post['tipo_contrato'] ?? 'CLT',
                'situacao' => 'ativo',
                'id_cargo' => $idCargo,
                'id_setor' => $idSetor,
                'id_endereco' => $idEndereco,
            ];

            $this->colaboradorModel->create($dadosColaborador);

            $this->pdo->commit();

            // Redireciona para atualizar a lista (ou poderia retornar JSON se mudarmos o JS de criar)
            header('Location: ' . BASE_URL . '/colaboradores?success=created');
            exit;

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            die("Erro ao salvar: " . $e->getMessage());
        }
    }

    // CORREÇÃO CRÍTICA: Retorna JSON e trata formatação de dados
    public function atualizar(): void
    {
        $this->exigirPermissaoGestor();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['erro' => true, 'msg' => 'Método inválido']);
            exit;
        }

        // Sanitização de dinheiro para evitar erro no banco decimal(10,2)
        $salarioInput = $_POST['salario'] ?? '0';
        $salarioLimpo = (float)str_replace(['R$', '.', ' ', ','], ['', '', '', '.'], $salarioInput);

        // Mapeamento correto dos dados vindos do Modal
        $dados = [
            'id_colaborador' => (int)($_POST['id_colaborador'] ?? 0),
            'nome_completo' => $_POST['nome_completo'] ?? '', // Atenção: JS envia 'nome_completo'
            'data_nascimento' => $_POST['data_nascimento'] ?? null,
            'genero' => $_POST['genero'] ?? null,
            'email_pessoal' => $_POST['email_pessoal'] ?? '',
            'email_profissional' => $_POST['email_corporativo'] ?? '',
            'telefone' => $_POST['telefone'] ?? '',
            'situacao' => $_POST['situacao'] ?? 'ativo',
            'data_admissao' => $_POST['data_admissao'] ?? null,
            'tipo_contrato' => $_POST['tipo_contrato'] ?? 'CLT',

            'endereco' => [
                'CEP' => $_POST['CEP'] ?? '',
                'logradouro' => $_POST['logradouro'] ?? '',
                'numero' => $_POST['numero'] ?? '',
                'bairro' => $_POST['bairro'] ?? '',
                'cidade' => $_POST['cidade'] ?? '',
                'estado' => $_POST['estado'] ?? '',
            ],
            'cargo' => [
                'nome_cargo' => $_POST['cargo'] ?? '',
                'salario_base' => $salarioLimpo,
            ],
            'setor' => [
                'nome_setor' => $_POST['departamento'] ?? $_POST['setor'] ?? '',
            ],
        ];

        if ($dados['id_colaborador'] === 0) {
            // Se o Javascript enviou via POST normal (fallback), redireciona.
            // Se foi AJAX, deveria tratar JSON. Vamos assumir fallback para HTML form submit
            header('Location: ' . BASE_URL . '/colaboradores?error=invalid_id');
            exit;
        }

        $sucesso = $this->colaboradorModel->atualizarColaborador($dados);

        if ($sucesso) {
            // Sucesso
            header('Location: ' . BASE_URL . '/colaboradores?success=updated');
        } else {
            // Falha
            header('Location: ' . BASE_URL . '/colaboradores?error=update_failed');
        }
        exit;
    }

    // CORREÇÃO CRÍTICA: Retorna JSON para o AJAX não recarregar a página
    public function toggleStatus(): void
    {
        $this->exigirPermissaoGestor();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['erro' => true, 'msg' => 'Método inválido']);
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);

        if ($id === 0) {
            echo json_encode(['erro' => true, 'msg' => 'ID inválido']);
            exit;
        }

        try {
            $sucesso = $this->colaboradorModel->toggleStatus($id);

            if ($sucesso) {
                echo json_encode(['erro' => false, 'msg' => 'Status alterado com sucesso']);
            } else {
                echo json_encode(['erro' => true, 'msg' => 'Erro ao atualizar no banco de dados']);
            }
        } catch (\Exception $e) {
            echo json_encode(['erro' => true, 'msg' => 'Erro interno: ' . $e->getMessage()]);
        }
        exit;
    }

    // Método auxiliar necessário para a view funcionar se você a chamar diretamente
    public function novo(): void
    {
        $this->exigirPermissaoGestor();
        $this->view('Colaborador/tabelaColaborador', ['colaboradores' => []]);
    }
    public function buscarAjax(): void
    {
        header('Content-Type: application/json');
        $termo = $_POST['termo'] ?? '';

        if (strlen($termo) < 3) {
            echo json_encode(['success' => false, 'data' => []]); exit;
        }

        // Usando SQL direto aqui ou criando um método no Model (recomendado)
        // Vou colocar a lógica aqui para facilitar, mas o ideal é mover para o Model
        $sql = "SELECT id_colaborador, nome_completo, matricula 
                FROM colaborador 
                WHERE (nome_completo LIKE :t OR matricula LIKE :t) 
                AND situacao = 'ativo' LIMIT 5";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':t' => "%$termo%"]);
        $resultados = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'data' => $resultados]);
        exit;
    }
}