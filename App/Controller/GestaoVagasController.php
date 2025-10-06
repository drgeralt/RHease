<?php 
declare(strict_types=1);

namespace App\Controller;

use App\Core\Controller;
use PDOException;

class GestaoVagasController extends Controller
{
    // lista as vagas e exibe a view
    public function listarVagas(): void 
    {
        $vagaModel = $this->model('GestaoVagas');
        $vagas = $vagaModel->listarVagas();
        $this->view('vaga/GestaoVagas', ['vagas' => $vagas]);
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
                'situacao' => $status,
                'requisitos' => $_POST['skills_necessarias'] ?? null,
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