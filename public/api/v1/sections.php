<?php
// public/api/v1/sections.php
header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors','1');

require_once __DIR__ . '/../../../src/config/db.php';

try {
    $stmt = $pdo->query("
        SELECT id, section_name, start_school_year, end_school_year
        FROM sections
        ORDER BY section_name
    ");
    $secs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode([
      'success'  => true,
      'sections' => $secs
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
      'success' => false,
      'message' => $e->getMessage()
    ]);
}
