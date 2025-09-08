<?php
// src/config/db.php

// Adjust username/password if your MySQL differs
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';  // default XAMPP MySQL root has no password
$DB_NAME = 'libertys_path_db';

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_errno) {
    die('DB connection failed: ' . $mysqli->connect_error);
}
$mysqli->set_charset('utf8mb4');

// Optional PDO (not required if mysqli is enough)
// $pdo = new PDO(
//     "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
//     $DB_USER,
//     $DB_PASS,
//     [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
// );
