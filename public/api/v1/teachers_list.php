<?php
// public/api/v1/teachers_list.php
// Returns a JSON list of teachers used to populate the dashboard dropdown.
// Response: { ok: true, data: [ { id, username, first_name, last_name }, ... ] }

ini_set('display_errors', '0'); // do NOT leak PHP warnings into JSON
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) session_start();

// Require login (keeps this endpoint protected)
if (!isset($_SESSION['teacher_id'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Not logged in']);
    exit;
}

// Defensive require: compute expected path and fail with JSON if missing
$dbPath = __DIR__ . '/../../../src/config/db.php';
if (!is_file($dbPath)) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => "Server misconfiguration: db.php not found at $dbPath"]);
    exit;
}

try {
    require_once $dbPath; // expects $pdo (PDO) to be defined by db.php

    $sql = "SELECT id, username, first_name, last_name
              FROM teachers
             WHERE role = 'TEACHER'
             ORDER BY COALESCE(last_name, '' ) ASC, COALESCE(first_name, '' ) ASC, username ASC";
    $stmt = $pdo->query($sql);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Normalize display fields (avoid nulls)
    $data = array_map(function($r){
        return [
            'id' => isset($r['id']) ? (int)$r['id'] : 0,
            'username' => $r['username'] ?? '',
            'first_name' => $r['first_name'] ?? '',
            'last_name' => $r['last_name'] ?? '',
        ];
    }, $rows ?: []);

    echo json_encode(['ok' => true, 'data' => $data], JSON_UNESCAPED_UNICODE);
    exit;
} catch (Throwable $e) {
    // Log server error somewhere if you have a logger (omitted here). Return safe JSON.
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Server error: ' . $e->getMessage()]);
    exit;
}
