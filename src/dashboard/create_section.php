<?php
// src/dashboard/create_section.php

// Start session if needed
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Protect route
if (!isset($_SESSION['teacher_id'])) {
    header("Location: /login");
    exit;
}

// DB connection
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sectionName  = trim($_POST['section_name']      ?? '');
    $startYear    = trim($_POST['start_school_year'] ?? '');
    $endYear      = trim($_POST['end_school_year']   ?? '');

    // Basic validation
    if (
        $sectionName === '' ||
        !preg_match('/^\d{4}$/', $startYear) ||
        !preg_match('/^\d{4}$/', $endYear)
    ) {
        // If invalid, reload form (you could pass an error here)
        header("Location: /sections/create");
        exit;
    }

    // Insert new section
    $stmt = $pdo->prepare("
        INSERT INTO sections
          (section_name, start_school_year, end_school_year)
        VALUES
          (:section, :start, :end)
    ");
    $stmt->execute([
        ':section' => $sectionName,
        ':start'   => $startYear,
        ':end'     => $endYear,
    ]);

    // On success, go back to dashboard
    header("Location: /dashboard");
    exit;
}

// GET → show the form
include __DIR__ . '/../../public/html/create_section.html';
