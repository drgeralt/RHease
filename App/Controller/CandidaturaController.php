<?php
declare(strict_types=1);

namespace App\Controller;

use App\Core\Controller;
use App\Core\Database;
use App\Model\CandidaturaModel;
use App\Model\GestaoVagasModel;
use App\Model\CandidatoModel;
use App\Service\Implementations\AnalisadorCurriculoService;
use Smalot\PdfParser\Parser;
// Adicionado para a nova função
use GeminiAPI\Client;


class CandidaturaController extends Controller
{
    public function analisarCurriculo()
    {
        $idCandidatura = (int)($_POST['id_candidatura'] ?? 0);
        if ($idCandidatura === 0) {
            die("Erro: ID da candidatura não fornecido.");
        }

        $db = Database::getInstance();
        $candidaturaModel = new CandidaturaModel($db);

        // Busca dados, incluindo a pontuação e sumário já existentes
        $analise = $candidaturaModel->buscarAnaliseCompleta($idCandidatura);

        // 1. VERIFICAÇÃO: Se já tem pontuação, exibe a view.
        if ($analise && $analise['pontuacao_aderencia'] !== null) {
            return $this->view('Candidatura/resultado_ia', [
                'candidatura' => $analise
            ]);
        }

        // 2. PROCESSAMENTO: Se não tem pontuação, chama a função de processar.
        $resultado = $this->processarCurriculoIA($idCandidatura);

        if ($resultado['sucesso']) {
            // Redireciona para a rota de exibição (que fará a busca no banco)
            header('Location: ' . BASE_URL . '/candidatura/ver-analise?id=' . $idCandidatura);
            exit;
        } else {
            die("Erro no processamento da IA: " . $resultado['erro']);
        }
    }
    private function processarCurriculoIA(int $idCandidatura): array
    {
        $db = Database::getInstance();
        $candidaturaModel = new CandidaturaModel($db);

        $candidatura = $candidaturaModel->buscarComVaga($idCandidatura);

        if (!$candidatura || empty($candidatura['curriculo']) || empty($candidatura['descricao_vaga'])) {
            return ['sucesso' => false, 'erro' => 'Dados da candidatura ou vaga incompletos.'];
        }

        $descricaoVaga = $candidatura['descricao_vaga'];
        $caminhoRelativo = $candidatura['curriculo'];
        $basePath = realpath(__DIR__ . '/../../public');
        $caminhoCompleto = $basePath . $caminhoRelativo;

        if (!file_exists($caminhoCompleto)) {
            return ['sucesso' => false, 'erro' => 'Ficheiro do currículo não encontrado.'];
        }

        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($caminhoCompleto);
            $textoDoCurriculo = $pdf->getText();

            $apiKey = $_ENV['GEMINI_API_KEY'];
            $analisadorService = new AnalisadorCurriculoService($apiKey);
            $resultadoAnalise = $analisadorService->analisar($textoDoCurriculo, $candidatura);

            if ($resultadoAnalise['sucesso']) {
                $sumario = $resultadoAnalise['resultado']['sumario'] ?? 'Sumário não retornado.';
                $nota = (int)($resultadoAnalise['resultado']['nota'] ?? 0);

                $sucessoUpdate = $candidaturaModel->atualizarResultadoIA($idCandidatura, $nota, $sumario);

                if (!$sucessoUpdate) {
                    return ['sucesso' => false, 'erro' => 'Falha ao salvar os resultados da IA no banco de dados.'];
                }
                return ['sucesso' => true];

            } else {
                return ['sucesso' => false, 'erro' => 'Erro na análise de IA: ' . $resultadoAnalise['erro']];
            }

        } catch (\Exception $e) {
            return ['sucesso' => false, 'erro' => "Ocorreu um erro: " . $e->getMessage()];
        }
    }
    public function exibirAnaliseIA()
    {
        $idCandidatura = 0;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vem do clique no botão "Ver Análise" (olho)
            $idCandidatura = (int)($_POST['id_candidatura'] ?? 0);
        } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // Vem do redirecionamento após o processamento da IA
            $idCandidatura = (int)($_GET['id'] ?? 0);
        }

        if ($idCandidatura === 0) {
            header('Location: ' . BASE_URL . '/vagas/listar');
            exit;
        }

        $candidaturaModel = $this->model('Candidatura');
        $analise = $candidaturaModel->buscarAnaliseCompleta($idCandidatura);

        if (!$analise || $analise['pontuacao_aderencia'] === null) {
            header('Location: ' . BASE_URL . '/vagas/listar?error=analise_nao_encontrada');
            exit;
        }

        return $this->view('Candidatura/resultado_ia', ['candidatura' => $analise]);
    }
    /**
     * Exibe a lista de vagas abertas para os candidatos.
     */
    public function listar()
    {
        $db = Database::getInstance();
        $vagaModel = new GestaoVagasModel($db);

        // Esta função agora depende que o GestaoVagasModel tenha um método
        // para buscar vagas formatadas para os candidatos.
        $vagasAbertas = $vagaModel->listarVagasAbertas();

        return $this->view('Candidatura/lista_vagas', ['vagas' => $vagasAbertas]);
    }

    /**
     * Exibe o formulário de candidatura para uma vaga específica.
     */
    public function exibirFormulario()
    {
        $idVaga = (int)($_POST['id'] ?? 0);

        if ($idVaga === 0) {
            header('Location: ' . BASE_URL . '/vagas');
            exit;
        }

        $db = Database::getInstance();
        $vagaModel = new GestaoVagasModel($db);
        $vaga = $vagaModel->buscarPorId($idVaga);

        if (!$vaga) {
            header('Location: ' . BASE_URL . '/vagas?error=not_found');
            exit;
        }

        return $this->view('Candidatura/formulario_candidatura', ['vaga' => $vaga]);
    }

    /**
     * Processa o formulário de candidatura, faz o upload do currículo
     * e salva os dados no banco de dados.
     */
    public function aplicar()
    {
        // 1. Capturar e validar os dados do formulário
        $idVaga = (int)($_POST['id_vaga'] ?? 0);
        $dadosCandidato = [
            'nome_completo' => $_POST['nome_completo'] ?? null,
            'cpf' => $_POST['cpf'] ?? null,
        ];
        $arquivoCurriculo = $_FILES['curriculo_pdf'] ?? null;

        if (!$idVaga || empty($dadosCandidato['nome_completo']) || empty($dadosCandidato['cpf']) || !$arquivoCurriculo || $arquivoCurriculo['error'] !== UPLOAD_ERR_OK) {
            header('Location: ' . BASE_URL . '/vagas?error=dados_invalidos');
            exit;
        }

        // 2. Processar o upload do currículo
        $uploadDir = __DIR__ . '/../../public/uploads/curriculos/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $nomeArquivo = uniqid() . '-' . basename($arquivoCurriculo['name']);
        $caminhoCompleto = $uploadDir . $nomeArquivo;
        $caminhoRelativoParaBD = '/uploads/curriculos/' . $nomeArquivo;

        if (!move_uploaded_file($arquivoCurriculo['tmp_name'], $caminhoCompleto)) {
            header('Location: ' . BASE_URL . '/vagas?error=falha_upload');
            exit;
        }

        $dadosCandidato['curriculo'] = $caminhoRelativoParaBD;

        // 3. Invocar os Models para salvar no banco de dados
        $db = Database::getInstance();
        $candidatoModel = new CandidatoModel($db);
        $candidaturaModel = new CandidaturaModel($db);

        $idCandidato = $candidatoModel->buscarOuCriar($dadosCandidato);

        if ($candidaturaModel->verificarExistente($idVaga, $idCandidato)) {
            header('Location: ' . BASE_URL . '/vagas?aviso=candidatura_existente');
            exit;
        }

        $sucesso = $candidaturaModel->criar($idVaga, $idCandidato);

        // 4. Redirecionar com mensagem de feedback
        if ($sucesso) {
            header('Location: ' . BASE_URL . '/vagas?sucesso=candidatura_enviada');
        } else {
            header('Location: ' . BASE_URL . '/vagas?error=erro_banco');
        }
        exit;
    }

    /**
     * Redireciona o usuário para a página principal de listagem de vagas.
     * Usado para tratar acessos diretos a URLs que exigem dados via POST.
     */
    public function redirecionarParaVagas()
    {
        header('Location: ' . BASE_URL . '/vagas');
        exit;
    }
}
