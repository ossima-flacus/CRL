<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/ClassModel.php';
require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/../config/Database.php';

class DashboardController
{
    public function dashboard(): void
    {
        $user = RoleMiddleware::requireRole(['admin', 'secretaire', 'comptable']); // Limited for now

        try {
            $pdo = Database::connect();

            // Role-specific data
            $roleData = ['role' => $user['role']];

            if (in_array($user['role'], ['admin', 'secretaire', 'comptable'])) {
                $classModel = new ClassModel($pdo);
                $studentModel = new Student($pdo);
                $roleData['classes'] = $classModel->getAll();
                $roleData['students'] = $studentModel->getAll();
                $roleData['permissions'] = ['add_class', 'add_student', 'view_dashboard'];
            } elseif (in_array($user['role'], ['parent', 'apprenant'])) {
                // Personal bulletins only
                // TODO
                $roleData['permissions'] = ['view_bulletins', 'blog'];
            }

            $this->respond([
                'success' => true,
                'data' => $roleData
            ]);
        } catch (PDOException $e) {
            $this->respond(['success' => false, 'message' => 'Erreur dashboard.'], 500);
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

