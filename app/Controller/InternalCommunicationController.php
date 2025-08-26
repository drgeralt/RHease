<?php

require_once BASE_PATH . '/app/Core/Controller.php';
require_once BASE_PATH . '/app/Models/InternalCommunicationModel.php';

class InternalCommunicationController
{
    public function index()
    {
        $model = new InternalCommunicationModel();
        $messages = $model->getMessages();
        require_once BASE_PATH . '/app/Views/communication/index.php';
    }

    public function sendMessage()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $content = $_POST['content'];

            $model = new InternalCommunicationModel();

            $senderId = $_POST['sender_id'] ?? null;
            $receiverId = $_POST['receiver_id'] ?? null;

            if ($senderId === null || $receiverId === null) {
                // Handle error: sender_id or receiver_id not provided
                // For now, we'll just redirect back or show an error
                header('Location: /comunicacao?error=missing_ids');
                exit;
            }

            $data = [
                'sender_id' => $senderId,
                'receiver_id' => $receiverId,
                'content' => $content
            ];

            $model->sendMessage($data);

            header('Location: /comunicacao');
            exit;
        }
    }
}