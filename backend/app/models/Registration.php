<?php

class Registration
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function save(array $data): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO registrations (
                first_name, last_name, gender,
                birth_date, birth_place, birth_certificate_number,
                previous_school, nationality, class_id,
                enrollment_type, guardian_name, guardian_relationship,
                guardian_phone, guardian_email, guardian_address,
                guardian_profession, created_at
            ) VALUES (
                :first_name, :last_name, :gender,
                :birth_date, :birth_place, :birth_certificate_number,
                :previous_school, :nationality, :class_id,
                :enrollment_type, :guardian_name, :guardian_relationship,
                :guardian_phone, :guardian_email, :guardian_address,
                :guardian_profession, NOW()
            )'
        );

        $stmt->execute([
            ':first_name' => $data['first_name'],
            ':last_name' => $data['last_name'],
            ':gender' => $data['gender'],
            ':birth_date' => $data['birth_date'],
            ':birth_place' => $data['birth_place'],
            ':birth_certificate_number' => $data['birth_certificate_number'],
            ':previous_school' => $data['previous_school'],
            ':nationality' => $data['nationality'],
            ':class_id' => (int) $data['class_id'],
            ':enrollment_type' => $data['enrollment_type'],
            ':guardian_name' => $data['guardian']['full_name'],
            ':guardian_relationship' => $data['guardian']['relationship'],
            ':guardian_phone' => $data['guardian']['phone'],
            ':guardian_email' => $data['guardian']['email'],
            ':guardian_address' => $data['guardian']['address'],
            ':guardian_profession' => $data['guardian']['profession'],
        ]);

        return (int) $this->pdo->lastInsertId();
    }
}
