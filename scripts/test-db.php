#!/usr/bin/env php
<?php
/**
 * Script de test de la connexion à la base de données
 * Utilisation: php scripts/test-db.php
 */

require_once __DIR__ . '/../backend/app/config/Database.php';
require_once __DIR__ . '/../backend/app/models/User.php';

echo "╔════════════════════════════════════════════╗\n";
echo "║  CRL - Test de Connexion Base de Données  ║\n";
echo "╚════════════════════════════════════════════╝\n\n";

try {
    echo "🔌 Test de connexion à la base de données...\n";
    $pdo = Database::connect();
    echo "✅ Connexion réussie!\n\n";

    // Test des tables
    echo "📋 Vérification des tables:\n";
    
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($tables as $table) {
        $countStmt = $pdo->query("SELECT COUNT(*) FROM $table");
        $count = $countStmt->fetchColumn();
        echo "   • $table: $count enregistrements\n";
    }
    
    echo "\n👤 Utilisateurs dans le système:\n";
    $userModel = new User($pdo);
    $users = $userModel->getAllActive();
    
    if (empty($users)) {
        echo "   ⚠️  Aucun utilisateur trouvé. Lancez d'abord: php scripts/init-db.php\n";
    } else {
        foreach ($users as $user) {
            echo "   • {$user['username']} ({$user['role']}) - {$user['email']}\n";
        }
    }
    
    echo "\n✅ Tous les tests sont passés!\n\n";

} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "\nDébogage:\n";
    echo "   • Vérifiez que MySQL est en cours d'exécution\n";
    echo "   • Vérifiez les paramètres dans .env\n";
    echo "   • Vérifiez les permissions de l'utilisateur MySQL\n\n";
    exit(1);
}
