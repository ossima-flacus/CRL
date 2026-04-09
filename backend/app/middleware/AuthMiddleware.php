<?php

session_start();

class AuthMiddleware
{
    public static function requireAuth(): ?array
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['success' => false, 'message' => 'Authentification requise.']);
            exit;
        }

        require_once __DIR__ . '/../config/Database.php';
        require_once __DIR__ . '/../models/User.php';

        try {
            $pdo = Database::connect();
            $userModel = new User($pdo);
            $user = $userModel->findById($_SESSION['user_id']);
            if (!$user) {
                session_destroy();
                http_response_code(401);
                header('Content-Type: application/json; charset=UTF-8');
                echo json_encode(['success' => false, 'message' => 'Session invalide.']);
                exit;
            }
            return $user;
        } catch (PDOException $e) {
            http_response_code(500);
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['success' => false, 'message' => 'Erreur serveur.']);
            exit;
        }
    }
}
