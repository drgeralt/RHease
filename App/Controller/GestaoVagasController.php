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
        // CORREÇÃO: Lê o ID da vaga a partir de $_POST, que é enviado pelo formulário.
        $idVaga = (int)($_POST['id'] ?? 0);

        if ($idVaga === 0) {
            header('Location: ' . BASE_URL . '/vagas/listar');
            exit;
        }

        $db = Database::getInstance();
        $vagaModel = new GestaoVagasModel($db);
        $candidaturaModel = new CandidaturaModel($db);

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

    //método para salvar uma nova vaga enviada via POST
    public function salvar(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            header('Location: ' . BASE_URL . '/vagas/criar');
            exit;
        }

         // Validação mínima
        $titulo = trim($_POST['titulo'] ?? '');
        $departamento = trim($_POST['departamento'] ?? '');
        $status = trim($_POST['status'] ?? 'aberta');

        if ($titulo === '' || $departamento === '') {
            header('Location: ' . BASE_URL . '/vagas/criar');
            exit;
        }

        try {
            $setorModel = $this->model('Setor');
            $idSetor = $setorModel->findOrCreateByName($departamento);

            $dadosVaga = [
                'titulo_vaga' => $titulo,
                'id_setor' => $idSetor,
                'situacao' => $_POST['status'] ?? 'rascunho',
                'descricao_vaga' => $_POST['descricao'] ?? null,
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
            // Log e redireciona para página de erro
            error_log('Erro ao salvar vaga: ' . $e->getMessage());
            $this->view('Common/error', ['message' => 'Erro ao salvar a vaga.']);
        }
    }
}