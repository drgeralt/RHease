<?php
declare(strict_types=1);

namespace App\Controller;

use App\Core\Controller;
use App\Model\GestaoVagasModel;
use App\Model\CandidaturaModel;
use PDO;

class GestaoVagasController extends Controller
{
    protected GestaoVagasModel $vagasModel;
    protected CandidaturaModel $candidaturaModel;

    public function __construct(
        GestaoVagasModel $vagasModel,
        CandidaturaModel $candidaturaModel,
        PDO $pdo
    ) {
        parent::__construct($pdo);
        $this->vagasModel = $vagasModel;
        $this->candidaturaModel = $candidaturaModel;
    }

    public function listarVagas(): void
    {
        $vagas = $this->vagasModel->listarVagas();
        $this->view('vaga/gestaoVagas', ['vagas' => $vagas]);
    }

    public function criar(): void
    {
        $this->view('vaga/novaVaga');
    }

    public function editar(): void
    {
        $this->view('vaga/editarVaga');
    }

    public function verCandidatos()
    {
        $idVaga = (int)($_GET['id'] ?? 0);

        $vaga = $this->vagasModel->buscarPorId($idVaga);
        $candidatos = $this->candidaturaModel->buscarPorVaga($idVaga);

        $this->view('Candidatura/lista_candidatos', [
            'vaga' => $vaga,
            'candidatos' => $candidatos
        ]);
    }
}