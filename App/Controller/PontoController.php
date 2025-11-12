<?php

namespace App\Controller;

use App\Core\Database;
use App\Model\ColaboradorModel;
use App\Model\PontoModel;
use Exception;
use PDO;
class PontoController
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
        if (!$userId) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $pdo = Database::getInstance();
        $pontoModel = new PontoModel($pdo);
        $colaboradorModel = new ColaboradorModel($pdo);

        // Busca os dados do colaborador para a view
        $colaborador = $colaboradorModel->getDadosColaborador($userId);

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
                throw new Exception('Usuário não autenticado. Faça login novamente.');
            }

            if (!isset($_POST['imagem']) || empty($_POST['imagem'])) {
                throw new Exception('Nenhuma imagem recebida.');
            }

            $imgData = $_POST['imagem'];

            // Envia para a API Facial
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

            if ($httpCode !== 200) {
                $errorData = json_decode($response, true);
                throw new Exception($errorData['message'] ?? 'Erro ao registrar face na API');
            }

            $result = json_decode($response, true);

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
                'ip_address' => $ipAddress
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
                throw new Exception("Erro ao conectar com a API Facial: $error");
            }

            curl_close($ch);

            if ($httpCode !== 200) {
                $errorData = json_decode($response, true);
                throw new Exception($errorData['message'] ?? 'Face não reconhecida');
            }

            $result = json_decode($response, true);

            // Retorna o resultado da API
            echo json_encode($result);

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