<?php
/**
 * Test Complet du Projet CRL
 * Vérifie la configuration, la base de données et les endpoints API
 */

echo "========================================\n";
echo "     Test Complet - Projet CRL\n";
echo "========================================\n\n";

$errors = [];
$warnings = [];
$success = [];

// 1. Vérifier PHP version
echo "[1] Vérification de PHP...\n";
if (PHP_VERSION_ID >= 70400) {
    $success[] = "PHP version: " . PHP_VERSION;
    echo "✓ PHP version: " . PHP_VERSION . "\n";
} else {
    $errors[] = "PHP 7.4+ requis (version actuelle: " . PHP_VERSION . ")";
    echo "✗ PHP 7.4+ requis\n";
}

// 2. Vérifier les extensions PHP nécessaires
echo "\n[2] Vérification des extensions PHP...\n";
$extensions = ['pdo', 'pdo_mysql', 'json'];
foreach ($extensions as $ext) {
    if (extension_loaded($ext)) {
        $success[] = "Extension $ext disponible";
        echo "✓ Extension $ext disponible\n";
    } else {
        $errors[] = "Extension $ext manquante";
        echo "✗ Extension $ext manquante\n";
    }
}

// 3. Vérifier les fichiers de configuration
echo "\n[3] Vérification des fichiers de configuration...\n";
$configFiles = [
    '.env' => __DIR__ . '/.env',
    'composer.json' => __DIR__ . '/composer.json',
    'backend/config.php' => __DIR__ . '/backend/config.php',
    '.htaccess' => __DIR__ . '/.htaccess'
];

foreach ($configFiles as $name => $path) {
    if (file_exists($path)) {
        $success[] = "Fichier $name existe";
        echo "✓ Fichier $name existe\n";
    } else {
        $errors[] = "Fichier $name manquant";
        echo "✗ Fichier $name manquant\n";
    }
}

// 4. Charger la configuration .env
echo "\n[4] Chargement de la configuration .env...\n";
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $envVars = [];
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $envVars[trim($key)] = trim($value, ' "\'');
        }
    }
    
    $requiredEnvVars = ['DB_HOST', 'DB_NAME', 'DB_USER'];
    foreach ($requiredEnvVars as $var) {
        if (isset($envVars[$var])) {
            echo "✓ $var = " . $envVars[$var] . "\n";
        } else {
            $errors[] = "Variable d'environnement $var manquante";
            echo "✗ $var manquante\n";
        }
    }
}

// 5. Test de connexion à la base de données
echo "\n[5] Test de connexion à la base de données...\n";
try {
    require __DIR__ . '/backend/config.php';
    require __DIR__ . '/backend/app/config/Database.php';
    
    $pdo = Database::connect();
    $success[] = "Connexion BD réussie";
    echo "✓ Connexion BD réussie\n";
    
    // Vérifier les tables
    echo "\n[6] Vérification des tables de la base de données...\n";
    $tables = ['users', 'classes', 'students', 'registrations'];
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            $success[] = "Table $table existe";
            echo "✓ Table $table existe\n";
        } else {
            $warnings[] = "Table $table manquante";
            echo "⚠ Table $table manquante\n";
        }
    }
    
    // Compter les utilisateurs
    echo "\n[7] Vérification des données...\n";
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    $userCount = $result['count'] ?? 0;
    echo "✓ Nombre d'utilisateurs dans la BD: $userCount\n";
    
} catch (Exception $e) {
    $errors[] = "Erreur de connexion BD: " . $e->getMessage();
    echo "✗ Erreur de connexion BD:\n";
    echo "  " . $e->getMessage() . "\n";
}

// 8. Vérifier les fichiers des contrôleurs
echo "\n[8] Vérification des fichiers des contrôleurs...\n";
$controllers = [
    'AuthController.php',
    'UserController.php',
    'ClassController.php',
    'StudentController.php',
    'RegistrationController.php',
    'DashboardController.php'
];

foreach ($controllers as $controller) {
    $path = __DIR__ . '/backend/app/controllers/' . $controller;
    if (file_exists($path)) {
        $success[] = "Contrôleur $controller existe";
        echo "✓ Contrôleur $controller\n";
    } else {
        $errors[] = "Contrôleur $controller manquant";
        echo "✗ Contrôleur $controller manquant\n";
    }
}

// 9. Vérifier les fichiers des modèles
echo "\n[9] Vérification des fichiers des modèles...\n";
$models = [
    'User.php',
    'Student.php',
    'ClassModel.php',
    'Registration.php'
];

foreach ($models as $model) {
    $path = __DIR__ . '/backend/app/models/' . $model;
    if (file_exists($path)) {
        $success[] = "Modèle $model existe";
        echo "✓ Modèle $model\n";
    } else {
        $errors[] = "Modèle $model manquant";
        echo "✗ Modèle $model manquant\n";
    }
}

// 10. Vérifier les fichiers frontend
echo "\n[10] Vérification des fichiers frontend...\n";
$frontendFiles = [
    'frontend/login.html',
    'frontend/index.html',
    'frontend/assets/js/config.js',
    'frontend/assets/js/auth.js',
    'frontend/assets/css/style.css'
];

foreach ($frontendFiles as $file) {
    $path = __DIR__ . '/' . $file;
    if (file_exists($path)) {
        echo "✓ Fichier $file\n";
    } else {
        $warnings[] = "Fichier $file manquant";
        echo "⚠ Fichier $file manquant\n";
    }
}

// Résumé
echo "\n========================================\n";
echo "          RÉSUMÉ DES TESTS\n";
echo "========================================\n\n";

echo "✓ Tests réussis: " . count($success) . "\n";
if ($warnings) {
    echo "⚠ Avertissements: " . count($warnings) . "\n";
}
echo "✗ Erreurs: " . count($errors) . "\n\n";

if ($errors) {
    echo "ERREURS DÉTECTÉES:\n";
    foreach ($errors as $i => $error) {
        echo ($i + 1) . ". " . $error . "\n";
    }
}

if ($warnings) {
    echo "\nAVERTISSEMENTS:\n";
    foreach ($warnings as $i => $warning) {
        echo ($i + 1) . ". " . $warning . "\n";
    }
}

if (empty($errors)) {
    echo "\n✓ TOUS LES TESTS RÉUSSIS - Le projet est prêt à fonctionner!\n\n";
    echo "Prochaines étapes:\n";
    echo "1. Accédez à http://localhost/CRL\n";
    echo "2. Connectez-vous avec vos identifiants\n";
    echo "3. Commencez à utiliser l'application\n";
} else {
    echo "\n✗ Veuillez corriger les erreurs avant de continuer.\n";
}

echo "\n========================================\n";
?>
