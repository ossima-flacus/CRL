<?php

require_once __DIR__ . '/../models/Registration.php';
require_once __DIR__ . '/../config/Database.php';

class RegistrationController
{
    public function register(): void
    {
        $rawBody = file_get_contents('php://input');
        $data = json_decode($rawBody, true);

        if (!is_array($data)) {
            $this->respond(['success' => false, 'message' => 'Format JSON invalide.'], 400);
            return;
        }

        $validation = $this->validate($data);
        if ($validation !== true) {
            $this->respond(['success' => false, 'message' => $validation], 400);
            return;
        }

        if (!filter_var($data['guardian']['email'], FILTER_VALIDATE_EMAIL)) {
            $this->respond(['success' => false, 'message' => 'Adresse email du tuteur invalide.'], 400);
            return;
        }

        try {
            $pdo = Database::connect();
            $registration = new Registration($pdo);
            $registrationId = $registration->save($data);

            $this->respond([
                'success' => true, 
                'registration_id' => $registrationId, 
                'message' => 'Inscription enregistrée avec succès.'
            ]);
        } catch (PDOException $e) {
            error_log('Registration error: ' . $e->getMessage());
            $this->respond(['success' => false, 'message' => 'Erreur lors de l\'enregistrement de l\'inscription.'], 500);
        } catch (Exception $e) {
            error_log('Registration exception: ' . $e->getMessage());
            $this->respond(['success' => false, 'message' => 'Erreur serveur.'], 500);
        }
    }

    private function validate(array $data)
    {
        $requiredFields = [
            'first_name',
            'last_name',
            'gender',
            'birth_date',
            'birth_place',
            'birth_certificate_number',
            'nationality',
            'class_id',
            'enrollment_type',
            'guardian'
        ];

        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || $data[$field] === '' || $data[$field] === null) {
                return "Champ requis manquant: $field.";
            }
        }

        $guardian = $data['guardian'];
        $requiredGuardianFields = ['full_name', 'relationship', 'phone', 'email', 'address'];
        foreach ($requiredGuardianFields as $field) {
            if (!isset($guardian[$field]) || $guardian[$field] === '' || $guardian[$field] === null) {
                return "Champ requis du tuteur manquant: $field.";
            }
        }

        return true;
    }

    private function respond(array $payload, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($payload);
    }
}
