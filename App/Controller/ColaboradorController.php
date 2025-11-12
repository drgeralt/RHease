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
    protected PDO $pdo;

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

    public function novo(): void
    {
        $this->view('Colaborador/cadastroColaborador');
    }

    public function criar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/colaboradores/adicionar');
            exit;
        }

        $post = $_POST;
        $this->pdo->beginTransaction();

        try {
            $idEndereco = $this->enderecoModel->create($post);

            $salario = (float)str_replace(['.', ','], ['', '.'], $post['salario'] ?? 0);
            $idCargo = $this->cargoModel->findOrCreateByName($post['cargo'], $salario);

            $idSetor = $this->setorModel->findOrCreateByName($post['setor']);

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
            header('Location: ' . BASE_URL . '/colaboradores');
            exit;

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            die("Erro ao salvar colaborador: " . $e->getMessage());
        }
    }

    public function listar()
    {
        $todosOsColaboradores = $this->colaboradorModel->listarColaboradores();

        return $this->view('Colaborador/tabelaColaborador', [
            'colaboradores' => $todosOsColaboradores
        ]);
    }

    public function editar()
    {
        $id = (int)($_POST['id'] ?? 0);

        if ($id === 0) {
            header('Location: ' . BASE_URL . '/colaboradores');
            exit;
        }

        $dadosCompletos = $this->colaboradorModel->buscarPorId($id);

        if (!$dadosCompletos) {
            header('Location: ' . BASE_URL . '/colaboradores');
            exit;
        }

        return $this->view('Colaborador/editarColaborador', $dadosCompletos);
    }

    public function atualizar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/colaboradores');
            exit;
        }

        $dados = [
            'id_colaborador' => (int)($_POST['id_colaborador'] ?? 0),
            'nome_completo' => $_POST['nome'] ?? '',
            'data_nascimento' => $_POST['data_nascimento'] ?? null,
            'genero' => $_POST['genero'] ?? null,
            'email_pessoal' => $_POST['email'] ?? '',
            'email_profissional' => $_POST['email_corporativo'] ?? '',
            'telefone' => $_POST['telefone'] ?? '',
            'situacao' => $_POST['situacao'] ?? 'ativo',
            'data_admissao' => $_POST['data_admissao'] ?? null,
            'tipo_contrato' => $_POST['tipo_contrato'] ?? 'CLT',

            'endereco' => [
                'CEP' => isset($_POST['CEP']) ? $_POST['CEP'] : '',
                'logradouro' => isset($_POST['logradouro']) ? $_POST['logradouro'] : '',
                'numero' => isset($_POST['numero']) ? $_POST['numero'] : '',
                'bairro' => isset($_POST['bairro']) ? $_POST['bairro'] : '',
                'cidade' => isset($_POST['cidade']) ? $_POST['cidade'] : '',
                'estado' => isset($_POST['estado']) ? $_POST['estado'] : '',
            ],
            'cargo' => [
                'nome_cargo' => isset($_POST['cargo']) ? $_POST['cargo'] : '',
                'salario_base' => (float)str_replace(',', '.', isset($_POST['salario']) ? $_POST['salario'] : 0),
            ],
            'setor' => [
                'nome_setor' => isset($_POST['departamento']) ? $_POST['departamento'] : '',
            ],
        ];

        if ($dados['id_colaborador'] === 0) {
            header('Location: ' . BASE_URL . '/colaboradores?error=invalid_id');
            exit;
        }

        $sucesso = $this->colaboradorModel->atualizarColaborador($dados);

        if ($sucesso) {
            header('Location: ' . BASE_URL . '/colaboradores?success=updated');
        } else {
            header('Location: ' . BASE_URL . '/colaboradores?error=update_failed');
        }
        exit;
    }

    public function toggleStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/colaboradores');
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id === 0) {
            header('Location: ' . BASE_URL . '/colaboradores?error=invalid_id');
            exit;
        }

        $sucesso = $this->colaboradorModel->toggleStatus($id);

        if (!$sucesso) {
            // CORREÇÃO AQUI: O caminho estava errado.
            header('Location: ' . BASE_URL . '/colaboradores?error=toggle_failed');
            exit;
        }

        header('Location: ' . BASE_URL . '/colaboradores');
        exit;
    }
}