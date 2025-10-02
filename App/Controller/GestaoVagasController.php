<?php 

namespace App\Controller;

use App\Core\Controller;



class GestaoVagasController extends Controller{
    public function listarVagas(){

        //instaciando o model de vagas
        $vagaModel = $this->model('GestaoVagas');

        // buscar as vagas no banco
        $vagas = $vagaModel->listarVagas();

        //renderizar a view passando as vagas
        $this->view('vaga/gestaoVagas', ['vagas' => $vagas]);
    }

     public function criar(): void
    {
        // A única responsabilidade deste método é carregar a view do formulário.
        $this->view('vaga/novaVaga');
    }
}