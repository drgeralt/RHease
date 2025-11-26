<?php

namespace App\Controller;

use App\Core\Controller;
use App\Model\ColaboradorModel;
use PDO;

class HomeController extends Controller
{
    protected ColaboradorModel $colaboradorModel;

    public function __construct(ColaboradorModel $colaboradorModel, PDO $pdo)
    {
        parent::__construct($pdo);
        $this->colaboradorModel = $colaboradorModel;
    }

    public function show_index(): void
    {
        // CORRECT: Uses the injected instance
        $colaboradores = $this->colaboradorModel->getAll();

        $this->view('Colaborador/tabelaColaborador', ['colaboradores' => $colaboradores]);
    }
}