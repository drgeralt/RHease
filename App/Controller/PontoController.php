<?php

namespace App\Controller;

use App\Core\Database;
use App\Core\Controller;
use App\Model\ColaboradorModel;
use App\Model\PontoModel;
use Exception;
use PDO;
class PontoController extends Controller
{
    protected $facialApiUrl = 'http://localhost:5000/facial-api';
    protected $pontoModel;
    protected $colaboradorModel;

    public function __construct(
        PontoModel $pontoModel,
        ColaboradorModel $colaboradorModel,
        PDO $pdo
    ) {
        parent::__construct($pdo);
        $this->pontoModel = $pontoModel;
        $this->colaboradorModel = $colaboradorModel;
    }
    /**
     * Carrega e exibe a página principal de registo de ponto.
     */
    public function index()
    {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) { header('Location: ' . BASE_URL . '/login'); exit; }

        $pdo = Database::createConnection();
        $pontoModel = new PontoModel($pdo);
        $colaboradorModel = new ColaboradorModel($pdo);

        $colaborador = $colaboradorModel->getDadosColaborador($userId);

        // NOVA VERIFICAÇÃO
        $precisaCadastrarFace = $colaboradorModel->precisaCadastrarFace($userId);

        $dataAtual = date('Y-m-d');
        $ultimoPonto = $pontoModel->getUltimoPontoAberto($userId, $dataAtual);

        $horaEntrada = null;
        if ($ultimoPonto) {
            $horaEntrada = date('H:i', strtotime($ultimoPonto['data_hora_entrada']));
        }

        require_once BASE_PATH . '/App/View/Colaborador/registroPonto.php';
    }

    /**
     * Registra a face do colaborador na API DeepFace
     */
    public function registrarFace()
    {
        header('Content-Type: application/json');

        try {
            $userId = $_SESSION['user_id'] ?? null;
            if (!$userId) {
                throw new Exception('Usuário não autenticado.');
            }

            if (!isset($_POST['imagem']) || empty($_POST['imagem'])) {
                throw new Exception('Nenhuma imagem recebida.');
            }

            $imgData = $_POST['imagem'];

            // 1. Envia para a API Facial (Python) para criar o arquivo .pkl
            $payload = json_encode([
                'imagem' => $imgData,
                'id_colaborador' => $userId
            ]);

            $ch = curl_init($this->facialApiUrl . '/register-face');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $result = json_decode($response, true);

            if ($httpCode !== 200) {
                throw new Exception($result['message'] ?? 'Erro ao registrar face na API');
            }

            // 2. SUCESSO NA API? Atualiza o MySQL para não pedir mais
            // Importante: certifique-se que $this->colaboradorModel foi instanciado no __construct
            $this->colaboradorModel->confirmarCadastroFace($userId);

            echo json_encode([
                'status' => 'success',
                'message' => 'Face registrada com sucesso!',
                'data' => $result
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }

        exit;
    }

    /**
     * Recebe os dados do front-end (foto), verifica via API DeepFace
     * e registra o ponto na base de dados.
     */
    public function registrar()
    {
        header('Content-Type: application/json');

        try {
            $userId = $_SESSION['user_id'] ?? null;
            if (!$userId) {
                throw new Exception('Usuário não autenticado. Faça login novamente.');
            }

            if (!isset($_POST['imagem']) || empty($_POST['imagem'])) {
                throw new Exception('Nenhuma imagem recebida.');
            }

            $imgData = $_POST['imagem'];
            $geolocalizacao = $_POST['geolocalizacao'] ?? 'Não informada';
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? "Não identificado";

            // Envia para a API Facial para verificação
            $payload = json_encode([
                'imagem' => $imgData,
                'geolocalizacao' => $geolocalizacao,
                'ip_address' => $ipAddress,
                'id_colaborador' => $userId // IMPORTANTE: Enviar o ID para a API saber quem verificar
            ]);

            $ch = curl_init($this->facialApiUrl . '/verify');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if (curl_errno($ch)) {
                $error = curl_error($ch);
                curl_close($ch);
                throw new Exception("Erro de conexão com a API Facial: $error");
            }

            curl_close($ch);
            $result = json_decode($response, true);

            // --- TRATAMENTO ESPECÍFICO PARA ROSTO NÃO RECONHECIDO (401) ---
            if ($httpCode === 401) {
                // Não é erro de servidor, é falha de validação
                http_response_code(400); // Bad Request (lógica de negócio)
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Rosto não correspondente. Tente melhorar a iluminação ou remova acessórios.'
                ]);
                exit;
            }

            // --- TRATAMENTO PARA OUTROS ERROS ---
            if ($httpCode !== 200) {
                $msg = $result['message'] ?? 'Erro desconhecido na API Facial';
                throw new Exception($msg);
            }

            // SE CHEGOU AQUI, O ROSTO FOI RECONHECIDO (200)

            // Registra o ponto no banco local (MySQL)
            $tipoRegistro = $this->pontoModel->registrarPonto(
                $userId,
                date('Y-m-d H:i:s'),
                $geolocalizacao,
                'caminho/para/foto/temp.jpg', // Você pode salvar a foto no disco se quiser
                $ipAddress
            );

            echo json_encode([
                'status' => 'success',
                'tipo' => $tipoRegistro == 'entrada' ? 'Entrada' : 'Saída',
                'horario' => date('H:i')
            ]);

        } catch (Exception $e) {
            // Só retorna 500 se for erro real de código/conexão
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }

        exit;
    }

    /**
     * Verifica o status da API Facial
     */
    public function checkApiStatus()
    {
        header('Content-Type: application/json');

        try {
            $ch = curl_init($this->facialApiUrl . '/health');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'API Facial está online',
                    'data' => json_decode($response, true)
                ]);
            } else {
                throw new Exception('API Facial não está respondendo');
            }

        } catch (Exception $e) {
            http_response_code(503);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }

        exit;
    }
}