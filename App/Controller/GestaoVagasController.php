<?php 

namespace App\Controller;

use App\Core\Controller;
use PDOException;

class GestaoVagasController extends Controller
{
    //criando método para listar vagas
    public function listarVagas()
    {
        $vagaModel = $this->model('GestaoVagas');
        $vagas = $vagaModel->listarVagas();
        $this->view('vaga/GestaoVagas', ['vagas' => $vagas]);
    }

    public function criar(): void
    {
        $this->view('vaga/NovaVaga');
    }
    //método para salvar a nova vaga no banco
    public function salvar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/GestaoVagas/criar');
            exit;
        }

        try {

            $setorModel = $this->model('Setor');
            $idSetor = $setorModel->findOrCreateByName($_POST['departamento']);

            // Os dados do forms
            $dadosVaga = [
                'titulo_vaga' => $_POST['titulo'],
                'id_setor' => $idSetor,
                'situacao' => $_POST['status'],
                'requisitos' => $_POST['skills_necessarias'],
                'id_cargo' => null, // tem q mudar isso depois
                //'descricao' => $_POST['descricao'],
                //'skills_recomendadas' => $_POST['skills_recomendadas'],
                //'skills_desejadas' => $_POST['skills_desejadas'],
                
            ];
            // Salva a vaga
            $vagaModel = $this->model('NovaVaga');
            $vagaModel->criarVaga($dadosVaga);

            // Redireciona para a listagem
            header('Location: ' . BASE_URL . '/GestaoVagas/listarVagas');
            exit;

        } catch (PDOException $e) {
            die("Erro ao salvar a vaga: " . $e->getMessage());
        }
    }
}