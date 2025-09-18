<?php
define('BASE_PATH', realpath(__DIR__ . '/../../'));
// 1. Definir os headers HTTP corretos
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Verifica se o método da requisição é PUT
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(["message" => "Método não permitido."]);
    exit();
}

// 2. Incluir e 3. Instanciar
require_once BASE_PATH . 'app/Model/Colaborador.php';
$colaborador = new Colaborador();

// 4. Obter os dados da requisição (ID da URL e corpo JSON)
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$data = json_decode(file_get_contents("php://input"), true);

// 5. Validar os dados
if (!$id) {
    http_response_code(400); // Bad Request
    echo json_encode(["message" => "ID do colaborador não fornecido na URL."]);
    exit();
}
if (empty($data['nome']) || empty($data['email']) || empty($data['cargo'])) {
    http_response_code(422); // Unprocessable Entity
    echo json_encode(["message" => "Dados incompletos."]);
    exit();
}
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(["message" => "Formato de e-mail inválido."]);
    exit();
}

// 6. Chamar o método apropriado
if ($colaborador->update($id, $data)) {
    // 7. Retornar resposta JSON
    http_response_code(200); // OK
    echo json_encode(["message" => "Colaborador atualizado com sucesso."]);
} else {
    http_response_code(503); // Service Unavailable
    echo json_encode(["message" => "Não foi possível atualizar o colaborador."]);
}
