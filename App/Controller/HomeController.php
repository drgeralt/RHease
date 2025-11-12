<?php

namespace App\Controller;

use App\Core\Controller;
use App\Core\Database;
use App\Model\ColaboradorModel;
use PDO;

class HomeController extends Controller{

    public function __construct(ColaboradorModel $colaboradorModel, PDO $pdo)
    {
        parent::__construct($pdo);
        $this->colaboradorModel = $colaboradorModel;
    }

    public function show_index(): void
    {
        $pdo = Database::getInstance();

        $colaboradorModel = new ColaboradorModel($pdo);

        $colaboradores = $colaboradorModel->getAll();

        $this->view('Colaborador/tabelaColaborador', ['colaboradores' => $colaboradores]);;
    }


}