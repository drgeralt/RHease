<?php
define('BASE_PATH', realpath(__DIR__ . '/../../'));
// 1. Definir os headers HTTP corretos
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

// 2. Incluir as classes necessárias
require_once BASE_PATH . '/app/Model/Colaborador.php';

// 3. Instanciar o objeto Colaborador
$colaborador = new Colaborador();

// 4. Obter os dados da requisição
$data = json_decode(file_get_contents("php://input"), true);

// 5. Validar os dados
if (
    empty($data['nome']) ||
    empty($data['email']) ||
    empty($data['cargo'])
) {
    http_response_code(422); // Unprocessable Entity
    echo json_encode(["message" => "Dados incompletos. Por favor, forneça nome, email e cargo."]);
    exit();
}

if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(["message" => "Formato de e-mail inválido."]);
    exit();
}

// 6. Chamar o método apropriado
if ($colaborador->create($data)) {
    // 7. Retornar uma resposta JSON com o código de status HTTP correto
    http_response_code(201); // Created
    echo json_encode(["message" => "Colaborador criado com sucesso."]);
} else {
    http_response_code(503); // Service Unavailable
    echo json_encode(["message" => "Não foi possível criar o colaborador."]);
}
