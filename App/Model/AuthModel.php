<?php
declare(strict_types=1);

namespace App\Model;

use App\Core\Model;
use PDO;
use PDOException;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class AuthModel extends Model
{
    private PHPMailer $mailer;

    public function __construct(PDO $pdo, PHPMailer $mailer)
    {
        parent::__construct($pdo);
        $this->mailer = $mailer;
    }

    public function validatePassword(string $password): bool
    {
        if (strlen($password) < 8) return false;
        if (!preg_match('/[A-Z]/', $password)) return false;
        if (!preg_match('/[a-z]/', $password)) return false;
        if (!preg_match('/[0-9]/', $password)) return false;
        if (!preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $password)) return false;
        return true;
    }

    public function sendVerificationEmail(string $email, string $token): void
    {
        $verificationLink = BASE_URL . '/verify?token=' . $token;

        $this->mailer->setFrom($_ENV['MAIL_USERNAME'], $_ENV['MAIL_FROM_NAME']);
        $this->mailer->addAddress($email);
        $this->mailer->isHTML(true);
        $this->mailer->Subject = 'Ative sua conta - RHEase';
        $this->mailer->Body    = "<h1>Bem-vindo ao RHEase!</h1><p>Clique no link a seguir para ativar sua conta: <a href='$verificationLink'>Ativar Conta</a></p><p>Este link expirará em 1 hora.</p>";
        $this->mailer->AltBody = "Ative sua conta copiando e colando este link em seu navegador: $verificationLink";

        $this->mailer->send();
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
            $stmt = $this->db_connection->prepare($sql);
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
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => 'Erro ao enviar e-mail de verificação.'];
        }
    }

    public function activateAccount(string $token): array
    {
        $tokenHash = hash('sha256', $token);
        $sql = "SELECT id_colaborador, token_expiracao FROM colaborador WHERE token_verificacao = :token AND status_conta = 'pendente_verificacao'";
        $stmt = $this->db_connection->prepare($sql);
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
        $stmt = $this->db_connection->prepare($sql);
        $stmt->execute([':id' => $user['id_colaborador']]);
        return ['success' => true, 'message' => 'Conta ativada com sucesso! Você já pode fazer login.'];
    }

    public function reenviarVerificacao(string $email): array
    {
        if (empty($email)) {
            return ['status' => 'error', 'message' => 'Por favor, insira seu e-mail.'];
        }

        try {
            $sql = "SELECT id_colaborador, status_conta FROM colaborador WHERE email_pessoal = :email OR email_profissional = :email";
            $stmt = $this->db_connection->prepare($sql);
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                return ['status' => 'success', 'message' => 'Se uma conta pendente existir com este e-mail, um novo link foi enviado.'];
            }

            if ($user['status_conta'] === 'ativo') {
                return ['status' => 'error', 'message' => 'Esta conta já foi ativada. Você pode fazer login.'];
            }

            if ($user['status_conta'] === 'pendente_verificacao') {
                $token = bin2hex(random_bytes(32));
                $tokenHash = hash('sha256', $token);
                $expires = (new \DateTime('now + 1 hour'))->format('Y-m-d H:i:s');

                $sql = "UPDATE colaborador 
                        SET token_verificacao = :token, token_expiracao = :expires 
                        WHERE id_colaborador = :id";
                $stmt = $this->db_connection->prepare($sql);
                $stmt->execute([
                    ':token' => $tokenHash,
                    ':expires' => $expires,
                    ':id' => $user['id_colaborador']
                ]);

                $this->sendVerificationEmail($email, $token);
            }

            return ['status' => 'success', 'message' => 'Se uma conta pendente existir com este e-mail, um novo link foi enviado.'];

        } catch (PDOException $e) {
            error_log("Database Error (reenviarVerificacao): " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Ocorreu um erro no banco de dados.'];
        } catch (Exception $e) {
            error_log("Mail Error (reenviarVerificacao): " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Ocorreu um erro ao enviar o e-mail.'];
        } catch (\Throwable $t) {
            error_log("General Error (reenviarVerificacao): " . $t->getMessage());
            return ['status' => 'error', 'message' => 'Ocorreu um erro inesperado.'];
        }
    }

    public function loginUser(string $email, string $senha): array
    {
        $maxAttempts = 5;
        $lockoutTimeMinutes = 15;

        $sql = "SELECT
                    id_colaborador,
                    senha,
                    status_conta,
                    perfil,
                    failed_login_attempts,
                    last_failed_login_at,
                    (failed_login_attempts >= :max_attempts AND last_failed_login_at IS NOT NULL AND last_failed_login_at > DATE_SUB(NOW(), INTERVAL :lockout_minutes MINUTE)) AS is_locked,
                    CEIL(TIMESTAMPDIFF(SECOND, NOW(), DATE_ADD(last_failed_login_at, INTERVAL :lockout_minutes MINUTE)) / 60) AS minutes_remaining
                FROM colaborador
                WHERE email_profissional = :email";

        $stmt = $this->db_connection->prepare($sql);
        $stmt->bindValue(':max_attempts', $maxAttempts, PDO::PARAM_INT);
        $stmt->bindValue(':lockout_minutes', $lockoutTimeMinutes, PDO::PARAM_INT);
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return ['status' => 'error', 'message' => 'E-mail ou senha inválidos.'];
        }

        if ($user['is_locked']) {
            $remainingMinutes = max(1, (int)$user['minutes_remaining']);
            error_log("Tentativa de login bloqueada (MySQL check) para {$email}. Tempo restante (MySQL calc): {$remainingMinutes} min.");
            return ['status' => 'error', 'message' => "Muitas tentativas falhadas. Tente novamente em {$remainingMinutes} minuto(s)."];
        }

        if (password_verify($senha, $user['senha'])) {
            if ($user['status_conta'] === 'ativo') {
                if ($user['failed_login_attempts'] > 0 || $user['last_failed_login_at'] !== null) {
                    $this->resetLoginAttempts($user['id_colaborador']);
                }
                return ['status' => 'success', 'user_id' => $user['id_colaborador'], 'user_perfil' => $user['perfil']];
            }
            if ($user['status_conta'] === 'pendente_verificacao') { return ['status' => 'error', 'message' => 'Sua conta ainda não foi ativada. Verifique seu e-mail.']; }
            return ['status' => 'error', 'message' => 'Conta inativa ou com problemas. Contacte o suporte.'];

        } else {
            $newAttemptCount = ($user['failed_login_attempts'] ?? 0) + 1;
            $this->incrementFailedLoginAttempts($user['id_colaborador']);

            if ($newAttemptCount >= $maxAttempts) {
                error_log("Conta bloqueada para {$email} após {$newAttemptCount} tentativas.");
                return ['status' => 'error', 'message' => "E-mail ou senha inválidos. Sua conta foi bloqueada por $lockoutTimeMinutes minutos devido a muitas tentativas falhadas."];
            } else {
                $attemptsLeft = $maxAttempts - $newAttemptCount;
                $plural = ($attemptsLeft !== 1) ? 's' : '';
                return ['status' => 'error', 'message' => "E-mail ou senha inválidos. Você tem mais $attemptsLeft tentativa{$plural} antes do bloqueio."];
            }
        }
    }

    public function incrementFailedLoginAttempts(int $userId): void {
        try {
            $sql = "UPDATE colaborador SET failed_login_attempts = failed_login_attempts + 1, last_failed_login_at = CURRENT_TIMESTAMP WHERE id_colaborador = :id";
            $stmt = $this->db_connection->prepare($sql);
            $stmt->execute([':id' => $userId]);
        } catch (PDOException $e) { error_log("Erro ao incrementar tentativas para user ID $userId: " . $e->getMessage()); }
    }

    public function resetLoginAttempts(int $userId): void {
        try {
            $sql = "UPDATE colaborador SET failed_login_attempts = 0, last_failed_login_at = NULL WHERE id_colaborador = :id";
            $stmt = $this->db_connection->prepare($sql);
            $stmt->execute([':id' => $userId]);
        } catch (PDOException $e) { error_log("Erro ao resetar tentativas para user ID $userId: " . $e->getMessage()); }
    }

    public function sendPasswordResetEmail(string $email, string $token): void
    {
        $resetLink = BASE_URL . '/redefinir-senha?token=' . $token;

        $this->mailer->setFrom($_ENV['MAIL_USERNAME'], $_ENV['MAIL_FROM_NAME']);
        $this->mailer->addAddress($email);
        $this->mailer->isHTML(true);
        $this->mailer->Subject = 'Redefinicao de Senha - RHEase';
        $this->mailer->Body    = "<h1>Redefinir sua senha</h1><p>Recebemos uma solicitação para redefinir sua senha. Clique no link a seguir para continuar: <a href='$resetLink'>Redefinir Senha</a></p><p>Este link expirará em 15 minutos. Se você não solicitou isso, pode ignorar este e-mail.</p>";
        $this->mailer->AltBody = "Redefina sua senha copiando e colando este link em seu navegador: $resetLink";

        $this->mailer->send();
    }

    public function generatePasswordResetToken(string $email): array
    {
        $sql = "SELECT id_colaborador FROM colaborador WHERE email_profissional = :email AND status_conta = 'ativo'";
        $stmt = $this->db_connection->prepare($sql);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return ['status' => 'success', 'message' => 'Se um e-mail correspondente for encontrado, um link de recuperação será enviado.'];
        }

        try {
            $token = bin2hex(random_bytes(32));
            $tokenHash = hash('sha256', $token);
            $expiracao = (new \DateTime('+15 minutes'))->format('Y-m-d H:i:s');

            $sql = "UPDATE colaborador SET token_recuperacao = :token, token_expiracao = :expiracao WHERE id_colaborador = :id";
            $stmt = $this->db_connection->prepare($sql);
            $stmt->execute([
                ':token' => $tokenHash,
                ':expiracao' => $expiracao,
                ':id' => $user['id_colaborador']
            ]);

            $this->sendPasswordResetEmail($email, $token);

            return ['status' => 'success', 'message' => 'Se um e-mail correspondente for encontrado, um link de recuperação será enviado.'];
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => 'Erro ao enviar e-mail de redefinição.'];
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
        $stmt = $this->db_connection->prepare($sql);
        $stmt->execute([':token' => $tokenHash]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return false;
        }

        if (new \DateTime() > new \DateTime($user['token_expiracao'])) {
            return false;
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
            $sql = "UPDATE colaborador 
                    SET senha = :senha, token_recuperacao = NULL, token_expiracao = NULL 
                    WHERE token_recuperacao = :token";
            $stmt = $this->db_connection->prepare($sql);
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