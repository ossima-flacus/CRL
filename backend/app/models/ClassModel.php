<?php

class ClassModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function save(array $data): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO classes (name, level, description, created_at)
             VALUES (:name, :level, :description, NOW())'
        );

        $stmt->execute([
            ':name' => $data['name'],
            ':level' => $data['level'],
            ':description' => $data['description'] ?? null,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->query('SELECT id, name, level, description, created_at FROM classes ORDER BY created_at DESC');
        return $stmt->fetchAll();
    }
}
