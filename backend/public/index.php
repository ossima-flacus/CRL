<?php
header('Content-Type: application/json; charset=UTF-8');

session_start();

require __DIR__ . '/../app/config/Database.php';
require __DIR__ . '/../app/controllers/AuthController.php';
require __DIR__ . '/../app/controllers/RegistrationController.php';
require __DIR__ . '/../app/controllers/ClassController.php';
require __DIR__ . '/../app/controllers/StudentController.php';
require __DIR__ . '/../app/middleware/AuthMiddleware.php';
require __DIR__ . '/../app/middleware/RoleMiddleware.php';

$route = $_GET['route'] ?? '';
if ($route === '') {
    $scriptName = basename($_SERVER['SCRIPT_NAME']);
    if ($scriptName === 'register.php') {
        $route = 'register';
    }
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($route) {
    case 'auth/login':
    case 'auth/register':
        if ($method === 'POST') {
            if ($route === 'auth/login') {
                (new AuthController())->login();
            } else {
                (new AuthController())->register();
            }
            exit;
        }
        break;
    case 'auth/logout':
        if ($method === 'POST') {
            (new AuthController())->logout();
            exit;
        }
        break;
    case 'auth/me':
        if ($method === 'GET') {
            (new AuthController())->me();
            exit;
        }
        break;
    case 'register':
        if ($method === 'POST') {
            (new RegistrationController())->register();
            exit;
        }
        break;
    case 'class.create':
        RoleMiddleware::requireRole(['admin', 'secretaire', 'comptable']);
        if ($method === 'POST') {
            (new ClassController())->create();
            exit;
        }
        break;
    case 'class.list':
        RoleMiddleware::requireRole(['admin', 'secretaire', 'comptable']);
        if ($method === 'GET') {
            (new ClassController())->list();
            exit;
        }
        break;
    case 'student.create':
        RoleMiddleware::requireRole(['admin', 'secretaire', 'comptable']);
        if ($method === 'POST') {
            (new StudentController())->create();
            exit;
        }
        break;
    case 'student.list':
        RoleMiddleware::requireRole(['admin', 'secretaire', 'comptable']);
        if ($method === 'GET') {
            (new StudentController())->list();
            exit;
        }
        break;
    case 'dashboard':
        RoleMiddleware::requireRole(['admin', 'secretaire', 'comptable', 'parent', 'apprenant']);
        if ($method === 'GET') {
require __DIR__ . '/../app/controllers/DashboardController.php';
            (new DashboardController())->dashboard();
            exit;
        }
        break;
    case 'users/list':
        RoleMiddleware::requireRole(['admin']);
        require __DIR__ . '/../app/controllers/UserController.php';
        (new UserController())->list();
        exit;
        break;
    case 'users/create':
        RoleMiddleware::requireRole(['admin']);
        if ($method === 'POST') {
            require __DIR__ . '/../app/controllers/UserController.php';
            (new UserController())->create();
            exit;
        }
        break;
    case 'users/delete':
        RoleMiddleware::requireRole(['admin']);
        require __DIR__ . '/../app/controllers/UserController.php';
        (new UserController())->delete();
        exit;
        break;
    case 'bulletin.list':
        RoleMiddleware::requireRole(['admin', 'parent', 'apprenant']);
        // TODO
        break;
    case 'publicite.create':
        RoleMiddleware::requireRole(['secretaire', 'comptable']);
        // TODO
        break;
}

http_response_code(404);
echo json_encode(['success' => false, 'message' => 'Route introuvable.']);
?>

