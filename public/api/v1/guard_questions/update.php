<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../../../src/config/db.php';

$input = $_POST;
if (empty($input)) {
    $raw = file_get_contents('php://input');
    if ($raw) $input = json_decode($raw, true) ?? [];
}

$id = isset($input['id']) ? (int)$input['id'] : 0;
if ($id <= 0) {
    http_response_code(422);
    echo json_encode(['success'=>false,'error'=>'Missing id']);
    exit;
}

$required = ['question_text','choice1','choice2','choice3','choice4','correct_index'];
foreach ($required as $k) {
    if (!isset($input[$k]) || $input[$k] === '') {
        http_response_code(422);
        echo json_encode(['success'=>false,'error'=>"Missing $k"]);
        exit;
    }
}

$ci = (int)$input['correct_index'];
if ($ci < 0 || $ci > 3) {
    http_response_code(422);
    echo json_encode(['success'=>false,'error'=>'correct_index must be 0..3']);
    exit;
}

try {
    $sql = "UPDATE guard_questions SET
            question_text=:qt, choice1=:c1, choice2=:c2, choice3=:c3, choice4=:c4, correct_index=:ci
            WHERE id=:id";
    $stmt = $pdo->prepare($sql);
    $ok = $stmt->execute([
        ':qt' => $input['question_text'],
        ':c1' => $input['choice1'],
        ':c2' => $input['choice2'],
        ':c3' => $input['choice3'],
        ':c4' => $input['choice4'],
        ':ci' => $ci,
        ':id' => $id
    ]);
    echo json_encode(['success'=>$ok]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>'DB error']);
}
