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

        try {
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Descomente apenas se precisar de depurar e-mails

            $mail->isSMTP();
            $mail->Host       = $_ENV['MAIL_HOST'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['MAIL_USERNAME'];
            $mail->Password   = $_ENV['MAIL_PASSWORD'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom($_ENV['MAIL_USERNAME'], $_ENV['MAIL_FROM_NAME']);
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Ative sua conta no RHEase';
            $verificationLink = BASE_URL . "/verify?token=" . $token;
            $mail->Body    = "Olá! <br><br>Obrigado por se registrar. Clique no link para ativar sua conta: <a href='{$verificationLink}'>Ativar Conta</a>.";

            $mail->send();
        } catch (Exception $e) {
            throw new Exception("PHPMailer Error: {$mail->ErrorInfo}");
        }
    }

    public function registerUser(array $data): array
    {
        // A sua lógica de validação de nome, cpf, e-mail, etc., permanece aqui
        if (empty($data['nome_completo']) || empty($data['cpf']) || empty($data['email_profissional']) || empty($data['senha'])) {
            return ['status' => 'error', 'message' => 'Todos os campos são obrigatórios.'];
        }
        // ... (outras validações)

        // A query SQL foi atualizada para incluir a 'data_admissao'
        $sql = "INSERT INTO colaborador (
                matricula, nome_completo, cpf, email_pessoal, email_profissional, senha,
                status_conta, token_verificacao, token_expiracao, data_admissao, situacao
            ) VALUES (
                :matricula, :nome, :cpf, :email_pessoal, :email_profissional, :senha,
                'pendente_verificacao', :token, :expiracao, :data_admissao, :situacao
            )";

        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare($sql);

            // O array de execução foi corrigido e completado
            $stmt->execute([
                ':matricula' => 'C' . time(),
                ':nome' => $data['nome_completo'],
                ':cpf' => $data['cpf'],
                ':email_pessoal' => $data['email_profissional'],
                ':email_profissional' => $data['email_profissional'],
                ':senha' => password_hash($data['senha'], PASSWORD_DEFAULT),
                ':token' => bin2hex(random_bytes(32)),
                ':expiracao' => (new \DateTime())->add(new \DateInterval('PT1H'))->format('Y-m-d H:i:s'),
                ':data_admissao' => date('Y-m-d'), // Adiciona a data de hoje
                ':situacao' => 'ativo'
            ]);

            // Supondo que a sua lógica de envio de e-mail e commit vem a seguir
            // $this->sendVerificationEmail($email, $token);
            $this->db->commit();

            return ['status' => 'success', 'message' => 'Cadastro realizado! Verifique seu e-mail.'];

        } catch (PDOException $e) {
            if ($this->db->inTransaction()) { $this->db->rollBack(); }
            // ... (o seu código de tratamento de erro)
            return ['status' => 'error', 'message' => 'Erro interno ao salvar os dados.'];
        } catch (\Exception $e) {
            if ($this->db->inTransaction()) { $this->db->rollBack(); }
            // ... (o seu código de tratamento de erro)
            return ['status' => 'error', 'message' => 'Não foi possível enviar o e-mail.'];
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
            return ['status' => 'error', 'message' => 'Esta conta está inativa ou bloqueada.'];
        }

        return ['status' => 'error', 'message' => 'E-mail ou senha inválidos.'];
    }

}