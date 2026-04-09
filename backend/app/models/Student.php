<?php

class Student
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function save(array $data): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO students (
                first_name, last_name, gender, birth_date,
                email, phone, class_id, notes, created_at
            ) VALUES (
                :first_name, :last_name, :gender, :birth_date,
                :email, :phone, :class_id, :notes, NOW()
            )'
        );

        $stmt->execute([
            ':first_name' => $data['first_name'],
            ':last_name' => $data['last_name'],
            ':gender' => $data['gender'],
            ':birth_date' => $data['birth_date'],
            ':email' => $data['email'] ?? null,
            ':phone' => $data['phone'] ?? null,
            ':class_id' => $data['class_id'] ? (int) $data['class_id'] : null,
            ':notes' => $data['notes'] ?? null,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->query(
            'SELECT s.id, s.first_name, s.last_name, s.gender, s.birth_date, s.email, s.phone, s.class_id, c.name AS class_name, s.notes, s.created_at
             FROM students s
             LEFT JOIN classes c ON s.class_id = c.id
             ORDER BY s.created_at DESC'
        );

        return $stmt->fetchAll();
    }
}
