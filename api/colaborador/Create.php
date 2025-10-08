<?php
// Headers obrigatórios
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Verifica se o método da requisição é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["message" => "Método não permitido."]);
    exit();
}

// --- ESTA É A LINHA CORRIGIDA ---
// Define o caminho base do projeto de forma mais confiável, subindo dois níveis.
define('BASE_PATH', dirname(dirname(__DIR__)));

// Inclui o Model
require_once BASE_PATH . '/app/Model/Colaborador.php';

// Instancia o objeto Colaborador
$colaborador = new Colaborador();

// Obtém os dados da requisição
$data = json_decode(file_get_contents("php://input"), true);

// Validação dos dados (campos obrigatórios conforme o novo BD)
if (
    empty($data['nome_completo']) ||
    empty($data['CPF']) ||
    empty($data['RG']) ||
    empty($data['email']) ||
    empty($data['data_admissao']) ||
    empty($data['id_setor'])
) {
    http_response_code(422); // Unprocessable Entity
    echo json_encode(["message" => "Não foi possível criar o colaborador. Dados obrigatórios incompletos."]);
    exit();
}

// Validação do formato do e-mail
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(["message" => "Formato de e-mail inválido."]);
    exit();
}

// Tenta criar o colaborador
if ($colaborador->create($data)) {
    http_response_code(201); // Created
    echo json_encode(["message" => "Colaborador criado com sucesso."]);
} else {
    http_response_code(503); // Service Unavailable
    echo json_encode(["message" => "Não foi possível criar o colaborador."]);
}