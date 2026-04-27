<?php
// Error handling - Ensure JSON is returned, not HTML
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../../logs/php-errors.log');

// Create logs directory if it doesn't exist
$logDir = __DIR__ . '/../../logs';
if (!is_dir($logDir)) {
    @mkdir($logDir, 0755, true);
}

// Set error and exception handlers to return JSON
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    error_log("[$errno] $errstr in $errfile:$errline");
    return false;
});

set_exception_handler(function ($exception) {
    http_response_code(500);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode([
        'success' => false,
        'message' => 'Erreur serveur. Veuillez réessayer plus tard.',
        'error' => ($_ENV['APP_ENV'] ?? 'production') === 'development' ? $exception->getMessage() : null
    ]);
    exit;
});

// Set headers
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load environment variables
require __DIR__ . '/../../config.php';

// Load required classes
require __DIR__ . '/../app/config/Database.php';
require __DIR__ . '/../app/middleware/AuthMiddleware.php';
require __DIR__ . '/../app/middleware/RoleMiddleware.php';
require __DIR__ . '/../app/controllers/AuthController.php';
require __DIR__ . '/../app/controllers/RegistrationController.php';
require __DIR__ . '/../app/controllers/ClassController.php';
require __DIR__ . '/../app/controllers/StudentController.php';
require __DIR__ . '/../app/controllers/UserController.php';
require __DIR__ . '/../app/controllers/DashboardController.php';

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
            (new DashboardController())->dashboard();
            exit;
        }
        break;
    case 'users/list':
        RoleMiddleware::requireRole(['admin']);
        (new UserController())->list();
        exit;
        break;
    case 'users/create':
        RoleMiddleware::requireRole(['admin']);
        if ($method === 'POST') {
            (new UserController())->create();
            exit;
        }
        break;
    case 'users/delete':
        RoleMiddleware::requireRole(['admin']);
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

