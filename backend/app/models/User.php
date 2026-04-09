<?php

class User
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findByUsername(string $username): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE username = :username AND is_active = TRUE');
        $stmt->execute([':username' => $username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = :id AND is_active = TRUE');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create(string $username, string $email, string $password, string $role = 'admin'): int
    {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (username, email, password_hash, role) VALUES (:username, :email, :password_hash, :role)'
        );
        $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':password_hash' => $passwordHash,
            ':role' => $role
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function verifyPassword(array $user, string $password): bool
    {
        return password_verify($password, $user['password_hash']);
    }

    public function getUsersByRole(string $role): array
    {
        $stmt = $this->pdo->prepare('SELECT id, username, email, role FROM users WHERE role = :role AND is_active = TRUE');
        $stmt->execute([':role' => $role]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllActive(): array
    {
        $stmt = $this->pdo->prepare('SELECT id, username, email, role FROM users WHERE is_active = TRUE ORDER BY created_at DESC');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('UPDATE users SET is_active = FALSE WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
