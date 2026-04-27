<?php

class RoleMiddleware
{
    public static function requireRole(array $allowedRoles): ?array
    {
        // First check authentication
        $user = AuthMiddleware::requireAuth();
        
        // Check if user has required role
        if (!in_array($user['role'], $allowedRoles, true)) {
            $message = 'Accès refusé. Rôles autorisés: ' . implode(', ', $allowedRoles);
            self::sendError($message, 403);
        }
        
        return $user;
    }

    public static function getUserRole(): ?string
    {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }
        
        $user = AuthMiddleware::requireAuth();
        return $user['role'] ?? null;
    }

    public static function hasRole(string $role): bool
    {
        return isset($_SESSION['user_id']) && $_SESSION['role'] === $role;
    }

    public static function hasAnyRole(array $roles): bool
    {
        return isset($_SESSION['user_id']) && in_array($_SESSION['role'], $roles, true);
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


