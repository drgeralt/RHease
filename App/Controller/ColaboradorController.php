<?php

namespace App\Controller;

use App\Core\Controller;
use App\Core\Database;
use App\Model\CargoModel;
use App\Model\ColaboradorModel;
use App\Model\EnderecoModel;
use App\Model\SetorModel;
use PDOException;

class ColaboradorController extends Controller
{
    public function criar(): void
    {
        $post = $_POST;
        $pdo = Database::getInstance();

        $pdo->beginTransaction();

        try {
            $enderecoModel = new EnderecoModel($pdo);
            $idEndereco = $enderecoModel->create($post);

            $cargoModel = new CargoModel($pdo);
            $idCargo = $cargoModel->findOrCreateByName($post['cargo']);

            $setorModel = new SetorModel($pdo);
            $idSetor = $setorModel->findOrCreateByName($post['departamento']);

            $dadosColaborador = [
                'matricula' => $post['matricula'],
                'nome_completo' => $post['nome'],
                'data_nascimento' => $post['data_nascimento'],
                'CPF' => $post['cpf'],
                'RG' => $post['rg'],
                'genero' => $post['genero'],
                'email' => $post['email'],
                'telefone' => $post['telefone'],
                'data_admissao' => $post['data_admissao'],
                'situacao' => 'ativo',
                'id_cargo' => $idCargo,
                'id_setor' => $idSetor,
                'id_endereco' => $idEndereco
            ];

            $colaboradorModel = new ColaboradorModel($pdo);
            $colaboradorModel->create($dadosColaborador);

            //Se tudo deu certo, confirma as operações no banco
            $pdo->commit();

            header('Location: ' . BASE_URL);
            exit;

        } catch (PDOException $e) {
            //Deu erro, fudeu, abortar
            $pdo->rollBack();
            die("Erro ao salvar colaborador: " . $e->getMessage());
        }
    }
    public function novo(): void
    {
        $this->view('Colaborador/cadastroColaborador');
    }
}