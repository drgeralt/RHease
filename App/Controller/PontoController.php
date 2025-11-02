<?php

namespace App\Controller;

use App\Core\Database;
use App\Model\ColaboradorModel;
use App\Model\PontoModel;
use Exception;

class PontoController
{
    /**
     * Carrega e exibe a página principal de registo de ponto.
     */
    public function index()
    {
        /*$userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }*/
        $userId = 1;
        $pdo = Database::getInstance();
        $pontoModel = new PontoModel($pdo);
        $colaboradorModel = new ColaboradorModel($pdo);

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
     * Recebe os dados do front-end (foto), envia para a API Python
     * para verificação facial e registo de ponto.
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

            // Prepara os dados para enviar à API Python
            $apiData = [
                'imagem' => $imgData,
                'geolocalizacao' => $geolocalizacao,
                'ip_address' => $ipAddress
            ];

            // URL da API Python (ajuste se necessário)
            $apiUrl = 'http://localhost:5000/facial-api/verify';

            // Inicializa cURL
            $ch = curl_init($apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($apiData));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Accept: application/json'
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            // Executa a requisição
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($curlError) {
                throw new Exception("Erro ao conectar com a API de reconhecimento facial: " . $curlError);
            }

            $result = json_decode($response, true);

            if ($httpCode !== 200) {
                $errorMessage = $result['message'] ?? 'Erro desconhecido na verificação facial';
                throw new Exception($errorMessage);
            }

            if ($result['status'] !== 'success') {
                throw new Exception($result['message'] ?? 'Falha na verificação facial');
            }

            // Verifica se o ID do colaborador reconhecido corresponde ao usuário logado
            if ($result['id_colaborador'] != $userId) {
                throw new Exception('A face reconhecida não corresponde ao usuário logado. Por favor, tire uma foto do próprio rosto.');
            }

            echo json_encode([
                'status' => 'success',
                'message' => 'Ponto registado como ' . $result['tipo'] . ' com sucesso!',
                'horario' => $result['horario'],
                'tipo' => $result['tipo'],
                'similarity' => $result['similarity'] ?? null
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
     * Endpoint para registrar a face do colaborador
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

            // Prepara os dados para enviar à API Python
            $apiData = [
                'imagem' => $imgData,
                'id_colaborador' => $userId
            ];

            // URL da API Python
            $apiUrl = 'http://localhost:5000/facial-api/register-face';

            // Inicializa cURL
            $ch = curl_init($apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($apiData));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Accept: application/json'
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            // Executa a requisição
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($curlError) {
                throw new Exception("Erro ao conectar com a API de reconhecimento facial: " . $curlError);
            }

            $result = json_decode($response, true);

            if ($httpCode !== 200) {
                $errorMessage = $result['message'] ?? 'Erro desconhecido ao registrar face';
                throw new Exception($errorMessage);
            }

            if ($result['status'] !== 'success') {
                throw new Exception($result['message'] ?? 'Falha ao registrar face');
            }

            echo json_encode([
                'status' => 'success',
                'message' => 'Face registrada com sucesso! Agora você pode usar o reconhecimento facial para registrar ponto.'
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
}