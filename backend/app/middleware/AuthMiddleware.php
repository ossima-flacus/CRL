<?php

class AuthMiddleware
{
    public static function requireAuth(): ?array
    {
        // Check if user is authenticated
        if (!isset($_SESSION['user_id'])) {
            self::sendError('Authentification requise.', 401);
        }

        try {
            require_once __DIR__ . '/../config/Database.php';
            require_once __DIR__ . '/../models/User.php';

            $pdo = Database::connect();
            $userModel = new User($pdo);
            $user = $userModel->findById($_SESSION['user_id']);
            
            if (!$user) {
                session_destroy();
                self::sendError('Session invalide.', 401);
            }
            
            return $user;
        } catch (PDOException $e) {
            error_log('Auth error: ' . $e->getMessage());
            self::sendError('Erreur serveur.', 500);
        }
    }

    public static function isAuthenticated(): bool
    {
        return isset($_SESSION['user_id']);
    }

    private static function sendError(string $message, int $statusCode): void
    {
        if (PHP_SAPI === 'cli') {
            throw new RuntimeException($message);
        }
        
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['success' => false, 'message' => $message]);
        exit;
    }
}
