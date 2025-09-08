<?php
// public/api/v1/sections.php
declare(strict_types=1);

// Always return JSON
header('Content-Type: application/json; charset=utf-8');

// Include DB (from /public/api/v1 to project root /src/config = go up 3 levels)
require __DIR__ . '/../../../src/config/db.php';

$response = ['success' => true, 'sections' => []];

try {
    // Prefer mysqli (your db.php defines $mysqli). Support PDO if you switch later.
    if (isset($mysqli) && $mysqli instanceof mysqli) {
        $sql = "SELECT id, section_name, start_school_year, end_school_year
                FROM sections
                ORDER BY start_school_year DESC, section_name ASC";
        $res = $mysqli->query($sql);

        if (!$res) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $mysqli->error], JSON_UNESCAPED_UNICODE);
            exit;
        }

        while ($row = $res->fetch_assoc()) {
            $row['id'] = (int)$row['id'];
            // Keep keys as expected by your JS
            $response['sections'][] = [
                'id'                 => $row['id'],
                'section_name'       => $row['section_name'],
                'start_school_year'  => (string)$row['start_school_year'],
                'end_school_year'    => (string)$row['end_school_year'],
            ];
        }

        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Optional PDO support (only if you enable $pdo in db.php)
    if (isset($pdo) && $pdo instanceof PDO) {
        $stmt = $pdo->query(
            "SELECT id, section_name, start_school_year, end_school_year
             FROM sections
             ORDER BY start_school_year DESC, section_name ASC"
        );
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
            $response['sections'][] = [
                'id'                 => (int)$row['id'],
                'section_name'       => $row['section_name'],
                'start_school_year'  => (string)$row['start_school_year'],
                'end_school_year'    => (string)$row['end_school_year'],
            ];
        }
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    // If neither connection exists:
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database connection not initialized.'], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
