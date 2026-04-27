<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../config/Database.php';

class UserController {
    public function list(): void {
        try {
            $pdo = Database::connect();
            $userModel = new User($pdo);
            $users = $userModel->getAllActive();
            $this->respond(['success' => true, 'users' => $users, 'count' => count($users)]);
        } catch (PDOException $e) {
            error_log('List users error: ' . $e->getMessage());
            $this->respond(['success' => false, 'message' => 'Erreur lors de la récupération des utilisateurs'], 500);
        } catch (Exception $e) {
            error_log('List users exception: ' . $e->getMessage());
            $this->respond(['success' => false, 'message' => 'Erreur serveur'], 500);
        }
    }

    public function create(): void {
        $rawBody = file_get_contents('php://input');
        $data = json_decode($rawBody, true);
        
        if (!is_array($data) || !isset($data['username'], $data['email'], $data['password'], $data['role'])) {
            $this->respond(['success' => false, 'message' => 'Données invalides ou incomplètes'], 400);
            return;
        }

        try {
            $pdo = Database::connect();
            $userModel = new User($pdo);
            $userId = $userModel->create($data['username'], $data['email'], $data['password'], $data['role']);
            $this->respond(['success' => true, 'user_id' => $userId, 'message' => 'Utilisateur créé avec succès']);
        } catch (PDOException $e) {
            error_log('Create user error: ' . $e->getMessage());
            $this->respond(['success' => false, 'message' => 'Erreur lors de la création de l\'utilisateur'], 500);
        } catch (Exception $e) {
            error_log('Create user exception: ' . $e->getMessage());
            $this->respond(['success' => false, 'message' => 'Erreur serveur'], 500);
        }
    }

    public function delete(): void {
        $userId = $_GET['id'] ?? 0;
        if (!$userId || !is_numeric($userId)) {
            $this->respond(['success' => false, 'message' => 'ID utilisateur requis et doit être valide'], 400);
            return;
        }

        try {
            $pdo = Database::connect();
            $userModel = new User($pdo);
            $success = $userModel->delete((int)$userId);
            if (!$success) {
                $this->respond(['success' => false, 'message' => 'Utilisateur non trouvé'], 404);
                return;
            }
            $this->respond(['success' => true, 'message' => 'Utilisateur supprimé avec succès']);
        } catch (PDOException $e) {
            error_log('Delete user error: ' . $e->getMessage());
            $this->respond(['success' => false, 'message' => 'Erreur lors de la suppression de l\'utilisateur'], 500);
        } catch (Exception $e) {
            error_log('Delete user exception: ' . $e->getMessage());
            $this->respond(['success' => false, 'message' => 'Erreur serveur'], 500);
        }
    }

    private function respond(array $payload, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($payload);
    }
}

