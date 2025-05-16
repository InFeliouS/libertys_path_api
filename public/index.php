<?php
// public/index.php – Front controller

// Start the session so controllers can read/write $_SESSION
session_start();

// Show errors in development (disable in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Autoload Composer dependencies (if any)
$autoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoload)) {
    require $autoload;
}

// Load environment variables
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile) && class_exists('Dotenv\Dotenv')) {
    $dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
    $dotenv->load();
}

// Dispatch routes
require __DIR__ . '/../src/routes.php';
