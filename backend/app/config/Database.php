<?php

class Database
{
    public static function connect(): PDO
    {
        $config = require __DIR__ . '/../../config.php';

        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $config['host'], $config['dbname']);
        $pdo = new PDO($dsn, $config['user'], $config['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        return $pdo;
    }
}
