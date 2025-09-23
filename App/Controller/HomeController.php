<?php

namespace App\Controller;

use App\Core\Controller;
use App\Core\Database;
use App\Model\ColaboradorModel;

class HomeController extends Controller{
    //temporario
    public function show_index(): void
    {
        $pdo = Database::getInstance();

        $colaboradorModel = new ColaboradorModel($pdo);

        $colaboradores = $colaboradorModel->getAll();

        $this->view('Colaborador/tabelaColaborador', ['colaboradores' => $colaboradores]);;
    }


}