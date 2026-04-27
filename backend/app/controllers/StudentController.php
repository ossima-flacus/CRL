<?php

require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/../config/Database.php';

class StudentController
{
    public function create(): void
    {
        $data = $this->getJsonInput();
        if (!is_array($data)) {
            $this->respond(['success' => false, 'message' => 'Format JSON invalide.'], 400);
            return;
        }

        $validation = $this->validate($data);
        if ($validation !== true) {
            $this->respond(['success' => false, 'message' => $validation], 400);
            return;
        }

        try {
            $pdo = Database::connect();
            $model = new Student($pdo);
            $studentId = $model->save($data);
            $this->respond(['success' => true, 'student_id' => $studentId, 'message' => 'Élève ajouté avec succès.']);
        } catch (PDOException $e) {
            error_log('Create student error: ' . $e->getMessage());
            $this->respond(['success' => false, 'message' => 'Erreur lors de la création de l\'enregistrement.'], 500);
        } catch (Exception $e) {
            error_log('Create student exception: ' . $e->getMessage());
            $this->respond(['success' => false, 'message' => 'Erreur serveur.'], 500);
        }
    }

    public function list(): void
    {
        try {
            $pdo = Database::connect();
            $model = new Student($pdo);
            $students = $model->getAll();
            $this->respond(['success' => true, 'students' => $students, 'count' => count($students)]);
        } catch (PDOException $e) {
            error_log('List students error: ' . $e->getMessage());
            $this->respond(['success' => false, 'message' => 'Erreur lors de la récupération des élèves.'], 500);
        } catch (Exception $e) {
            error_log('List students exception: ' . $e->getMessage());
            $this->respond(['success' => false, 'message' => 'Erreur serveur.'], 500);
        }
    }

    private function validate(array $data)
    {
        if (empty($data['first_name'])) {
            return 'Le prénom de l’élève est requis.';
        }
        if (empty($data['last_name'])) {
            return 'Le nom de l’élève est requis.';
        }
        if (empty($data['gender'])) {
            return 'Le genre est requis.';
        }
        if (empty($data['birth_date'])) {
            return 'La date de naissance est requise.';
        }
        if (empty($data['class_id'])) {
            return 'La classe est requise pour l’élève.';
        }
        return true;
    }

    private function getJsonInput()
    {
        $rawBody = file_get_contents('php://input');
        return json_decode($rawBody, true);
    }

    private function respond(array $payload, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($payload);
    }
}
