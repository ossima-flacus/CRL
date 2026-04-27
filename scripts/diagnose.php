#!/usr/bin/env php
<?php
/**
 * Script de diagnostic complet
 * Utilisation: php scripts/diagnose.php
 */

echo "╔════════════════════════════════════════════╗\n";
echo "║  CRL - Diagnostic Complet                 ║\n";
echo "╚════════════════════════════════════════════╝\n\n";

// Étape 1: Vérifier les fichiers
echo "📋 ÉTAPE 1: Vérification des fichiers\n";
echo str_repeat("─", 45) . "\n";

$requiredFiles = [
    'backend/config.php',
    'backend/schema.sql',
    'backend/app/config/Database.php',
    'backend/app/models/User.php',
    'backend/app/controllers/AuthController.php',
    '.env.example',
];

foreach ($requiredFiles as $file) {
    $path = __DIR__ . '/../' . $file;
    if (file_exists($path)) {
        echo "✅ $file\n";
    } else {
        echo "❌ $file MANQUANT!\n";
    }
}

echo "\n";

// Étape 2: Vérifier les connexion DB
echo "🔌 ÉTAPE 2: Vérification Connexion Base de Données\n";
echo str_repeat("─", 45) . "\n";

try {
    require_once __DIR__ . '/../backend/app/config/Database.php';
    
    $pdo = Database::connect();
    echo "✅ Connexion MySQL réussie\n";
    
    // Vérifier les tables
    echo "\n📊 Tables dans la base de données:\n";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (empty($tables)) {
        echo "❌ AUCUNE TABLE TROUVÉE!\n";
        echo "⚠️  La base de données est vide!\n";
        echo "→  Exécutez: php scripts/init-db.php\n";
    } else {
        foreach ($tables as $table) {
            $countStmt = $pdo->query("SELECT COUNT(*) FROM $table");
            $count = $countStmt->fetchColumn();
            echo "   • $table: $count lignes\n";
        }
    }
    
} catch (PDOException $e) {
    echo "❌ Erreur de connexion: " . $e->getMessage() . "\n";
    echo "\nVérifiez:\n";
    echo "   1. MySQL est-il en cours d'exécution?\n";
    echo "   2. Les credentials dans backend/config.php sont-ils corrects?\n";
    echo "   3. La base 'crl_db' existe-t-elle?\n";
    exit(1);
}

echo "\n";

// Étape 3: Vérifier les utilisateurs
echo "👥 ÉTAPE 3: Vérification des Utilisateurs\n";
echo str_repeat("─", 45) . "\n";

try {
    require_once __DIR__ . '/../backend/app/models/User.php';
    
    $userModel = new User($pdo);
    $users = $userModel->getAllActive();
    
    if (empty($users)) {
        echo "❌ AUCUN UTILISATEUR TROUVÉ!\n";
        echo "⚠️  Les données de test n'ont pas été créées!\n";
        echo "→  Exécutez: php scripts/init-db.php\n";
    } else {
        echo "✅ Utilisateurs trouvés: " . count($users) . "\n\n";
        foreach ($users as $user) {
            echo "   • Username: {$user['username']}\n";
            echo "     Email: {$user['email']}\n";
            echo "     Role: {$user['role']}\n";
            echo "     Actif: " . ($user['is_active'] ? 'Oui' : 'Non') . "\n\n";
        }
    }
    
    // Vérifier spécifiquement l'utilisateur admin
    echo "🔑 Test de l'utilisateur 'admin':\n";
    $admin = $userModel->findByUsername('admin');
    if ($admin) {
        echo "✅ Utilisateur 'admin' trouvé\n";
        echo "   • ID: {$admin['id']}\n";
        echo "   • Email: {$admin['email']}\n";
        echo "   • Actif: " . ($admin['is_active'] ? 'Oui' : 'Non') . "\n";
        
        // Test du mot de passe
        $passwordTest = $userModel->verifyPassword($admin, 'Admin@123');
        if ($passwordTest) {
            echo "   ✅ Mot de passe 'Admin@123' est CORRECT\n";
        } else {
            echo "   ❌ Mot de passe 'Admin@123' est INCORRECT!\n";
            echo "   → Le mot de passe a peut-être été modifié\n";
        }
    } else {
        echo "❌ Utilisateur 'admin' NOT FOUND!\n";
        echo "→  Exécutez: php scripts/init-db.php\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}

echo "\n";

// Étape 4: Vérifier les logs d'erreur
echo "📝 ÉTAPE 4: Vérification des Logs\n";
echo str_repeat("─", 45) . "\n";

$logLocations = [
    'c:\\xampp\\apache\\logs\\error.log',
    'c:\\xampp\\apache\\logs\\access.log',
    '/var/log/apache2/error.log',
    '/var/log/apache2/access.log',
    __DIR__ . '/../logs/error.log',
];

$foundLog = false;
foreach ($logLocations as $log) {
    if (file_exists($log) && is_readable($log)) {
        echo "✅ Log trouvé: $log\n";
        echo "\n📄 Dernières lignes d'erreur:\n";
        $lines = file($log);
        $lastLines = array_slice($lines, -5);
        foreach ($lastLines as $line) {
            echo "   " . trim($line) . "\n";
        }
        $foundLog = true;
        break;
    }
}

if (!$foundLog) {
    echo "⚠️  Aucun fichier log trouvé aux emplacements attendus\n";
    echo "→  Vérifiez les logs Apache dans votre console XAMPP\n";
}

echo "\n";

// Étape 5: Test API
echo "🌐 ÉTAPE 5: Test de l'API\n";
echo str_repeat("─", 45) . "\n";

echo "Pour tester manuellement l'API, utilisez:\n\n";
echo "curl -X POST http://localhost/CRL/backend/public/index.php?route=auth/login \\\\\n";
echo "  -H \"Content-Type: application/json\" \\\\\n";
echo "  -d '{\"username\":\"admin\",\"password\":\"Admin@123\"}'\n\n";

// Étape 6: Résumé
echo "╔════════════════════════════════════════════╗\n";
echo "║  Résumé du Diagnostic                    ║\n";
echo "╚════════════════════════════════════════════╝\n\n";

echo "✅ À FAIRE IMMÉDIATEMENT:\n";
echo "   1. Exécutez: php scripts/init-db.php\n";
echo "   2. Attendez la fin du script\n";
echo "   3. Exécutez: php scripts/test-db.php\n";
echo "   4. Redémarrez Apache\n";
echo "   5. Réessayez la connexion\n\n";

echo "Si le problème persiste:\n";
echo "   1. Vérifiez les logs Apache\n";
echo "   2. Testez l'API avec cURL\n";
echo "   3. Consultez QUICK_START.md\n\n";
