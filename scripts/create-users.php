#!/usr/bin/env php
<?php
require_once __DIR__ . '/../backend/app/config/Database.php';

try {
    $pdo = Database::connect();

    $users = [
        ['username' => 'admin', 'password' => 'Admin@123', 'role' => 'admin'],
        ['username' => 'secretaire', 'password' => 'Secr@123', 'role' => 'secretaire'],
        ['username' => 'teacher', 'password' => 'Teach@123', 'role' => 'comptable'],
        ['username' => 'parent', 'password' => 'Parent@123', 'role' => 'parent'],
        ['username' => 'apprenant', 'password' => 'Apprenant@123', 'role' => 'apprenant'],
    ];

    $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, role, is_active) VALUES (?, ?, ?, ?, TRUE)");

    foreach ($users as $user) {
        $hash = password_hash($user['password'], PASSWORD_DEFAULT);
        $email = $user['username'] . '@crl.local';
        $stmt->execute([$user['username'], $email, $hash, $user['role']]);
        echo "✅ Créé: {$user['username']} ({$user['role']}) - {$user['password']}\n";
    }

    echo "\nUtilisateurs créés avec succès!\n";

} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
}
