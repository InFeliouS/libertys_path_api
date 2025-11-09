<?php
/**
 * Section Detail (safe include)
 * - Validates section_id
 * - Loads section row (for existence check and to get section_name)
 * - Reads the static HTML and replaces the placeholder "**name nung section**"
 *   with the escaped real section name, then echoes the result.
 */

declare(strict_types=1);

// DB connection
require_once __DIR__ . '/../config/db.php'; // provides $pdo (PDO)

// get and validate section_id (accept both section_id and id param for flexibility)
$sectionId = 0;
if (isset($_GET['section_id'])) {
    $sectionId = (int)$_GET['section_id'];
} elseif (isset($_GET['id'])) {
    $sectionId = (int)$_GET['id'];
}
if ($sectionId <= 0) {
    http_response_code(400);
    echo "Missing or invalid section_id.";
    exit;
}

$sectionName = 'Unnamed Section';
try {
    $stmt = $pdo->prepare("SELECT id, section_name FROM sections WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $sectionId]);
    $section = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$section) {
        http_response_code(404);
        echo "Section not found.";
        exit;
    }

    $sectionName = trim((string)($section['section_name'] ?? ''));
    if ($sectionName === '') $sectionName = 'Unnamed Section';
} catch (Throwable $e) {
    http_response_code(500);
    // avoid leaking DB details in production; log instead
    error_log('section_detail error: ' . $e->getMessage());
    echo "Database error.";
    exit;
}

// Path to the static HTML shell
$sectionDetailHtml = __DIR__ . '/../../public/html/section_detail.html';
if (!is_file($sectionDetailHtml) || !is_readable($sectionDetailHtml)) {
    http_response_code(500);
    echo "Section detail HTML not found or unreadable.";
    exit;
}

// Read the HTML as plain text and replace the exact placeholder string.
// NOTE: the placeholder must appear exactly as "**name nung section**" in the HTML.
$html = file_get_contents($sectionDetailHtml);
if ($html === false) {
    http_response_code(500);
    echo "Failed to load section detail HTML.";
    exit;
}

// Escape the section name for HTML insertion
$escapedName = htmlspecialchars($sectionName, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

// Replace placeholder. If the placeholder isn't found, we still echo the file unchanged.
$htmlReplaced = str_replace('**name nung section**', $escapedName, $html);

// Optionally expose sectionId and sectionName to client JS (uncomment if you want)
// $injection = "<script>window.SECTION_DETAIL = { id: " . (int)$sectionId . ", name: " . json_encode($sectionName, JSON_UNESCAPED_UNICODE) . " };</script>";
// insert after opening <head> if you want; careful with placement. For now we skip automatic injection.

// Output the transformed HTML
echo $htmlReplaced;
exit;
