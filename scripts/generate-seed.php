#!/usr/bin/env php
<?php
/**
 * Générer les mots de passe hashés corrects
 */

$passwords = [
    'admin' => 'Admin@123',
    'secretaire' => 'Secr@123',
    'teacher' => 'Teach@123',
    'parent' => 'Parent@123',
    'apprenant' => 'Apprenant@123',
];

echo "-- ============================================\n";
echo "-- CRL - Données de Test (Mots de passe corrects)\n";
echo "-- ============================================\n";
echo "-- Importer ce fichier après schema.sql\n";
echo "-- Commande: mysql -u root crl_db < backend/seed-fixed.sql\n\n";

echo "-- Insérer des classes de test\n";
echo "INSERT INTO classes (name, level, description, created_at) VALUES\n";
echo "('Classe A', '6e', 'Classe de 6ème année', NOW()),\n";
echo "('Classe B', 'CM2', 'Classe de CM2', NOW()),\n";
echo "('Maternelle', 'GS', 'Grande Section', NOW());\n\n";

echo "-- Insérer les utilisateurs de test\n";
echo "INSERT INTO users (username, email, password_hash, role, is_active) VALUES\n";

$users = [];
foreach ($passwords as $username => $password) {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $email = $username . '@crl.local';
    $role = ($username === 'teacher') ? 'comptable' : $username;
    $users[] = "('$username', '$email', '$hash', '$role', TRUE)";
}

echo implode(",\n", $users) . ";\n\n";

echo "-- Insérer des étudiants de test\n";
echo "INSERT INTO students (first_name, last_name, gender, birth_date, email, phone, class_id, notes, created_at) VALUES\n";
echo "('Jean', 'Dupont', 'M', '2010-05-15', 'jean.dupont@example.com', '+33612345678', 1, 'Élève appliqué', NOW()),\n";
echo "('Marie', 'Martin', 'F', '2010-08-22', 'marie.martin@example.com', '+33687654321', 1, 'Très bonne élève', NOW()),\n";
echo "('Pierre', 'Bernard', 'M', '2011-03-10', 'pierre.bernard@example.com', NULL, 2, 'À suivre', NOW());\n";
