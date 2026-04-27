<?php
// Charger les variables d'environnement du fichier .env
if (!function_exists('loadEnv')) {
    function loadEnv($filePath) {
        if (!file_exists($filePath)) {
            return;
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '=') === false || strpos($line, '#') === 0) {
                continue;
            }

            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value, ' "\'');

            if (!empty($key)) {
                $_ENV[$key] = $value;
            }
        }
    }
}

loadEnv(__DIR__ . '/../.env');

return [
    'host' => $_ENV['DB_HOST'] ?? '127.0.0.1',
    'dbname' => $_ENV['DB_NAME'] ?? 'crl_db',
    'user' => $_ENV['DB_USER'] ?? 'root',
    'pass' => $_ENV['DB_PASSWORD'] ?? '',
];
