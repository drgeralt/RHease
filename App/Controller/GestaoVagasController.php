<?php 
declare(strict_types=1);

namespace App\Controller;

use App\Core\Controller;

class GestaoVagasController extends Controller
{
    public function listarVagas(): void 
    {
        $this->view('vaga/gestaoVagas');
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
        $this->view('Candidatura/lista_candidatos');
    }
}