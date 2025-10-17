<?php 
declare(strict_types=1);

namespace App\Controller;

use App\Core\Controller;
use App\Core\Database;
use App\Model\GestaoVagasModel;
use App\Model\CandidaturaModel;
use PDOException;

class GestaoVagasController extends Controller
{
    // lista as vagas e exibe a view
    public function listarVagas(): void 
    {
        $db = Database::getInstance();
        $vagaModel = $this->model('GestaoVagas');
        $vagas = $vagaModel->listarVagas();
        $this->view('vaga/GestaoVagas', ['vagas' => $vagas]);
    }

    public function verCandidatos()
    {
        // le o id da vaga a partir de $_POST, que é enviado pelo formulário
        $idVaga = (int)($_POST['id'] ?? 0);

        if ($idVaga === 0) {
            header('Location: ' . BASE_URL . '/vagas/listar');
            exit;
        }

        $vagaModel = $this->model('GestaoVagas');
        $candidaturaModel = $this->model('Candidatura');

        $vaga = $vagaModel->buscarPorId($idVaga);
        $candidatos = $candidaturaModel->buscarPorVaga($idVaga);

        if (!$vaga) {
            header('Location: ' . BASE_URL . '/vagas/listar?error=not_found');
            exit;
        }

        return $this->view('Candidatura/lista_candidatos', [
            'vaga' => $vaga,
            'candidatos' => $candidatos
        ]);
    }

    // exibe o formulário de criação
    public function criar(): void
    {
        $cargoModel = $this->model('Cargo');
        $cargosDisponiveis = $cargoModel->findAll();
        $this->view('vaga/NovaVaga', ['cargos' => $cargosDisponiveis]);
    }


public function salvar(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        header('Location: ' . BASE_URL . '/vagas/criar');
        exit;
    }

    $erros = []; // Array para guardar as mensagens de erro

    // --- Início da Validação Detalhada ---
    $titulo = trim($_POST['titulo'] ?? '');
    if (empty($titulo)) {
        $erros['titulo'] = 'O título da vaga é obrigatório.';
    } elseif (strlen($titulo) > 100) {
        $erros['titulo'] = 'O título não pode ter mais de 100 caracteres.';
    }

    $departamento = trim($_POST['departamento'] ?? '');
    if (empty($departamento)) {
        $erros['departamento'] = 'O departamento é obrigatório.';
    }

    $descricao = trim($_POST['descricao'] ?? '');
    if (empty($descricao)) {
        $erros['descricao'] = 'A descrição da vaga é obrigatória.';
    }

    $status = $_POST['status'] ?? '';
    if (!in_array($status, ['aberta', 'rascunho', 'fechada'])) {
        $erros['status'] = 'Selecione um status válido.';
    }
    // --- Fim da Validação ---

    // Se existirem erros, redireciona de volta para o formulário
    if (!empty($erros)) {
        $_SESSION['erros_vaga'] = $erros;
        $_SESSION['old_data'] = $_POST; // Guarda os dados antigos para preencher o form
        header('Location: ' . BASE_URL . '/vagas/criar');
        exit;
    }

    // Se a validação passou, continua com o processo de salvar
    try {
        $setorModel = $this->model('Setor');
        $idSetor = $setorModel->findOrCreateByName($departamento);

        $dadosVaga = [
            'titulo_vaga' => $titulo,
            'id_setor' => $idSetor,
            'situacao' => $status,
            'descricao_vaga' => $descricao,
            'requisitos_necessarios' => $_POST['skills_necessarias'] ?? null,
            'requisitos_recomendados' => $_POST['skills_recomendadas'] ?? null,
            'requisitos_desejados' => $_POST['skills_desejadas'] ?? null,
            'id_cargo' => null,
        ];

        $vagaModel = $this->model('NovaVaga');
        $vagaModel->criarVaga($dadosVaga);

        header('Location: ' . BASE_URL . '/vagas/listar');
        exit;

    } catch (PDOException $e) {
        error_log('Erro ao salvar vaga: ' . $e->getMessage());
        $this->view('Common/error', ['message' => 'Erro ao salvar a vaga.']);
    }
}

    public function editar(): void
    {
        $idVaga = (int)($_GET['id'] ?? 0);

        if ($idVaga === 0) {
            header('Location: ' . BASE_URL . '/vagas/listar');
            exit;
        }

        $vagaModel = $this->model('GestaoVagas');
        $vaga = $vagaModel->buscarPorId($idVaga);

        if (!$vaga) {
            // Vaga não encontrada, redireciona para a lista
            header('Location: ' . BASE_URL . '/vagas/listar');
            exit;
        }

        $this->view('vaga/editarVaga', ['vaga' => $vaga]);
    }

    //Processa os dados do formulário de edição e atualiza no banco.
    
    public function atualizar(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            header('Location: ' . BASE_URL . '/vagas/listar');
            exit;
        }

        $idVaga = (int)($_POST['id_vaga'] ?? 0);
        $titulo = trim($_POST['titulo'] ?? '');
        $departamento = trim($_POST['departamento'] ?? '');

        if ($idVaga === 0 || $titulo === '' || $departamento === '') {
            header('Location: ' . BASE_URL . '/vagas/editar?id=' . $idVaga . '&error=invalid_data');
            exit;
        }

        $setorModel = $this->model('Setor');
        $idSetor = $setorModel->findOrCreateByName($departamento);

        // CORRIGIDO: O array agora inclui explicitamente os campos de skills
        // que vêm do formulário que acabamos de criar.
        $dadosVaga = [
            'titulo_vaga' => $titulo,
            'id_setor' => $idSetor,
            'situacao' => $_POST['status'] ?? 'rascunho',
            'descricao_vaga' => $_POST['descricao'] ?? null,
            'requisitos_necessarios' => $_POST['skills_necessarias'] ?? null,
            'requisitos_recomendados' => $_POST['skills_recomendadas'] ?? null,
            'requisitos_desejados' => $_POST['skills_desejadas'] ?? null,
        ];

        $vagaModel = $this->model('GestaoVagas');
        
        // Passamos o ID da vaga e o array completo de dados para o model
        $vagaModel->atualizarVaga($idVaga, $dadosVaga);

        header('Location: ' . BASE_URL . '/vagas/listar');
        exit;
    }


    public function excluir(): void
    {
        $idVaga = (int)($_GET['id'] ?? 0);

        if ($idVaga > 0) {
            $vagaModel = $this->model('GestaoVagas');
            $vagaModel->excluirVaga($idVaga);
        }

        header('Location: ' . BASE_URL . '/vagas/listar');
        exit;
    }
}