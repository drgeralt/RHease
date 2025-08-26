<?php

class InternalCommunicationModel
{
    private $db_connection;

    public function __construct()
    {
        $this->db_connection = Database::getInstance();
    }

    public function getMessages()
    {
        $sql = "SELECT * FROM messages ORDER BY created_at DESC";

        try {
            $stmt = $this->db_connection->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao buscar as mensagens: " . $e->getMessage());
            return [];
        }
    }


    public function sendMessage($data)
    {
        $sql = "INSERT INTO messages (sender_id, receiver_id, content, created_at)
                VALUES (:sender_id, :receiver_id, :content, NOW())";

        try {
            $stmt = $this->db_connection->prepare($sql);

            $stmt->bindParam(':sender_id', $data['sender_id']);
            $stmt->bindParam(':receiver_id', $data['receiver_id']);
            $stmt->bindParam(':content', $data['content']);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao salvar a mensagem: " . $e->getMessage());
            return false;
        }
    }
}