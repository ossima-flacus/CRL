<?php

require_once __DIR__ . '/../models/ClassModel.php';
require_once __DIR__ . '/../config/Database.php';

class ClassController
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
            $model = new ClassModel($pdo);
            $classId = $model->save($data);
            $this->respond(['success' => true, 'class_id' => $classId, 'message' => 'Classe créée avec succès.']);
        } catch (PDOException $e) {
            error_log('Create class error: ' . $e->getMessage());
            $this->respond(['success' => false, 'message' => 'Erreur lors de la création de la classe.'], 500);
        } catch (Exception $e) {
            error_log('Create class exception: ' . $e->getMessage());
            $this->respond(['success' => false, 'message' => 'Erreur serveur.'], 500);
        }
    }

    public function list(): void
    {
        try {
            $pdo = Database::connect();
            $model = new ClassModel($pdo);
            $classes = $model->getAll();
            $this->respond(['success' => true, 'classes' => $classes, 'count' => count($classes)]);
        } catch (PDOException $e) {
            error_log('List classes error: ' . $e->getMessage());
            $this->respond(['success' => false, 'message' => 'Erreur lors de la récupération des classes.'], 500);
        } catch (Exception $e) {
            error_log('List classes exception: ' . $e->getMessage());
            $this->respond(['success' => false, 'message' => 'Erreur serveur.'], 500);
        }
    }

    private function validate(array $data)
    {
        if (empty($data['name'])) {
            return 'Le nom de la classe est requis.';
        }
        if (empty($data['level'])) {
            return 'Le niveau de la classe est requis.';
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
