<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../config/Database.php';

session_start();

class AuthController
{
    public function login(): void
    {
        $rawBody = file_get_contents('php://input');
        $data = json_decode($rawBody, true);

        if (!isset($data['username']) || !isset($data['password'])) {
            $this->respond(['success' => false, 'message' => 'Username et mot de passe requis.'], 400);
            return;
        }

        try {
            $pdo = Database::connect();
            $userModel = new User($pdo);
            $user = $userModel->findByUsername($data['username']);

            if (!$user || !$userModel->verifyPassword($user, $data['password'])) {
                $this->respond(['success' => false, 'message' => 'Identifiants invalides.'], 401);
                return;
            }

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            $this->respond([
                'success' => true, 
                'message' => 'Connexion réussie.',
                'user' => ['id' => $user['id'], 'username' => $user['username'], 'role' => $user['role']]
            ]);
        } catch (PDOException $e) {
            $this->respond(['success' => false, 'message' => 'Erreur DB. Vérifiez phpMyAdmin crl_db.'], 500);
        }
    }

    public function register(): void
    {
        $rawBody = file_get_contents('php://input');
        $data = json_decode($rawBody, true);

        if (!isset($data['username']) || !isset($data['email']) || !isset($data['password']) || !isset($data['role'])) {
            $this->respond(['success' => false, 'message' => 'Username, email, mot de passe et rôle requis.'], 400);
            return;
        }

        $allowedRoles = ['admin', 'secretaire', 'comptable', 'parent', 'apprenant'];
        if (!in_array($data['role'], $allowedRoles)) {
            $this->respond(['success' => false, 'message' => 'Rôle invalide.'], 400);
            return;
        }

        if (strlen($data['password']) < 6) {
            $this->respond(['success' => false, 'message' => 'Mot de passe trop court.'], 400);
            return;
        }

        try {
            $pdo = Database::connect();
            $userModel = new User($pdo);
            $existing = $userModel->findByUsername($data['username']);
            if ($existing) {
                $this->respond(['success' => false, 'message' => 'Username déjà pris.'], 409);
                return;
            }

            $userId = $userModel->create($data['username'], $data['email'], $data['password'], $data['role']);
            $this->respond(['success' => true, 'user_id' => $userId, 'role' => $data['role'], 'message' => 'Compte créé.']);
        } catch (PDOException $e) {
            $this->respond(['success' => false, 'message' => 'Erreur lors de la création.'], 500);
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
