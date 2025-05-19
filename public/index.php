<?php
// public/index.php

// load Composer, .env, your DB config, etc., if you need them here
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require __DIR__ . '/../vendor/autoload.php';
    if (class_exists('Dotenv\Dotenv') && file_exists(__DIR__ . '/../.env')) {
        Dotenv\Dotenv::createImmutable(__DIR__ . '/..')->load();
    }
}

// now hand off every request to src/routes.php
require __DIR__ . '/../src/routes.php';
