#!/usr/bin/env php
<?php
/**
 * Script d'initialisation de la base de données
 * Utilisation: php scripts/init-db.php
 */

require_once __DIR__ . '/../backend/app/config/Database.php';

$config = require __DIR__ . '/../backend/config.php';

echo "╔════════════════════════════════════════════╗\n";
echo "║  CRL - Initialisation Base de Données     ║\n";
echo "╚════════════════════════════════════════════╝\n\n";

// Étape 1: Créer la base de données et tables
echo "📦 Étape 1: Création de la base de données et tables...\n";

try {
    // Connexion sans DB pour créer la DB
    $dsn = sprintf('mysql:host=%s;charset=utf8mb4', $config['host']);
    $pdo = new PDO($dsn, $config['user'], $config['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    // Créer la base de données
    $pdo->exec(sprintf('CREATE DATABASE IF NOT EXISTS %s CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci', $config['dbname']));
    echo "   ✅ Base de données créée\n";

    // Sélectionner la DB et exécuter le schema
    $pdo->exec(sprintf('USE %s', $config['dbname']));
    
    $schema = file_get_contents(__DIR__ . '/../backend/schema.sql');
    
    // Exécuter chaque statement du schema
    $statements = array_filter(array_map('trim', explode(';', $schema)));
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $pdo->exec($statement);
        }
    }
    
    echo "   ✅ Tables créées avec succès\n";

} catch (PDOException $e) {
    echo "   ❌ Erreur: " . $e->getMessage() . "\n";
    exit(1);
}

// Étape 2: Créer un utilisateur admin par défaut
echo "\n📋 Étape 2: Création utilisateur admin...\n";

try {
    require_once __DIR__ . '/../backend/app/models/User.php';
    
    $pdo = Database::connect();
    $userModel = new User($pdo);
    
    // Vérifier si admin existe
    $existingAdmin = $userModel->findByUsername('admin');
    if ($existingAdmin) {
        echo "   ⚠️  Utilisateur 'admin' existe déjà\n";
    } else {
        $userId = $userModel->create('admin', 'admin@crl.local', 'Admin@123', 'admin');
        echo "   ✅ Admin créé (ID: $userId)\n";
        echo "   📧 Email: admin@crl.local\n";
        echo "   🔐 Mot de passe: Admin@123\n";
    }
    
    // Ajouter quelques utilisateurs de test
    $testUsers = [
        ['username' => 'secretaire', 'email' => 'secretaire@crl.local', 'password' => 'Secr@123', 'role' => 'secretaire'],
        ['username' => 'teacher', 'email' => 'teacher@crl.local', 'password' => 'Teach@123', 'role' => 'comptable'],
    ];
    
    foreach ($testUsers as $user) {
        $existing = $userModel->findByUsername($user['username']);
        if (!$existing) {
            $userModel->create($user['username'], $user['email'], $user['password'], $user['role']);
            echo "   ✅ Utilisateur '{$user['username']}' créé\n";
        }
    }

} catch (Exception $e) {
    echo "   ❌ Erreur: " . $e->getMessage() . "\n";
    exit(1);
}

// Étape 3: Créer des données de test
echo "\n📚 Étape 3: Création données de test...\n";

try {
    require_once __DIR__ . '/../backend/app/models/ClassModel.php';
    require_once __DIR__ . '/../backend/app/models/Student.php';
    
    $classModel = new ClassModel($pdo);
    $studentModel = new Student($pdo);
    
    // Créer quelques classes
    $classes = [
        ['name' => 'Classe A', 'level' => '6e', 'description' => 'Classe de 6ème année'],
        ['name' => 'Classe B', 'level' => 'CM2', 'description' => 'Classe de CM2'],
        ['name' => 'Maternelle', 'level' => 'GS', 'description' => 'Grande Section'],
    ];
    
    foreach ($classes as $class) {
        try {
            $classModel->save($class);
        } catch (Exception $e) {
            // Class peut déjà exister
        }
    }
    
    echo "   ✅ Classes créées\n";
    
    // Récupérer la première classe
    $allClasses = $classModel->getAll();
    if (!empty($allClasses)) {
        $classId = $allClasses[0]['id'];
        
        // Créer quelques étudiants de test
        $students = [
            ['first_name' => 'Jean', 'last_name' => 'Dupont', 'gender' => 'M', 'birth_date' => '2010-05-15', 'class_id' => $classId, 'email' => 'jean@example.com'],
            ['first_name' => 'Marie', 'last_name' => 'Martin', 'gender' => 'F', 'birth_date' => '2010-08-22', 'class_id' => $classId, 'email' => 'marie@example.com'],
        ];
        
        foreach ($students as $student) {
            try {
                $studentModel->save($student);
            } catch (Exception $e) {
                // Student peut déjà exister
            }
        }
        
        echo "   ✅ Étudiants créés\n";
    }

} catch (Exception $e) {
    echo "   ⚠️  Attention lors de la création des données de test: " . $e->getMessage() . "\n";
}

echo "\n╔════════════════════════════════════════════╗\n";
echo "║  ✅ Initialisation Complète!              ║\n";
echo "╚════════════════════════════════════════════╝\n\n";

echo "🚀 Prochaines étapes:\n";
echo "   1. Vérifier que la base de données fonctionne\n";
echo "   2. Configurer votre serveur Apache\n";
echo "   3. Visiter http://localhost/CRL/frontend/login.html\n";
echo "   4. Se connecter avec admin/Admin@123\n\n";
