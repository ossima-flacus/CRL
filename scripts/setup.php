#!/usr/bin/env php
<?php
/**
 * Script de configuration rapide du projet
 * Utilisation: php scripts/setup.php
 */

require_once __DIR__ . '/../backend/app/config/Database.php';

echo "╔════════════════════════════════════════════╗\n";
echo "║  CRL - Configuration Initiale             ║\n";
echo "╚════════════════════════════════════════════╝\n\n";

// Étape 1: Vérifier les prérequis
echo "🔍 Étape 1: Vérification des prérequis...\n";

$checks = [
    'PHP' => phpversion(),
    'MySQL PDO' => extension_loaded('pdo_mysql') ? '✓ Activé' : '✗ Non disponible',
    'JSON' => extension_loaded('json') ? '✓ Activé' : '✗ Non disponible',
    'File Permissions' => is_writable(__DIR__ . '/..') ? '✓ OK' : '✗ Non accessible',
];

$allGood = true;
foreach ($checks as $check => $status) {
    $icon = is_string($status) && strpos($status, '✓') !== false ? '✅' : (strpos($status, '✗') !== false ? '❌' : '✅');
    echo "   $icon $check: $status\n";
    if (strpos($status, '✗') !== false) $allGood = false;
}

if (!$allGood) {
    echo "\n⚠️  Certaines dépendances sont manquantes.\n";
    exit(1);
}

// Étape 2: Vérifier/créer .env
echo "\n📝 Étape 2: Configuration .env...\n";

$envFile = __DIR__ . '/../.env';
$envExampleFile = __DIR__ . '/../.env.example';

if (!file_exists($envFile)) {
    if (file_exists($envExampleFile)) {
        copy($envExampleFile, $envFile);
        echo "   ✅ Fichier .env créé depuis .env.example\n";
    } else {
        echo "   ⚠️  Impossible de créer .env\n";
    }
} else {
    echo "   ✅ Fichier .env existe déjà\n";
}

// Étape 3: Initialiser la base de données
echo "\n💾 Étape 3: Initialisation de la base de données...\n";

$initScript = __DIR__ . '/init-db.php';
if (file_exists($initScript)) {
    echo "   Exécution de init-db.php...\n";
    shell_exec("php " . escapeshellarg($initScript));
} else {
    echo "   ⚠️  init-db.php non trouvé\n";
}

// Étape 4: Résumé
echo "\n╔════════════════════════════════════════════╗\n";
echo "║  ✅ Configuration Terminée!               ║\n";
echo "╚════════════════════════════════════════════╝\n\n";

echo "📋 Prochaines étapes:\n";
echo "   1. Vérifier MySQL: php scripts/test-db.php\n";
echo "   2. Configurer Apache (mod_rewrite activé)\n";
echo "   3. Ajouter à httpd.conf:\n";
echo "      <Directory \"/xampp/htdocs/CRL/backend/public\">\n";
echo "          AllowOverride All\n";
echo "      </Directory>\n";
echo "   4. Redémarrer Apache\n";
echo "   5. Visiter: http://localhost/CRL/frontend/login.html\n";
echo "   6. Se connecter: admin / Admin@123\n\n";
