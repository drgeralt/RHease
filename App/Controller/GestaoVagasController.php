<?php

namespace App\Controller;

use App\Core\Controller;
use App\Model\GestaoVagasModel;
// use App\Model\CandidaturaModel; // <--- REMOVIDO
use PDO;

class GestaoVagasController extends Controller
{
    protected GestaoVagasModel $vagaModel;
    // protected CandidaturaModel $candidaturaModel; // <--- REMOVIDO

    // CONSTRUTOR ATUALIZADO: Aceita apenas 2 argumentos agora
    public function __construct(GestaoVagasModel $vagaModel, PDO $pdo)
    {
        parent::__construct($pdo);
        $this->vagaModel = $vagaModel;
        // $this->candidaturaModel = $candidaturaModel; // <--- REMOVIDO
        $this->pdo = $pdo;
    }

    public function listarVagas()
    {
        $this->exigirPermissaoGestor();
        $vagas = $this->vagaModel->listarTodas();

        return $this->view('Vaga/gestaoVagas', [
            'vagas' => $vagas
        ]);
    }
}