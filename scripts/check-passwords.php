#!/usr/bin/env php
<?php
require_once __DIR__ . '/../backend/app/config/Database.php';
require_once __DIR__ . '/../backend/app/models/User.php';

try {
    $pdo = Database::connect();
    $userModel = new User($pdo);

    $testPasswords = [
        'admin' => 'Admin@123',
        'secretaire' => 'Secr@123',
        'teacher' => 'Teach@123',
        'parent' => 'Parent@123',
        'apprenant' => 'Apprenant@123',
    ];

    echo "VÉRIFICATION DES MOTS DE PASSE:\n";
    echo str_repeat("=", 40) . "\n";

    foreach ($testPasswords as $username => $expectedPassword) {
        $user = $userModel->findByUsername($username);
        if (!$user) {
            echo "❌ Utilisateur '$username' non trouvé\n";
            continue;
        }

        $isValid = password_verify($expectedPassword, $user['password_hash']);
        echo "$username: " . ($isValid ? "✅ OK" : "❌ ÉCHEC") . "\n";
        if (!$isValid) {
            echo "  Attendu: $expectedPassword\n";
            echo "  Hash en DB: " . substr($user['password_hash'], 0, 20) . "...\n";
        }
    }

} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
}
