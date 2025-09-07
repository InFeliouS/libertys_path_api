<?php
// src/dashboard/section_detail.php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['teacher_id'])) { header('Location: index.php?route=login'); exit; }

$sectionId = (int)($_GET['id'] ?? 0);
if ($sectionId <= 0) { http_response_code(400); echo "Missing section ID."; exit; }

require_once __DIR__ . '/../config/db.php';
if (!isset($mysqli)) { http_response_code(500); echo "Database connection not initialized."; exit; }

/* Section info */
$stmt = $mysqli->prepare("SELECT id, section_name, start_school_year, end_school_year
                          FROM sections WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $sectionId);
$stmt->execute();
$section = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$section) { http_response_code(404); echo "Section not found."; exit; }

/* Students in this section */
$stmt = $mysqli->prepare("SELECT id, given_name, middle_name, last_name, birth_sex
                          FROM students
                          WHERE section_id = ?
                          ORDER BY last_name, given_name, middle_name");
$stmt->bind_param("i", $sectionId);
$stmt->execute();
$res = $stmt->get_result();
$students = [];
while ($row = $res->fetch_assoc()) {
    $full = trim($row['given_name'].' '.($row['middle_name'] ? $row['middle_name'].' ' : '').$row['last_name']);
    $students[] = [
        'id'        => (int)$row['id'],
        'full_name' => $full,
        'birth_sex' => $row['birth_sex'],
    ];
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Section Detail</title>
  <link rel="stylesheet" href="css/section_detail.css">
</head>
<body>
  <h1><?= htmlspecialchars($section['section_name']) ?></h1>
  <p>School Year: <?= htmlspecialchars($section['start_school_year']) ?> - <?= htmlspecialchars($section['end_school_year']) ?></p>

  <h2>Students</h2>
  <?php if (!$students): ?>
    <p>No students in this section yet.</p>
  <?php else: ?>
    <table>
      <thead><tr><th>Name</th><th>Sex</th></tr></thead>
      <tbody>
      <?php foreach ($students as $s): ?>
        <tr>
          <td><?= htmlspecialchars($s['full_name']) ?></td>
          <td><?= htmlspecialchars($s['birth_sex']) ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>

  <p><a href="index.php?route=dashboard">Back to Dashboard</a></p>
</body>
</html>
