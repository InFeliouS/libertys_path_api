<?php
// src/config/db.php

// Load Composer autoloader
require_once __DIR__ . '/../../vendor/autoload.php';

// Load environment variables
if (class_exists('Dotenv\Dotenv') && file_exists(__DIR__ . '/../../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
    $dotenv->load();
}

$host     = $_ENV['DB_HOST'] ?? '127.0.0.1';
$dbname   = $_ENV['DB_NAME'] ?? 'database';
$username = $_ENV['DB_USER'] ?? 'root';
$password = $_ENV['DB_PASS'] ?? '';

try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
        $username,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    // In production you might log this instead of outputting
    die(json_encode(['error' => 'Connection failed: ' . $e->getMessage()]));
}
