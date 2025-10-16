<?php

namespace App\Model;

use App\Core\Database;
use PDO;
use PDOException;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

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
        $mail = new PHPMailer(true);
        $verificationLink = BASE_URL . '/verify?token=' . $token;

        try {
            $mail->isSMTP();
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->Host       = $_ENV['MAIL_HOST'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['MAIL_USERNAME'];
            $mail->Password   = $_ENV['MAIL_PASSWORD'];
            $mail->SMTPSecure = $_ENV['MAIL_ENCRYPTION'];
            $mail->Port       = $_ENV['MAIL_PORT'];

            // Remetente e Destinatário
            $mail->setFrom($_ENV['MAIL_USERNAME'], $_ENV['MAIL_FROM_NAME']);
            $mail->addAddress($email);

            // Conteúdo do E-mail
            $mail->isHTML(true);
            $mail->Subject = 'Ative sua conta - RHEase';
            $mail->Body    = "<h1>Bem-vindo ao RHEase!</h1><p>Clique no link a seguir para ativar sua conta: <a href='$verificationLink'>Ativar Conta</a></p><p>Este link expirará em 1 hora.</p>";
            $mail->AltBody = "Ative sua conta copiando e colando este link em seu navegador: $verificationLink";

            $mail->send();
        } catch (Exception $e) {
            echo "Mailer Error: {$mail->ErrorInfo}";
            exit;
        }
    }

    public function registerUser(array $data): array
    {
        $nome = $data['nome_completo'] ?? '';
        $cpf = $data['cpf'] ?? '';
        $email = $data['email_profissional'] ?? '';
        $senha = $data['senha'] ?? '';

        if (empty($nome) || empty($cpf) || empty($email) || empty($senha)) {
            return ['status' => 'error', 'message' => 'Todos os campos são obrigatórios.'];
        }

        if (!$this->validatePassword($senha)) {
            return ['status' => 'error', 'message' => 'A senha não atende aos critérios de segurança.'];
        }

        try {
            $hashedPassword = password_hash($senha, PASSWORD_DEFAULT);
            $token = bin2hex(random_bytes(32));
            $tokenHash = hash('sha256', $token);
            $expiracao = (new \DateTime('+1 hour'))->format('Y-m-d H:i:s');

            $sql = "INSERT INTO colaborador (nome_completo, cpf, email_profissional, senha, token_verificacao, token_expiracao, status_conta, data_admissao) 
                    VALUES (:nome, :cpf, :email, :senha, :token, :expiracao, 'pendente_verificacao', CURDATE())";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':nome' => $nome,
                ':cpf' => $cpf,
                ':email' => $email,
                ':senha' => $hashedPassword,
                ':token' => $tokenHash,
                ':expiracao' => $expiracao
            ]);

            $this->sendVerificationEmail($email, $token);

            return ['status' => 'success', 'message' => 'Cadastro realizado com sucesso! Verifique seu e-mail.'];
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                return ['status' => 'error', 'message' => 'E-mail ou CPF já cadastrado.'];
            }
            return ['status' => 'error', 'message' => 'Erro ao salvar os dados.'];
        }
    }

    public function activateAccount(string $token): array
    {
        // ... (código do activateAccount existente, sem alterações)
        $tokenHash = hash('sha256', $token);
        $sql = "SELECT id_colaborador, token_expiracao FROM colaborador WHERE token_verificacao = :token AND status_conta = 'pendente_verificacao'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':token' => $tokenHash]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return ['success' => false, 'message' => 'Link de verificação inválido, expirado ou já utilizado.'];
        }

        $expiracao = new \DateTime($user['token_expiracao']);
        if (new \DateTime() > $expiracao) {
            return ['success' => false, 'message' => 'Seu link de verificação expirou.'];
        }

        $sql = "UPDATE colaborador SET status_conta = 'ativo', token_verificacao = NULL, token_expiracao = NULL WHERE id_colaborador = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $user['id_colaborador']]);
        return ['success' => true, 'message' => 'Conta ativada com sucesso! Você já pode fazer login.'];
    }

    public function loginUser(string $email, string $senha): array
    {
        // ... (código do loginUser existente, sem alterações)
        $sql = "SELECT id_colaborador, senha, status_conta FROM colaborador WHERE email_profissional = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return ['status' => 'error', 'message' => 'E-mail ou senha inválidos.'];
        }

        if (password_verify($senha, $user['senha'])) {
            if ($user['status_conta'] === 'ativo') {
                return ['status' => 'success', 'user_id' => $user['id_colaborador']];
            }
            if ($user['status_conta'] === 'pendente_verificacao') {
                return ['status' => 'error', 'message' => 'Sua conta ainda não foi ativada. Verifique seu e-mail.'];
            }
        }
        return ['status' => 'error', 'message' => 'E-mail ou senha inválidos.'];
    }

    // --- NOVOS MÉTODOS PARA RECUPERAÇÃO DE SENHA ---

    private function sendPasswordResetEmail(string $email, string $token): void
    {
        $mail = new PHPMailer(true);
        $resetLink = BASE_URL . '/redefinir-senha?token=' . $token;

        try {
            $mail->isSMTP();
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER; descomente apenas para debug
            $mail->Host       = $_ENV['MAIL_HOST'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['MAIL_USERNAME'];
            $mail->Password   = $_ENV['MAIL_PASSWORD'];
            $mail->SMTPSecure = $_ENV['MAIL_ENCRYPTION'];
            $mail->Port       = $_ENV['MAIL_PORT'];

            $mail->setFrom($_ENV['MAIL_USERNAME'], $_ENV['MAIL_FROM_NAME']);
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Redefinicao de Senha - RHEase';
            $mail->Body    = "<h1>Redefinir sua senha</h1><p>Recebemos uma solicitação para redefinir sua senha. Clique no link a seguir para continuar: <a href='$resetLink'>Redefinir Senha</a></p><p>Este link expirará em 15 minutos. Se você não solicitou isso, pode ignorar este e-mail.</p>";
            $mail->AltBody = "Redefina sua senha copiando e colando este link em seu navegador: $resetLink";

            $mail->send();
        } catch (Exception $e) {
            echo "Mailer Error: {$mail->ErrorInfo}";
            exit;
        }
    }

    public function generatePasswordResetToken(string $email): array
    {
        $sql = "SELECT id_colaborador FROM colaborador WHERE email_profissional = :email AND status_conta = 'ativo'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            // Mensagem genérica para não revelar se um e-mail existe ou não
            return ['status' => 'success', 'message' => 'Se um e-mail correspondente for encontrado, um link de recuperação será enviado.'];
        }

        try {
            $token = bin2hex(random_bytes(32));
            $tokenHash = hash('sha256', $token);
            // Token expira em 15 minutos
            $expiracao = (new \DateTime('+15 minutes'))->format('Y-m-d H:i:s');

            $sql = "UPDATE colaborador SET token_recuperacao = :token, token_expiracao = :expiracao WHERE id_colaborador = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':token' => $tokenHash,
                ':expiracao' => $expiracao,
                ':id' => $user['id_colaborador']
            ]);

            $this->sendPasswordResetEmail($email, $token);

            return ['status' => 'success', 'message' => 'Se um e-mail correspondente for encontrado, um link de recuperação será enviado.'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Ocorreu um erro ao processar sua solicitação.'];
        }
    }

    public function isPasswordResetTokenValid(string $token): bool
    {
        if (empty($token)) {
            return false;
        }

        $tokenHash = hash('sha256', $token);

        $sql = "SELECT id_colaborador, token_expiracao FROM colaborador WHERE token_recuperacao = :token";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':token' => $tokenHash]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return false; // Token não encontrado
        }

        // Verifica se o token expirou
        if (new \DateTime() > new \DateTime($user['token_expiracao'])) {
            return false; // Token expirado
        }

        return true;
    }

    public function resetPassword(string $token, string $newPassword): array
    {
        if (!$this->isPasswordResetTokenValid($token)) {
            return ['status' => 'error', 'message' => 'Token inválido ou expirado.'];
        }

        if (!$this->validatePassword($newPassword)) {
            return ['status' => 'error', 'message' => 'A nova senha não atende aos critérios de segurança.'];
        }

        $tokenHash = hash('sha256', $token);
        $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);

        try {
            // Atualiza a senha e invalida o token
            $sql = "UPDATE colaborador 
                    SET senha = :senha, token_recuperacao = NULL, token_expiracao = NULL 
                    WHERE token_recuperacao = :token";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':senha' => $newPasswordHash,
                ':token' => $tokenHash
            ]);

            return ['status' => 'success', 'message' => 'Senha redefinida com sucesso! Você já pode fazer login.'];

        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => 'Ocorreu um erro ao atualizar sua senha.'];
        }
    }
}