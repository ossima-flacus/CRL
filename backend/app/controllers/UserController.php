<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../config/Database.php';

class UserController {
    public function list(): void {
        try {
            $pdo = Database::connect();
            $userModel = new User($pdo);
            $users = $userModel->getAllActive();
            $this->respond(['success' => true, 'users' => $users]);
        } catch (Exception $e) {
            $this->respond(['success' => false, 'message' => 'Erreur liste'], 500);
        }
    }

    public function create(): void {
        $rawBody = file_get_contents('php://input');
        $data = json_decode($rawBody, true);
        
        if (!$data || !isset($data['username'], $data['email'], $data['password'], $data['role'])) {
            $this->respond(['success' => false, 'message' => 'Données invalides'], 400);
            return;
        }

        try {
            $pdo = Database::connect();
            $userModel = new User($pdo);
            $userId = $userModel->create($data['username'], $data['email'], $data['password'], $data['role']);
            $this->respond(['success' => true, 'user_id' => $userId]);
        } catch (Exception $e) {
            $this->respond(['success' => false, 'message' => 'Erreur création'], 500);
        }
    }

    public function delete(): void {
        $userId = $_GET['id'] ?? 0;
        if (!$userId) {
            $this->respond(['success' => false, 'message' => 'ID requis'], 400);
            return;
        }

        try {
            $pdo = Database::connect();
            $userModel = new User($pdo);
            $userModel->delete($userId);
            $this->respond(['success' => true]);
        } catch (Exception $e) {
            $this->respond(['success' => false, 'message' => 'Erreur suppression'], 500);
        }
    }

    private function respond(array $payload, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($payload);
    }
}

