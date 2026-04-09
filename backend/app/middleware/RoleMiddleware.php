<?php
session_start();

class RoleMiddleware
{
    public static function requireRole(array $allowedRoles): ?array
    {
        $user = AuthMiddleware::requireAuth(); // Reuse AuthMiddleware
        if (!in_array($user['role'], $allowedRoles)) {
            http_response_code(403);
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['success' => false, 'message' => 'Accès refusé. Rôle requis: ' . implode('/', $allowedRoles)]);
            exit;
        }
        return $user;
    }

    public static function getUserRole(): ?string
    {
        if (!isset($_SESSION['user_id'])) return null;
        $user = AuthMiddleware::requireAuth();
        return $user['role'];
    }
}
?>

