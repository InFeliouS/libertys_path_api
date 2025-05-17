<?php
// src/dashboard/dashboard.php

// Only start a session if one isn't already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login if not authenticated
if (!isset($_SESSION['teacher_id'])) {
    header("Location: /login");
    exit;
}

// We’ll let the JS fetch & render your section cards,
// so we just serve the static HTML now:
include __DIR__ . '/../../public/html/dashboard_view.html';
