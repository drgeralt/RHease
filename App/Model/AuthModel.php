<?php

namespace App\Model;

use App\Core\Database;
use PDO;
use PDOException;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP; // Adicionado para usar o SMTP::DEBUG_SERVER

// A linha 'require mailer/PHPMailerAutoload.php' foi removida. O Composer já faz isso.

class AuthModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
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

    private function sendVerificationEmail(string $email, string $token): void
    {
        // A flag 'true' no construtor já ativa as exceções.
        $mail = new PHPMailer(true);

        // A linha "$mail->exceptions = true;" que causava o erro foi REMOVIDA.

        try {
            // Ativa o debug detalhado para vermos a conversa com o Gmail
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;

            // Configurações do servidor a partir do .env
            $mail->isSMTP();
            $mail->Host       = $_ENV['MAIL_HOST'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['MAIL_USERNAME'];
            $mail->Password   = $_ENV['MAIL_PASSWORD'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;
            $mail->CharSet    = 'UTF-8';

            // Destinatários
            $mail->setFrom($_ENV['MAIL_USERNAME'], $_ENV['MAIL_FROM_NAME']);
            $mail->addAddress($email);

            // Conteúdo
            $mail->isHTML(true);
            $mail->Subject = 'Ative sua conta no RHEase';
            $verificationLink = BASE_URL . "/verify?token=" . $token;
            $mail->Body    = "Olá! <br><br>Obrigado por se registrar. Clique no link para ativar sua conta: <a href='{$verificationLink}'>Ativar Conta</a>.";

            $mail->send();

        } catch (Exception $e) {
            // Se houver um erro, ele será "relançado" para ser capturado no método registerUser
            throw new Exception("PHPMailer Error: {$mail->ErrorInfo}");
        }
    }

    public function registerUser(array $data): array
    {
        $nome = trim($data['nome_completo'] ?? '');
        $cpf = trim($data['cpf'] ?? '');
        $email = trim($data['email_profissional'] ?? '');
        $senha = $data['senha'] ?? '';

        if (empty($nome) || empty($cpf) || empty($email) || empty($senha)) {
            return ['status' => 'error', 'message' => 'Todos os campos são obrigatórios.'];
        }

        if (!$this->validatePassword($senha)) {
            return ['status' => 'error', 'message' => 'A senha não atende aos requisitos de segurança.'];
        }

        $token = bin2hex(random_bytes(32));
        $expiracao = (new \DateTime())->add(new \DateInterval('PT1H'))->format('Y-m-d H:i:s');

        $sql = "INSERT INTO colaborador (nome_completo, cpf, email_profissional, senha, status_conta, token_verificacao, token_expiracao)
                VALUES (:nome, :cpf, :email, :senha, 'pendente_verificacao', :token, :expiracao)";

        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':nome' => $nome,
                ':cpf' => $cpf,
                ':email' => $email,
                ':senha' => password_hash($senha, PASSWORD_DEFAULT),
                ':token' => $token,
                ':expiracao' => $expiracao
            ]);

            // Tenta enviar o e-mail
            $this->sendVerificationEmail($email, $token);

            $this->db->commit();
            return ['status' => 'success', 'message' => 'Cadastro realizado! Verifique seu e-mail.'];

        } catch (PDOException $e) {
            $this->db->rollBack();
            if ($e->getCode() === '23000') {
                return ['status' => 'error', 'message' => 'Este CPF ou e-mail já está cadastrado.'];
            }
            error_log("DB Error: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro ao salvar os dados.'];

        } catch (Exception $e) { // Captura erros do PHPMailer vindos de sendVerificationEmail
            $this->db->rollBack();
            error_log("Mailer Error: " . $e->getMessage());
            // Mostra o erro real do PHPMailer no alerta para depuração
            return ['status' => 'error', 'message' => 'Não foi possível enviar o e-mail: ' . $e->getMessage()];
        }
    }

    public function activateAccount(string $token): array
    {
        $sql = "UPDATE colaborador SET status_conta = 'ativo', token_verificacao = NULL, token_expiracao = NULL 
                WHERE token_verificacao = :token AND token_expiracao > NOW() AND status_conta = 'pendente_verificacao'";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':token' => $token]);

        if ($stmt->rowCount() > 0) {
            return ['success' => true, 'message' => 'Conta ativada com sucesso! Você já pode fazer login.'];
        } else {
            return ['success' => false, 'message' => 'Link de verificação inválido, expirado ou já utilizado.'];
        }
    }
    public function loginUser(string $email, string $senha): array
    {
        // 1. Encontrar o utilizador pelo e-mail
        $sql = "SELECT id_colaborador, senha, status_conta FROM colaborador WHERE email_profissional = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // 2. Se o utilizador não existe, a senha está incorreta (mensagem genérica por segurança)
        if (!$user) {
            return ['status' => 'error', 'message' => 'E-mail ou senha inválidos.'];
        }

        // 3. Verificar se a senha corresponde E se a conta está ativa
        if (password_verify($senha, $user['senha'])) {
            // Senha correta! Agora verificamos o status da conta.

            if ($user['status_conta'] === 'ativo') {
                // Login bem-sucedido!
                return ['status' => 'success', 'user_id' => $user['id_colaborador']];
            }

            if ($user['status_conta'] === 'pendente_verificacao') {
                // A conta existe, mas não foi ativada
                return ['status' => 'error', 'message' => 'Sua conta ainda não foi ativada. Verifique seu e-mail.'];
            }

            // Outros status como 'inativo' ou 'bloqueado'
            return ['status' => 'error', 'message' => 'Esta conta está inativa ou bloqueada.'];
        }

        // 4. Se a senha estiver incorreta (mensagem genérica)
        return ['status' => 'error', 'message' => 'E-mail ou senha inválidos.'];
    }
}