<?php 

namespace App\Controller;

use App\Core\Controller;
use App\Core\Database;
use App\Model\SetorModel;
use App\Model\NovaVagaModel;
use PDOException;

class NovaVagaController extends Controller
{
    public function criar(): void
    {
        // Apenas renderiza a view que contém o seu novo formulário HTML.
        // O nome do arquivo pode ser, por exemplo, 'criarVagaForm.php'
        $this->view('vaga/criarVagaForm');
    }

    /**
     * Ação para PROCESSAR os dados do formulário e salvar a vaga.
     * Acessível via POST: /public/gestaoVagas/salvar
     */
    public function salvar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /RHease/public/gestaoVagas/criar');
            exit;
        }

        $post = $_POST;
        $pdo = Database::getInstance();

        $pdo->beginTransaction();

        try {
            // 1. Encontra ou cria o Setor (Departamento) e pega o ID
            $setorModel = new SetorModel($pdo);
            $idSetor = $setorModel->findOrCreateByName($post['departamento']);

            // 2. Prepara o array de dados para a vaga com os campos do novo formulário
            $dadosVaga = [
                'titulo_vaga' => $post['titulo_vaga'],
                'departamento' => $post['departamento'],
                'descricao' => $post['descricao'],
                'status_vaga' => $post['status_vaga'],
                'skills_necessarias' => $post['skills_necessarias'],
                'skills_recomendadas' => $post['skills_recomendadas'],
                'skills_desejadas' => $post['skills_desejadas'],
                'id_setor' => $idSetor // Adiciona o ID do setor que encontramos/criamos
            ];

            // 3. Cria a Vaga usando o model apropriado
            $vagaModel = new NovaVagaModel($pdo);
            $vagaModel->criarVaga($dadosVaga);

            // 4. Se tudo deu certo, confirma as operações no banco
            $pdo->commit();

            // 5. Redireciona para a página de gestão de vagas (ou outra página de sucesso)
            header('Location: /RHease/public/gestaoVagas/listarVagas'); // Mude se o nome do método for outro
            exit;

        } catch (PDOException $e) {
            // 6. Se qualquer passo deu erro, desfaz todas as operações
            $pdo->rollBack();
            // Para produção, logue o erro. Para desenvolvimento, pode exibir a mensagem.
            die("Erro ao salvar a vaga: " . $e->getMessage());
        }
    }
}