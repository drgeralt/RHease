<?php
declare(strict_types=1);

namespace App\Controller;

use App\Core\Controller;
use App\Model\GestaoVagasModel;
use App\Model\CandidaturaModel;
use App\Model\SetorModel;
use App\Model\NovaVagaModel; // Assumindo que este Model existe
use PDO;
use PDOException;

class VagaApiController extends Controller
{
    // Armazena as dependências injetadas
    private GestaoVagasModel $gestaoVagasModel;
    private CandidaturaModel $candidaturaModel;
    private SetorModel $setorModel;
    private NovaVagaModel $novaVagaModel;

    // O Construtor agora recebe todos os Models necessários
    public function __construct(
        GestaoVagasModel $gestaoVagasModel,
        CandidaturaModel $candidaturaModel,
        SetorModel $setorModel,
        NovaVagaModel $novaVagaModel,
        PDO $pdo
    ) {
        parent::__construct($pdo); // Passa o PDO para o Core\Controller
        $this->gestaoVagasModel = $gestaoVagasModel;
        $this->candidaturaModel = $candidaturaModel;
        $this->setorModel = $setorModel;
        $this->novaVagaModel = $novaVagaModel;
    }

    // [API] GET /api/vagas/listar
    public function listarVagas(): void
    {
        try {
            // Usa o Model injetado
            $vagas = $this->gestaoVagasModel->listarVagas();
            $this->jsonResponse(true, 'Vagas carregadas com sucesso', $vagas);

        } catch (\Exception $e) {
            $this->jsonResponse(false, 'Erro ao consultar o banco.', null, 500);
        }
    }

    // [API] POST /api/vagas/salvar
    public function salvar(): void
    {
        $dados = json_decode(file_get_contents('php://input'), true);

        $titulo = trim($dados['titulo'] ?? '');
        $departamento = trim($dados['departamento'] ?? '');

        if (empty($titulo) || empty($departamento)) {
            $this->jsonResponse(false, 'Título e Departamento são obrigatórios.', null, 422);
            return;
        }

        try {
            // Usa os Models injetados
            $idSetor = $this->setorModel->findOrCreateByName($departamento);

            $dadosVaga = [
                'titulo_vaga' => $titulo,
                'id_setor' => $idSetor,
                'situacao' => $dados['status'] ?? 'rascunho',
                'descricao_vaga' => $dados['descricao'] ?? null,
                'requisitos_necessarios' => $dados['skills_necessarias'] ?? null,
                'requisitos_recomendados' => $dados['skills_recomendadas'] ?? null,
                'requisitos_desejados' => $dados['skills_desejadas'] ?? null,
                'id_cargo' => null, // Você não está enviando id_cargo, então definimos como null
            ];

            $novoId = $this->novaVagaModel->criarVaga($dadosVaga);
            $this->jsonResponse(true, 'Vaga criada com sucesso', ['id_vaga' => $novoId], 201);

        } catch (\Exception $e) {
            $this->jsonResponse(false, 'Erro ao salvar a vaga no banco.', null, 500);
        }
    }

    // [API] GET /api/vagas/editar?id=...
    public function editar(): void
    {
        $idVaga = (int)($_GET['id'] ?? 0);
        if ($idVaga === 0) {
            $this->jsonResponse(false, 'ID da vaga não fornecido.', null, 400);
            return;
        }

        try {
            // Usa o Model injetado
            $vaga = $this->gestaoVagasModel->buscarPorId($idVaga);
            if (!$vaga) {
                $this->jsonResponse(false, 'Vaga não encontrada.', null, 404);
                return;
            }
            $this->jsonResponse(true, 'Vaga encontrada', $vaga);
        } catch (\Exception $e) {
            $this->jsonResponse(false, 'Erro ao consultar o banco de dados.', null, 500);
        }
    }

    // [API] POST /api/vagas/atualizar
    public function atualizar(): void
    {
        $dados = json_decode(file_get_contents('php://input'), true);

        $idVaga = (int)($dados['id_vaga'] ?? 0);
        $titulo = trim($dados['titulo'] ?? '');
        $departamento = trim($dados['departamento'] ?? '');

        if ($idVaga === 0 || $titulo === '' || $departamento === '') {
            $this->jsonResponse(false, 'Dados inválidos', null, 422);
            return;
        }

        try {
            // Usa os Models injetados
            $idSetor = $this->setorModel->findOrCreateByName($departamento);

            $dadosVaga = [
                'titulo_vaga' => $titulo,
                'id_setor' => $idSetor,
                'situacao' => $dados['status'] ?? 'rascunho',
                'descricao_vaga' => $dados['descricao'] ?? null,
                'requisitos_necessarios' => $dados['skills_necessarias'] ?? null,
                'requisitos_recomendados' => $dados['skills_recomendadas'] ?? null,
                'requisitos_desejados' => $dados['skills_desejadas'] ?? null,
            ];

            $this->gestaoVagasModel->atualizarVaga($idVaga, $dadosVaga);
            $this->jsonResponse(true, 'Vaga atualizada com sucesso');
        } catch (\Exception $e) {
            $this->jsonResponse(false, 'Erro ao atualizar a vaga no banco.', null, 500);
        }
    }

    // [API] GET /api/vagas/excluir?id=...
    public function excluir(): void
    {
        $idVaga = (int)($_GET['id'] ?? 0);
        if ($idVaga === 0) {
            $this->jsonResponse(false, 'ID da vaga não fornecido.', null, 400);
            return;
        }
        try {
            // Usa o Model injetado
            $this->gestaoVagasModel->excluirVaga($idVaga);
            $this->jsonResponse(true, 'Vaga excluída com sucesso');
        } catch (\Exception $e) {
            $this->jsonResponse(false, 'Erro ao excluir a vaga.', null, 500);
        }
    }

    // [API] GET /api/vagas/candidatos?id=...
    public function verCandidatos()
    {
        $idVaga = (int)($_GET['id'] ?? 0);
        if ($idVaga === 0) {
            $this->jsonResponse(false, 'ID da vaga não fornecido.', null, 400);
            return;
        }

        try {
            // Usa os Models injetados
            $vaga = $this->gestaoVagasModel->buscarPorId($idVaga);
            $candidatos = $this->candidaturaModel->buscarPorVaga($idVaga);

            if (!$vaga) {
                $this->jsonResponse(false, 'Vaga não encontrada.', null, 404);
                return;
            }

            $this->jsonResponse(true, 'Candidatos encontrados', [
                'vaga' => $vaga,
                'candidatos' => $candidatos
            ]);

        } catch (\Exception $e) {
            $this->jsonResponse(false, 'Erro ao consultar o banco de dados.', null, 500);
        }
    }
}