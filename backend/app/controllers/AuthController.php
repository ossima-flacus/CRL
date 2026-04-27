<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../config/Database.php';

class AuthController
{
    public function login(): void
    {
        $rawBody = file_get_contents('php://input');
        $data = json_decode($rawBody, true);

        if (!is_array($data)) {
            $this->respond(['success' => false, 'message' => 'Format JSON invalide.'], 400);
            return;
        }

        $loginValue = $data['username'] ?? $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!$loginValue || !$password) {
            $this->respond(['success' => false, 'message' => 'Identifiant et mot de passe requis.'], 400);
            return;
        }

        try {
            $pdo = Database::connect();
            $userModel = new User($pdo);
            $user = $userModel->findByUsernameOrEmail($loginValue);

            if (!$user || !$userModel->verifyPassword($user, $data['password'])) {
                $this->respond(['success' => false, 'message' => 'Identifiants invalides.'], 401);
                return;
            }

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['email'] = $user['email'];

            $this->respond([
                'success' => true, 
                'message' => 'Connexion réussie.',
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'role' => $user['role']
                ]
            ]);
        } catch (PDOException $e) {
            error_log('Login error: ' . $e->getMessage());
            $this->respond(['success' => false, 'message' => 'Erreur serveur.'], 500);
        } catch (Exception $e) {
            error_log('Login exception: ' . $e->getMessage());
            $this->respond(['success' => false, 'message' => 'Erreur serveur.'], 500);
        }
    }

    public function register(): void
    {
        $rawBody = file_get_contents('php://input');
        $data = json_decode($rawBody, true);

        if (!is_array($data)) {
            $this->respond(['success' => false, 'message' => 'Format JSON invalide.'], 400);
            return;
        }

        if (!isset($data['username']) || !isset($data['email']) || !isset($data['password'])) {
            $this->respond(['success' => false, 'message' => 'Nom d\'utilisateur, email et mot de passe requis.'], 400);
            return;
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->respond(['success' => false, 'message' => 'Adresse email invalide.'], 400);
            return;
        }

        $allowedRoles = ['admin', 'secretaire', 'comptable', 'parent', 'apprenant'];
        $role = $data['role'] ?? 'apprenant';
        if (!in_array($role, $allowedRoles, true)) {
            $this->respond(['success' => false, 'message' => 'Rôle invalide.'], 400);
            return;
        }

        if (strlen((string)$data['password']) < 6) {
            $this->respond(['success' => false, 'message' => 'Mot de passe doit avoir au moins 6 caractères.'], 400);
            return;
        }

        if (strlen((string)$data['username']) < 3) {
            $this->respond(['success' => false, 'message' => 'Nom d\'utilisateur doit avoir au moins 3 caractères.'], 400);
            return;
        }

        try {
            $pdo = Database::connect();
            $userModel = new User($pdo);
            $existing = $userModel->findByUsername($data['username']);
            if ($existing) {
                $this->respond(['success' => false, 'message' => 'Cet utilisateur existe déjà.'], 409);
                return;
            }

            $userId = $userModel->create($data['username'], $data['email'], $data['password'], $role);
            $this->respond(['success' => true, 'user_id' => $userId, 'role' => $role, 'message' => 'Compte créé avec succès.']);
        } catch (PDOException $e) {
            error_log('Register error: ' . $e->getMessage());
            $this->respond(['success' => false, 'message' => 'Erreur lors de la création du compte.'], 500);
        } catch (Exception $e) {
            error_log('Register exception: ' . $e->getMessage());
            $this->respond(['success' => false, 'message' => 'Erreur serveur.'], 500);
        }
    }

    public function logout(): void
    {
        session_destroy();
        $this->respond(['success' => true, 'message' => 'Déconnexion réussie.']);
    }

    public function me(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $this->respond(['success' => false, 'message' => 'Non connecté.'], 401);
            return;
        }

        try {
            $pdo = Database::connect();
            $userModel = new User($pdo);
            $user = $userModel->findById($_SESSION['user_id']);
            if (!$user) {
                session_destroy();
                $this->respond(['success' => false, 'message' => 'Session invalide.'], 401);
                return;
            }
            $this->respond(['success' => true, 'user' => $user]);
        } catch (PDOException $e) {
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
