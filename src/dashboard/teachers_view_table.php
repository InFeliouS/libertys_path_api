<?php
// teachers_view_table.php
// Returns HTML table rows (<tr>...) for the teachers table.
// Safe, minimal, intended to be included in a server-rendered page's <tbody>
// (or fetched over AJAX and inserted as innerHTML).
// - Requires session login
// - Outputs only rows (no <table> wrapper)

declare(strict_types=1);

ini_set('display_errors', '0');
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

// Ensure correct content-type if fetched directly
header('Content-Type: text/html; charset=utf-8');

if (session_status() === PHP_SESSION_NONE)
    session_start();

// Require login
if (!isset($_SESSION['teacher_id'])) {
    // Output a single row indicating not logged in (so frontend can show it in the table)
    echo '<tr><td colspan="4" style="padding:18px;text-align:center;color:#a00;">Not logged in.</td></tr>';
    exit;
}

// Compute DB path (adjust if your project layout differs)
$dbPath = __DIR__ . '/../../src/config/db.php';
if (!is_file($dbPath)) {
    error_log("teachers_view_table: db.php not found at $dbPath");
    echo '<tr><td colspan="4" style="padding:18px;text-align:center;color:#a00;">Server misconfiguration.</td></tr>';
    exit;
}

try {
    require $dbPath; // expects $pdo (PDO) to be provided by db.php

    // Select only needed columns and order by id (you can change ordering if desired)
    // Only show users with role 'TEACHER' (case-insensitive)
    $sql = "SELECT id, first_name, last_name, username FROM teachers WHERE UPPER(role) = 'TEACHER' ORDER BY id ASC";
    $stmt = $pdo->query($sql);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$rows) {
        echo '<tr><td colspan="4" style="padding:18px;text-align:center;color:#666;">No teachers found.</td></tr>';
        exit;
    }

    // Emit rows. Column order (TDs): ID | First name | Last name | Username | Actions
    foreach ($rows as $r) {
        $id = isset($r['id']) ? (int) $r['id'] : 0;
        $first = $r['first_name'] ?? '';
        $last = $r['last_name'] ?? '';
        $username = $r['username'] ?? '';

        // safe JSON for data-json attribute (then escape)
        $teacherObj = [
            'id' => $id,
            'first_name' => $first,
            'last_name' => $last,
            'username' => $username
        ];
        $json = htmlspecialchars(json_encode($teacherObj, JSON_UNESCAPED_UNICODE), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        // escape cell text
        $escFirst = htmlspecialchars($first, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $escLast = htmlspecialchars($last, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $escUser = htmlspecialchars($username, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        echo '<tr data-id="' . $id . '" style="border-bottom:1px solid rgba(0,0,0,0.06);">';
        echo '<td class="td-first" style="padding:12px 10px;">' . $escFirst . '</td>';
        echo '<td class="td-last" style="padding:12px 10px;">' . $escLast . '</td>';
        echo '<td class="td-username" style="padding:12px 10px;">' . $escUser . '</td>';
        echo '<td class="tv-actions-col" style="padding:12px 10px;">';
        // Edit button includes data-json for JS; Delete button includes data-id
        echo '<button class="btn btn-edit" data-id="' . $id . '" data-json=\'' . $json . '\' style="margin-right:8px;">Edit</button>';
        echo '<button class="btn btn-delete" data-id="' . $id . '">Delete</button>';
        echo '</td>';
        echo '</tr>';
    }
    exit;
} catch (Throwable $e) {
    error_log('teachers_view_table error: ' . $e->getMessage());
    echo '<tr><td colspan="4" style="padding:18px;text-align:center;color:#a00;">Server error.</td></tr>';
    exit;
}
