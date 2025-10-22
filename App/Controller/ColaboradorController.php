<?php

namespace App\Controller;

use App\Core\Controller;
use App\Core\Database;
use App\Model\CargoModel;
use App\Model\ColaboradorModel;
use App\Model\EnderecoModel;
use App\Model\SetorModel;
use PDOException;

class ColaboradorController extends Controller
{
    public function criar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/colaboradores/adicionar');
            exit;
        }

        $post = $_POST;
        $pdo = Database::getInstance();

        $pdo->beginTransaction();

        try {
            // 1. Cria ou encontra Endereço, Cargo e Setor
            $enderecoModel = new EnderecoModel($pdo);
            $idEndereco = $enderecoModel->create($post);

            $cargoModel = new CargoModel($pdo);
            $salario = (float)str_replace(['.', ','], ['', '.'], $post['salario'] ?? 0);
            $idCargo = $cargoModel->findOrCreateByName($post['cargo'], $salario);

            $setorModel = new SetorModel($pdo);
            $idSetor = $setorModel->findOrCreateByName($post['setor']);

            // 2. Monta o array de dados APENAS para a tabela 'colaborador'
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
                'situacao' => 'ativo', // Define 'ativo' como padrão
                'id_cargo' => $idCargo,
                'id_setor' => $idSetor,
                'id_endereco' => $idEndereco,
            ];

            // 3. Cria o colaborador
            $colaboradorModel = new ColaboradorModel($pdo);
            $colaboradorModel->create($dadosColaborador);

            // Se tudo deu certo, confirma as operações no banco
            $pdo->commit();

            // Redireciona para a lista de colaboradores após o sucesso
            header('Location: ' . BASE_URL . '/colaboradores');
            exit;

        } catch (PDOException $e) {
            // Se algo deu errado, desfaz tudo
            $pdo->rollBack();
            // Em um ambiente de produção, logue o erro em vez de usar 'die'
            die("Erro ao salvar colaborador: " . $e->getMessage());
        }
    }
    public function novo(): void
    {
        $this->view('Colaborador/cadastroColaborador');
    }
    public function listar()
    {
        // 1. Obtém a conexão com a base de dados
        $db = Database::getInstance();

        // 2. Instancia o model, passando a conexão
        $colaboradorModel = new ColaboradorModel($db);

        // 3. Usa o model para buscar a lista de todos os colaboradores
        $todosOsColaboradores = $colaboradorModel->listarColaboradores();

        // 4. CORREÇÃO: Envia os dados para a view.
        // A chave 'colaboradores' torna-se a variável $colaboradores na sua view.
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

        $db = Database::getInstance();
        $colaboradorModel = new ColaboradorModel($db);
        $dadosCompletos = $colaboradorModel->buscarPorId($id);

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

        // Coleta todos os dados do formulário em um array estruturado
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
                'CEP' => $_POST['CEP'] ?? '',
                'logradouro' => $_POST['logradouro'] ?? '',
                'numero' => $_POST['numero'] ?? '',
                'bairro' => $_POST['bairro'] ?? '',
                'cidade' => $_POST['cidade'] ?? '',
                'estado' => $_POST['estado'] ?? '',
            ],
            'cargo' => [
                'nome_cargo' => $_POST['cargo'] ?? '',
                'salario_base' => (float)str_replace(',', '.', $_POST['salario'] ?? 0),
            ],
            'setor' => [
                'nome_setor' => $_POST['departamento'] ?? '',
            ],
        ];

        if ($dados['id_colaborador'] === 0) {
            // Lidar com erro de ID inválido
            header('Location: ' . BASE_URL . '/colaboradores?error=invalid_id');
            exit;
        }

        $db = Database::getInstance();
        $colaboradorModel = new ColaboradorModel($db);

        $sucesso = $colaboradorModel->atualizarColaborador($dados);

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
            // Redireciona com erro se o ID for inválido
            header('Location: ' . BASE_URL . '/colaboradores?error=invalid_id');
            exit;
        }

        $db = Database::getInstance();
        $colaboradorModel = new ColaboradorModel($db);

        // Invoca o método no model para alterar o status
        $sucesso = $colaboradorModel->toggleStatus($id);

        if (!$sucesso) {
            // Redireciona com erro se a atualização falhar
            header('Location: ' . BASE_URL . '/colaboradores?error=toggle_failed');
            exit;
        }

        // Redireciona de volta para a lista após o sucesso
        header('Location: ' . BASE_URL . '/colaboradores');
        exit;
    }
}