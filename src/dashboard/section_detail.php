<?php
/**
 * Section Detail (QPR-free)
 * - Validates section_id
 * - Loads section row (for existence check and potential future use)
 * - Includes the frontend HTML; all data tables are fetched via public APIs
 */

declare(strict_types=1);

// DB connection
require_once __DIR__ . '/../config/db.php'; // provides $pdo (PDO)

$sectionId = isset($_GET['section_id']) ? (int)$_GET['section_id'] : 0;
if ($sectionId <= 0) {
    http_response_code(400);
    echo "Missing or invalid section_id.";
    exit;
}

// Make sure the section exists (and get name if you want to show it elsewhere)
try {
    $stmt = $pdo->prepare("SELECT id, section_name FROM sections WHERE id = :id");
    $stmt->execute([':id' => $sectionId]);
    $section = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$section) {
        http_response_code(404);
        echo "Section not found.";
        exit;
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo "Database error: " . htmlspecialchars($e->getMessage());
    exit;
}

/**
 * NOTE:
 * We intentionally removed all queries to legacy QPR tables
 * (student_progress, *_qpr_* tables, etc.). The roster and leaderboard
 * are now loaded by the page’s JS via:
 *   - public/api/v1/sections_students.php
 *   - public/api/v1/leaderboard/team_top_by_section.php
 * Keep this file as a thin controller that just includes the HTML shell.
 */

// If you ever want the section title in the H1, you can inject it by
// appending a query param, e.g. ?section_name=... in the URL.
// For now, we just include the static HTML file.
$sectionDetailHtml = __DIR__ . '/../../public/html/section_detail.html';

if (!is_file($sectionDetailHtml)) {
    http_response_code(500);
    echo "Section detail HTML not found at: " . htmlspecialchars($sectionDetailHtml);
    exit;
}

// Include the static HTML shell; JS will render roster/leaderboard.
require $sectionDetailHtml;
