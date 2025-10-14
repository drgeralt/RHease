<?php

namespace App\Model;

use App\Core\Database;
use PDO;
use PDOException;

class UserModel
{
    private PDO $db_connection;

    public function __construct()
    {
        // Usa a classe Database da sua equipe para pegar a conexão
        $this->db_connection = Database::getInstance();
    }

    private function validatePassword(string $password): bool
    {
        if (strlen($password) < 8) return false;
        if (!preg_match('/[A-Z]/', $password)) return false;
        if (!preg_match('/[a-z]/', $password)) return false;
        if (!preg_match('/[0-9]/', $password)) return false;
        if (!preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $password)) return false;
        return true;
    }

    public function create(array $data): array
    {
        $nome = $data['nome_completo'] ?? '';
        $cpf = $data['cpf'] ?? '';
        $email = $data['email_profissional'] ?? '';
        $senha = $data['senha'] ?? '';

        if (empty($nome) || empty($cpf) || empty($email) || empty($senha)) {
            return ['status' => 'error', 'message' => 'Todos os campos são obrigatórios.'];
        }

        if (!$this->validatePassword($senha)) {
            return ['status' => 'error', 'message' => 'A senha não atende aos requisitos de segurança.'];
        }

        $params = [
            ':matricula' => 'C' . time(), // Matrícula gerada aleatoriamente
            ':nome_completo' => $nome,
            ':email_profissional' => $email,
            ':senha' => password_hash($senha, PASSWORD_DEFAULT),
            ':cpf' => $cpf,
            ':status_conta' => 'pendente_verificacao'
        ];

        $sql = "INSERT INTO colaborador (matricula, nome_completo, email_profissional, senha, cpf, status_conta)
                VALUES (:matricula, :nome_completo, :email_profissional, :senha, :cpf, :status_conta)";

        try {
            $stmt = $this->db_connection->prepare($sql);
            $stmt->execute($params);
            return ['status' => 'success', 'message' => 'Cadastro realizado com sucesso! Verifique seu e-mail para ativar a conta.'];
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                return ['status' => 'error', 'message' => 'Este CPF ou e-mail já está cadastrado.'];
            }
            return ['status' => 'error', 'message' => 'Erro ao salvar os dados.'];
        }
    }
    public function activateAccount(string $token): array
    {
        $sql = "SELECT id_colaborador, token_expiracao FROM colaborador WHERE token_verificacao = :token AND status_conta = 'pendente_verificacao'";
        $stmt = $this->db_connection->prepare($sql);
        $stmt->execute([':token' => $token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Se não encontrou o token ou a conta já está ativa
        if (!$user) {
            return ['success' => false, 'message' => 'Link de verificação inválido ou a conta já foi ativada.'];
        }

        // Verifica se o token expirou
        $expiracao = new \DateTime($user['token_expiracao']);
        $agora = new \DateTime();

        if ($agora > $expiracao) {
            return ['success' => false, 'message' => 'Seu link de verificação expirou. Por favor, solicite um novo.'];
        }

        // Se tudo estiver certo, ativa a conta e limpa o token
        $sqlUpdate = "UPDATE colaborador SET status_conta = 'ativo', token_verificacao = NULL, token_expiracao = NULL WHERE id_colaborador = :id";
        $stmtUpdate = $this->db_connection->prepare($sqlUpdate);
        $stmtUpdate->execute([':id' => $user['id_colaborador']]);

        return ['success' => true, 'message' => 'Sua conta foi ativada com sucesso! Você já pode fazer o login.'];
    }
}