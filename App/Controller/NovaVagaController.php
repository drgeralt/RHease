<?php 

namespace App\Controller;

use App\Core\Controller;

class NovaVagaController extends Controller
{
    public function criar()
    {
        $this->view('vaga/novaVaga', [/*'setores' => $setores*/]);
    }
    public function showForm()
    {
        // Renderiza a view do formulário de nova vaga
        $this->view('vaga/novaVaga');
    }

    public function salvar()
    {
        // 1. Verificar se os dados foram enviados via POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // 2. Coletar e filtrar os dados do formulário
            $dados = [
                'titulo_vaga' => filter_input(INPUT_POST, 'titulo', FILTER_SANITIZE_STRING),
                'id_setor' => 1, // PONTO IMPORTANTE: Veja a nota abaixo
                'situacao' => filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING),
                'requisitos' => filter_input(INPUT_POST, 'skills_necessarias', FILTER_SANITIZE_STRING)
                // Adicione os outros campos do seu formulário aqui...
            ];

            // 3. (Opcional, mas recomendado) Validar os dados.
            // Se houver erros de validação, renderize o formulário novamente com as mensagens de erro.

            // 4. Instanciar o Model
            $vagaModel = $this->model('GestaoVagas');

            // 5. Chamar o método do Model para criar a vaga
            $novaVagaId = $vagaModel->criarVaga($dados);

            // 6. Redirecionar o usuário (Padrão Post-Redirect-Get)
            if ($novaVagaId) {
                // Sucesso: redireciona para a lista de vagas
                header('Location: /RHease/public/gestaoVagas/listarVagas');
                exit;
            } else {
                // Erro: redireciona de volta para o formulário com uma mensagem de erro
                // (Em um sistema mais avançado, você usaria sessões para passar a mensagem)
                header('Location: /RHease/public/gestaoVagas/criar');
                exit;
            }

        } else {
            // Se não for POST, redireciona para a página inicial ou de criação
            header('Location: /RHease/public/gestaoVagas/criar');
            exit;
        }
    }
}