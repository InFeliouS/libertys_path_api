<?php
// src/dashboard/dashboard.php

// Ensure session is active (it’s started in public/index.php)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login if not authenticated
if (!isset($_SESSION['teacher_id'])) {
    header("Location: /login");
    exit;
}

// (Optional) Expose teacher username to the view
$teacher_username = $_SESSION['teacher_username'] ?? '';

// Include the dashboard HTML view from public/html
include __DIR__ . '/../../public/html/dashboard_view.html';
?>