<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/ClassModel.php';
require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/../config/Database.php';

class DashboardController
{
    public function dashboard(): void
    {
        $user = RoleMiddleware::requireRole(['admin', 'secretaire', 'comptable', 'parent', 'apprenant']);

        try {
            $pdo = Database::connect();

            $roleData = [
                'role' => $user['role'],
                'username' => $user['username'],
                'email' => $user['email']
            ];

            if (in_array($user['role'], ['admin', 'secretaire', 'comptable'], true)) {
                $classModel = new ClassModel($pdo);
                $studentModel = new Student($pdo);
                $roleData['classes'] = $classModel->getAll();
                $roleData['students'] = $studentModel->getAll();
                $roleData['permissions'] = ['add_class', 'add_student', 'view_dashboard', 'manage_users'];
            } elseif (in_array($user['role'], ['parent', 'apprenant'], true)) {
                // Personal data only
                $roleData['permissions'] = ['view_bulletins', 'view_blog'];
            }

            $this->respond([
                'success' => true,
                'data' => $roleData
            ]);
        } catch (PDOException $e) {
            error_log('Dashboard error: ' . $e->getMessage());
            $this->respond(['success' => false, 'message' => 'Erreur lors du chargement du tableau de bord.'], 500);
        } catch (Exception $e) {
            error_log('Dashboard exception: ' . $e->getMessage());
            $this->respond(['success' => false, 'message' => 'Erreur serveur.'], 500);
        }
    }

    private function respond(array $payload, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($payload);
    }
}
?>

