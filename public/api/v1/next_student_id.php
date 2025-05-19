<?php
// public/api/v1/next_student_id.php
header('Content-Type: application/json');
require __DIR__ . '/../../../src/config/db.php';

try {
    // Ask MySQL what the next AUTO_INCREMENT for `students` will be
    $stmt = $pdo->query("SHOW TABLE STATUS LIKE 'students'");
    $row  = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode([
      'success' => true,
      'next_id' => (int)$row['Auto_increment']
    ]);
} catch (PDOException $e) {
    echo json_encode([
      'success' => false,
      'error'   => $e->getMessage()
    ]);
}
