<?php
// public/api/v1/student_info.php

header('Content-Type: application/json');
session_start();

// Allow CORS if needed (remove or tighten in production)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// read JSON body
$in = json_decode(file_get_contents('php://input'), true);
$student_id = isset($in['student_id']) ? (int)$in['student_id'] : 0;
if (!$student_id) {
    http_response_code(400);
    echo json_encode(['success'=>false,'error'=>'Missing student_id']);
    exit;
}

// include your database connection
// ↑ three levels up from public/api/v1 → into src/config/db.php
require_once __DIR__ . '/../../../src/config/db.php';

try {
    // fetch student + account + progress + section
    $stmt = $pdo->prepare("
      SELECT 
        sa.username,
        st.given_name,     st.middle_name,   st.last_name,
        st.birth_sex,
        se.section_name,
        sp.first_qpr_status,   sp.first_qpr_retries,
        sp.second_qpr_status,  sp.second_qpr_retries,
        sp.third_qpr_status,   sp.third_qpr_retries,
        sp.fourth_qpr_status,  sp.fourth_qpr_retries
      FROM students st
      JOIN sections se           ON se.id          = st.section_id
      JOIN student_accounts sa   ON sa.student_id  = st.id
      JOIN student_progress sp   ON sp.student_id  = st.id
      WHERE st.id = ?
      LIMIT 1
    ");
    $stmt->execute([$student_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
      http_response_code(404);
      echo json_encode(['success'=>false,'error'=>'Student not found']);
      exit;
    }

    // return everything (now including section_name)
    echo json_encode([
      'success' => true,
      'data'    => $row
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
}
